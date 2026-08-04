<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class PasswordResetController extends Controller
{
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
            'email'      => $request->email,
            'token'      => Hash::make($code),
            'created_at' => now(),
        ]);

        // Kirim kode via email
        Mail::raw(
            "Kode verifikasi untuk reset password Anda adalah: {$code}\n\nKode ini berlaku selama 15 menit.\n\nJika Anda tidak meminta reset password, abaikan email ini.",
            function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Kode Reset Password - ' . config('app.name'));
            }
        );

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
            'email'    => 'required|email',
            'code'     => 'required|string|size:6',
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
}
