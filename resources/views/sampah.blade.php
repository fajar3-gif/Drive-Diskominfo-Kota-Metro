<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tempat Sampah</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; display: flex; height: 100vh; background-color: #ffffff; color: #1f1f1f; }
        .sidebar { width: 250px; background: #f1f3f4; padding: 20px; display: flex; flex-direction: column; border-right: 2px solid #ccc; }
        .logo-title { margin-top: 0; font-size: 22px; margin-bottom: 30px; }
        .main-content { flex: 1; padding: 30px; overflow-y: auto; background-color: #ffffff; }
        .section-title { font-size: 14px; font-weight: 600; color: #000; margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
        .item-card { background: white; border: 1px solid #666; padding: 10px; display: flex; align-items: center; gap: 10px; }
        .empty-text { color: #666; font-size: 14px; font-style: italic; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2 class="logo-title">🗑️ Tempat Sampah</h2>
        <p style="font-size: 13px; color: #666;">Item di sini akan terhapus permanen secara otomatis setelah 30 hari.</p>
        
        <a href="{{ url('/dashboard') }}" 
           style="display: block; padding: 12px; background: #0b57d0; color: white; border: none; text-decoration: none; font-weight: bold; text-align: center; margin-top: 20px;">
           ← Kembali ke Drive
        </a>
    </div>

    <div class="main-content">
        <h1 style="margin-top: 0; font-size: 24px;">Daftar Item Terhapus</h1>

        <div class="section-title">Folder Terhapus</div>
        <div class="grid">
            @forelse ($folders as $folder)
                <div class="item-card" style="background: #f8d7da; border-color: #f5c6cb;">
                    <span>📁</span>
                    <span style="text-decoration: line-through;">{{ $folder->name }}</span>
                </div>
            @empty
                <p class="empty-text">Kosong.</p>
            @endforelse
        </div>

        <div class="section-title">File Terhapus</div>
        <div class="grid">
            @forelse ($files as $file)
                <div class="item-card" style="background: #f8d7da; border-color: #f5c6cb;">
                    <span>📄</span>
                    <span style="text-decoration: line-through;">{{ $file->name }}</span>
                </div>
            @empty
                <p class="empty-text">Kosong.</p>
            @endforelse
        </div>
    </div>

</body>
</html>