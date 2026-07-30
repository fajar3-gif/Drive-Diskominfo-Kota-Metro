<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Manajemen File</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            width: 100%;
            max-width: 400px;
            padding: 45px 38px;
            background: #ffffff;
            /* Border radius kecil */
            border-radius: 6px;
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
            margin-bottom: 8px;
            text-align: center;
        }

        .card .subtitle {
            font-size: 13px;
            color: #6b7280;
            text-align: center;
            margin-bottom: 28px;
        }

        .error {
            color: #dc2626;
            font-size: 13px;
            margin-bottom: 16px;
            padding: 10px 14px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 4px;
        }

        .error ul {
            margin: 0;
            padding-left: 16px;
        }

        .error ul li {
            margin-bottom: 2px;
        }

        .error ul li:last-child {
            margin-bottom: 0;
        }

        .input-group {
            margin-bottom: 18px;
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
            padding: 12px 16px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: #1f2937;
            background-color: #f3f4f6;
            border: 1.5px solid #e5e7eb;
            /* Border radius kecil */
            border-radius: 4px;
            outline: none;
            transition: all 0.25s ease;
            /* Efek timbul pada input */
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.04);
        }

        .input-group input::placeholder {
            color: #9ca3af;
        }

        .input-group input:focus {
            border-color: #2563eb;
            background-color: #ffffff;
            box-shadow:
                0 0 0 3px rgba(37, 99, 235, 0.15),
                inset 0 1px 2px rgba(0, 0, 0, 0.02);
        }

        .btn-register {
            width: 100%;
            padding: 13px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
            background: #2563eb;
            border: none;
            /* Border radius disesuaikan */
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
            /* Efek timbul pada tombol */
            box-shadow: none;
        }

        .btn-register:hover {
            background: #1e40af;
            box-shadow: none;
        }

        .btn-register:active {
            transform: translateY(0);
            background: #1e40af;
            box-shadow: none;
        }

        /* Link login */
        .login-link {
            text-align: center;
            font-size: 13px;
            color: #6b7280;
            margin-top: 22px;
        }

        .login-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .login-link a:hover {
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
            <h2>Buat Akun Baru</h2>
            <p class="subtitle">Daftarkan akun untuk mulai mengelola file</p>

            @if($errors->any())
                <div class="error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="input-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                </div>
                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email" required>
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </div>
                <div class="input-group">
                    <label>Ulangi Password</label>
                    <input type="password" name="password_confirmation" placeholder="Konfirmasi password" required>
                </div>
                <button type="submit" class="btn-register">Daftar</button>
            </form>

            <p class="login-link">Sudah punya akun? <a href="{{ route('login') }}">Log in</a></p>
        </div>
    </div>
</body>
</html>