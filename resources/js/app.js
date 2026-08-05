import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// ============================================================
// FIREBASE INITIALIZATION
// ============================================================
import { initializeApp } from "firebase/app";
import { getAuth, GoogleAuthProvider, signInWithPopup } from "firebase/auth";

// Firebase config dibaca dari window.__firebaseConfig yang diinjeksikan server (login.blade.php)
// Nilai aslinya ada di .env — tidak pernah ada di file JS yang ter-commit ke git
const firebaseConfig = window.__firebaseConfig;

// Initialize Firebase
const firebaseApp = initializeApp(firebaseConfig);
const auth = getAuth(firebaseApp);
const provider = new GoogleAuthProvider();

// Selalu tampilkan popup pilih akun Google (tidak auto-login)
provider.setCustomParameters({ prompt: 'select_account' });

// ============================================================
// GOOGLE LOGIN VIA FIREBASE POPUP
// ============================================================
window.loginWithGoogle = async function () {
    const btn = document.getElementById('btn-google-firebase');
    const errorEl = document.getElementById('firebase-error');

    // Reset error message
    if (errorEl) errorEl.textContent = '';

    // Tampilkan loading state pada tombol
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="google-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Menghubungkan...
        `;
    }

    try {
        // Buka popup Google via Firebase
        const result = await signInWithPopup(auth, provider);

        // Ambil ID Token dari Firebase
        const idToken = await result.user.getIdToken();

        // Kirim ID Token ke backend Laravel untuk diverifikasi & login
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const response = await fetch('/auth/firebase', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ idToken }),
        });

        const data = await response.json();

        if (data.success) {
            // Login berhasil — redirect ke dashboard
            window.location.href = data.redirect || '/dashboard';
        } else {
            throw new Error(data.message || 'Login gagal dari server.');
        }

    } catch (error) {
        // Kembalikan tombol ke semula
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = `
                <svg class="google-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Lanjutkan dengan Google
            `;
        }

        // Tampilkan pesan error yang relevan
        let pesan = 'Gagal login dengan Google. Silakan coba lagi.';
        if (error.code === 'auth/popup-closed-by-user') {
            pesan = 'Popup login ditutup. Silakan coba lagi.';
        } else if (error.code === 'auth/popup-blocked') {
            pesan = 'Popup diblokir browser. Izinkan popup dari situs ini.';
        } else if (error.code === 'auth/cancelled-popup-request') {
            pesan = ''; // Abaikan — hanya klik ganda
        } else if (error.message) {
            pesan = error.message;
        }

        if (pesan && errorEl) {
            errorEl.textContent = pesan;
        }

        console.error('Firebase login error:', error);
    }
};
