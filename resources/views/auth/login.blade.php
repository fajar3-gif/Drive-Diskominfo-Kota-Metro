<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login - Manajemen File</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { width: 100%; max-width: 400px; padding: 20px; box-sizing: border-box; }
        .input-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 15px; cursor: pointer; }
        .error { color: red; font-size: 13px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="card">
        <h2 style="text-align: center;">Masuk ke Akun</h2>
        
        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Log In</button>
        </form>
        <p style="text-align: center; font-size: 14px; margin-top: 15px;">Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p>
    </div>
</body>
</html>