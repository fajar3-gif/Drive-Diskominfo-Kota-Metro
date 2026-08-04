<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SocialAuthController extends Controller
{
    /**
     * Menerima idToken dari Firebase JS SDK, memverifikasinya ke Firebase REST API,
     * lalu membuat sesi login Laravel untuk user yang bersangkutan.
     */
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
