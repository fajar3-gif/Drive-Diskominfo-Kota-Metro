<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Manajemen File</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    {{-- Firebase config diinjeksikan dari server (.env) — tidak ada key di file JS --}}
    <script>
        window.__firebaseConfig = {
            apiKey:            "{{ config('firebase.api_key') }}",
            authDomain:        "{{ config('firebase.auth_domain') }}",
            projectId:         "{{ config('firebase.project_id') }}",
            storageBucket:     "{{ config('firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('firebase.messaging_sender_id') }}",
            appId:             "{{ config('firebase.app_id') }}",
            measurementId:     "{{ config('firebase.measurement_id') }}",
        };
    </script>
    @vite('resources/js/app.js')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            /* Background solid blue */
            background: #1b5c96;
            position: relative;
            overflow: hidden;
        }

        /* Efek dekorasi latar belakang */
        body::before {
            content: '';
            position: absolute;
            top: -150px;
            right: -100px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: absolute;
            bottom: -180px;
            left: -80px;
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(0, 0, 0, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        /* Container luar — efek timbul kuat */
        .login-wrapper {
            position: relative;
            z-index: 1;
        }

        .card {
            width: 350px;
            max-width: calc(100vw - 32px);
            padding: 32px 30px 16px;
            background: #ffffff;
            border-radius: 0;
            /* Shadow kuat untuk efek timbul seperti referensi */
            box-shadow:
                0 25px 60px rgba(0, 0, 0, 0.25),
                0 10px 25px rgba(0, 0, 0, 0.15),
                0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .card h2 {
            font-size: 22px;
            font-weight: 700;
            color: #1e3a5f;
            margin-bottom: 24px;
            text-align: center;
        }

        .error-wrapper:empty {
            display: none;
        }

        .error {
            color: #dc2626;
            font-size: 13px;
            margin-top: -4px;
            margin-bottom: 8px;
        }

        .input-group {
            margin-bottom: 12px;
        }

        .input-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }

        .input-group input {
            width: 100%;
            height: 52px;
            padding: 0 16px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: #1f2937;
            background-color: #f0f2f5;
            border: none;
            border-radius: 0;
            outline: none;
            transition: all 0.25s ease;
            box-sizing: border-box;
            box-shadow: none;
        }

        .input-group input::placeholder {
            color: #9ca3af;
        }

        /* Override browser autofill background */
        .input-group input:-webkit-autofill,
        .input-group input:-webkit-autofill:hover, 
        .input-group input:-webkit-autofill:focus, 
        .input-group input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #f0f2f5 inset !important;
            -webkit-text-fill-color: #1f2937 !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        .btn-login {
            width: 100%;
            height: 40px;
            padding: 0;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
            background: #2563eb;
            border: none;
            border-radius: 0;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
            box-shadow: none;
        }

        .btn-login:hover {
            background: #1e40af;
            box-shadow: none;
        }

        .btn-login:active {
            transform: translateY(0);
            background: #1e40af;
            box-shadow: none;
        }

        /* Pembatas ATAU */
        .divider {
            display: flex;
            align-items: center;
            margin: 8px 0;
        }

        .divider hr {
            flex-grow: 1;
            border: none;
            border-top: 1px solid #e5e7eb;
        }

        .divider span {
            padding: 0 14px;
            font-size: 11px;
            font-weight: 600;
            color: #9ca3af;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        /* Tombol Google */
        .btn-google {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 40px;
            padding: 0;
            background-color: #ffffff;
            color: #374151;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 600;
            border: 1.5px solid #e5e7eb;
            border-radius: 0;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.25s ease;
            box-shadow: none;
        }

        .btn-google:hover {
            background-color: #f3f4f6;
            border-color: #d1d5db;
            box-shadow: none;
        }

        .btn-google:active {
            transform: translateY(0);
            box-shadow: none;
        }

        /* Google Icon SVG */
        .google-icon {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            flex-shrink: 0;
        }

        /* Link daftar */
        .register-link {
            text-align: center;
            font-size: 13px;
            color: #6b7280;
            margin-top: 12px;
        }

        .register-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .register-link a:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-wrapper {
                margin: 16px;
            }
            .card {
                padding: 30px 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="card">
            <h2>Masuk ke Akun</h2>

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email" required>
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </div>
                
                <div class="error-wrapper">
                    @if(session('status'))
                        <div style="color: #059669; font-size: 13px;">{{ session('status') }}</div>
                    @elseif($errors->any())
                        <div class="error">{{ $errors->first() }}</div>
                    @endif
                </div>

                <div style="text-align: right; margin-bottom: 18px; margin-top: 0;">
                    <a href="{{ route('password.request') }}" style="font-size: 12px; color: #2563eb; text-decoration: none; font-weight: 500; transition: color 0.2s;"
                       onmouseover="this.style.color='#1d4ed8'; this.style.textDecoration='underline'"
                       onmouseout="this.style.color='#2563eb'; this.style.textDecoration='none'">Lupa Password?</a>
                </div>
                <button type="submit" class="btn-login">Log In</button>
            </form>

            <!-- Pembatas ATAU -->
            <div class="divider">
                <hr>
                <span>ATAU</span>
                <hr>
            </div>

            <!-- Tombol Google (Firebase Auth) -->
            <div id="firebase-error" style="color: #dc2626; font-size: 13px; margin-bottom: 0;"></div>
            <button id="btn-google-firebase" type="button" class="btn-google" onclick="loginWithGoogle()">
                <svg class="google-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Lanjutkan dengan Google
            </button>

            <p class="register-link">Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p>
        </div>
    </div>
</body>
</html>