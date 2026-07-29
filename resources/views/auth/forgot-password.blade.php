<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Manajemen File</title>
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
            background: linear-gradient(135deg, #4a7c8a 0%, #5a8f9e 20%, #7baab5 40%, #a8c8cf 60%, #6a9aaa 80%, #4a7080 100%);
            position: relative;
            overflow: hidden;
        }

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

        .login-wrapper {
            position: relative;
            z-index: 1;
        }

        .card {
            width: 100%;
            max-width: 400px;
            padding: 45px 38px;
            background: #ffffff;
            border-radius: 6px;
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
            line-height: 1.5;
        }

        .success {
            color: #059669;
            font-size: 13px;
            margin-bottom: 16px;
            padding: 10px 14px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 4px;
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

        .input-group {
            margin-bottom: 20px;
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
            border-radius: 4px;
            outline: none;
            transition: all 0.25s ease;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.04);
        }

        .input-group input::placeholder {
            color: #9ca3af;
        }

        .input-group input:focus {
            border-color: #4a7c8a;
            background-color: #ffffff;
            box-shadow:
                0 0 0 3px rgba(74, 124, 138, 0.15),
                inset 0 1px 2px rgba(0, 0, 0, 0.02);
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
            background: #4a7c8a;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
            box-shadow:
                0 4px 12px rgba(74, 124, 138, 0.4),
                0 1px 3px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }

        .btn-submit:hover {
            background: #3d6b78;
            box-shadow:
                0 6px 18px rgba(74, 124, 138, 0.5),
                0 2px 6px rgba(0, 0, 0, 0.12),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
            background: #355e6a;
            box-shadow:
                0 2px 6px rgba(74, 124, 138, 0.3),
                inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .back-link {
            text-align: center;
            font-size: 13px;
            color: #6b7280;
            margin-top: 22px;
        }

        .back-link a {
            color: #4a7c8a;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .back-link a:hover {
            color: #3d6b78;
            text-decoration: underline;
        }

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
            <h2>Lupa Password</h2>
            <p class="subtitle">Masukkan alamat email akun Anda. Kami akan mengirimkan kode verifikasi untuk mereset kata sandi.</p>

            @if(session('status'))
                <div class="success">{{ session('status') }}</div>
            @endif

            @if($errors->any())
                <div class="error">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email Anda" required>
                </div>
                <button type="submit" class="btn-submit">Kirim Kode Verifikasi</button>
            </form>

            <p class="back-link"><a href="{{ route('login') }}">← Kembali ke Login</a></p>
        </div>
    </div>
</body>
</html>
