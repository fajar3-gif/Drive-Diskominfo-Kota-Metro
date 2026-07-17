<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login - Manajemen File</title>
    <style>
        body { font-family: sans-serif; background-color: #f3f4f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .input-group { margin-bottom: 15px; }
        input { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #0f172a; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
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
            @csrf <!-- Wajib ada untuk keamanan form Laravel -->
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
        <p style="text-align: center; font-size: 14px; margin-top: 15px;">Belum punya akun? <a href="#">Daftar di sini</a></p>
    </div>
</body>
</html>