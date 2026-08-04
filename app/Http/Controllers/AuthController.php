<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }
    public function showRegister()
    {
        return view('auth.register'); 
    }
    public function storeRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6', 
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        Auth::login($user);
        return redirect('/dashboard');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard'); 
        }
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // --- FITUR LUPA PASSWORD ---

    // Menampilkan form lupa password
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // Mengirim kode verifikasi ke email
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Cek apakah email terdaftar
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak terdaftar di sistem kami.'])->onlyInput('email');
        }

        // Generate kode 6 digit
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Simpan kode ke tabel password_reset_tokens
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($code),
            'created_at' => now(),
        ]);

        // Kirim kode via email
        Mail::raw("Kode verifikasi untuk reset password Anda adalah: {$code}\n\nKode ini berlaku selama 15 menit.\n\nJika Anda tidak meminta reset password, abaikan email ini.", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Kode Reset Password - ' . config('app.name'));
        });

        // Redirect ke form reset password
        return redirect()->route('password.reset', ['email' => $request->email])
                         ->with('status', 'Kode verifikasi telah dikirim ke email Anda.');
    }

    // Menampilkan form reset password (input kode + password baru)
    public function showResetForm(Request $request)
    {
        return view('auth.reset-password', ['email' => $request->query('email', '')]);
    }

    // Memproses reset password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Ambil record dari tabel password_reset_tokens
        $record = DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->first();

        if (!$record) {
            return back()->withErrors(['code' => 'Kode verifikasi tidak valid atau sudah kadaluarsa.']);
        }

        // Cek apakah kode sudah kadaluarsa (15 menit)
        if (now()->diffInMinutes($record->created_at) > 15) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['code' => 'Kode verifikasi sudah kadaluarsa. Silakan minta kode baru.']);
        }

        // Verifikasi kode
        if (!Hash::check($request->code, $record->token)) {
            return back()->withErrors(['code' => 'Kode verifikasi yang Anda masukkan salah.']);
        }

        // Update password user
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Hapus token reset
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Redirect ke login dengan pesan sukses
        return redirect()->route('login')->with('status', 'Password berhasil direset! Silakan login dengan password baru Anda.');
    }

    // --- FITUR LOGIN GOOGLE VIA FIREBASE AUTH ---

    // Menerima idToken dari Firebase JS SDK, memverifikasinya ke Firebase REST API,
    // lalu membuat sesi login Laravel untuk user yang bersangkutan.
    public function handleFirebaseLogin(Request $request)
    {
        $request->validate([
            'idToken' => 'required|string',
        ]);

        try {
            // Firebase Web API Key (public key, aman dipakai di server)
            $firebaseApiKey = 'AIzaSyDPhmpfpI6okjVtiPf7hQlbWeKbJV4W8UA';

            // Verifikasi idToken ke Firebase Identity Toolkit REST API
            $response = Http::post(
                "https://identitytoolkit.googleapis.com/v1/accounts:lookup?key={$firebaseApiKey}",
                ['idToken' => $request->idToken]
            );

            if ($response->failed() || empty($response->json('users'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token Firebase tidak valid. Silakan coba login lagi.',
                ], 401);
            }

            $firebaseUser = $response->json('users')[0];
            $email        = $firebaseUser['email'] ?? null;
            $name         = $firebaseUser['displayName'] ?? ($email ? explode('@', $email)[0] : 'User');
            $avatar       = $firebaseUser['photoUrl'] ?? null;
            $firebaseUid  = $firebaseUser['localId']; // Firebase unique user ID

            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Google Anda tidak memiliki email. Silakan gunakan akun lain.',
                ], 422);
            }

            // Cari atau buat user di database Laravel
            $user = User::where('email', $email)->first();

            if ($user) {
                // Update data Google terbaru
                $user->update([
                    'google_id' => $firebaseUid,
                    'avatar'    => $avatar,
                ]);
            } else {
                // Daftarkan user baru otomatis
                $user = User::create([
                    'name'      => $name,
                    'email'     => $email,
                    'google_id' => $firebaseUid,
                    'avatar'    => $avatar,
                    'password'  => Hash::make(Str::random(24)),
                ]);
            }

            // Login-kan user ke sistem Laravel
            Auth::login($user);
            $request->session()->regenerate();

            return response()->json([
                'success'  => true,
                'redirect' => '/dashboard',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server. Silakan coba lagi.',
            ], 500);
        }
    }
}