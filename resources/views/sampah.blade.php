<!DOCTYPE html>
<html lang="id">

<head>
    <title>Sampah - Drive Saya</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            display: flex;
            height: 100vh;
            background-color: #ffffff;
            color: #1f1f1f;
        }

        .sidebar {
            width: 250px;
            background: #f1f3f4;
            padding: 20px;
            display: flex;
            flex-direction: column;
            border-right: 2px solid #ccc;
        }

        .logo-title {
            margin-top: 0;
            font-size: 22px;
            margin-bottom: 30px;
        }

        .logout-btn {
            margin-top: auto;
            padding: 12px;
            background: white;
            color: #d93025;
            border: 2px solid #d93025;
            border-radius: 0;
            cursor: pointer;
            font-weight: bold;
        }

        .logout-btn:hover {
            background: #d93025;
            color: white;
        }

        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            background-color: #ffffff;
            border-radius: 0;
            box-shadow: none;
        }

        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: #000;
            margin-top: 30px;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .grid {
            display: flex;
            flex-direction: column;
        }

        .folder-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .folder-card {
            background: white;
            border: 1px solid #666;
            padding: 10px;
            border-radius: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .item-card {
            background: white;
            border-bottom: 1px solid #ccc;
            padding: 12px 10px;
            border-radius: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .item-card:hover {
            background-color: #f9f9f9;
        }

        .file-name {
            min-width: 0;
            flex: 1;
            overflow: hidden;
        }

        .file-name span:last-child {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .empty-text {
            color: #666;
            font-size: 14px;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropbtn {
            background: none;
            border: none;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            padding: 0 10px;
            color: #1f1f1f;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 120px;
            border: 1px solid #ccc;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            z-index: 10;
        }

        .dropdown-content a, .dropdown-content button {
            color: black;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            width: 100%;
            text-align: left;
            border: none;
            background: none;
            cursor: pointer;
            font-family: inherit;
            font-size: 14px;
            box-sizing: border-box;
        }

        .dropdown-content a:hover, .dropdown-content button:hover {
            background-color: #f1f3f4;
        }

        .dropdown-content button.text-danger {
            color: #d93025;
            font-weight: bold;
        }

        .show { display: block; }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #1f1f1f;
            color: white;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
            border-radius: 0;
        }

        .back-btn:hover {
            background-color: #444;
        }

        .folder-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 30px; margin-top: 0;">
            <img src="{{ asset('images/kominfo.png') }}" alt="Logo Kominfo" style="width: 40px; height: 40px; object-fit: contain;">
            <h2 class="logo-title" style="margin: 0; font-size: 18px;">KOMDRIVE METRO</h2>
        </div>

        <a href="{{ url('/dashboard') }}"
            style="display: block; padding: 12px; background: white; color: #1f1f1f; border: 2px solid #ccc; text-decoration: none; font-weight: bold; text-align: center; margin-bottom: 20px;">
            ← Kembali ke Dashboard
        </a>

        <a href="{{ url('/sampah') }}"
            style="display: block; padding: 12px; background: #e0e0e0; color: #1f1f1f; border: 2px solid #ccc; text-decoration: none; font-weight: bold; text-align: center; margin-bottom: 20px;">
            Sampah
        </a>
        
        <form action="{{ url('/logout') }}" method="POST"
            style="margin-top: auto; display: flex; flex-direction: column;">
            @csrf
            <button type="submit" class="logout-btn">Keluar Akun</button>
        </form>
    </div>

    <div class="main-content">

        <div class="folder-title">Sampah</div>
        <p style="color: #666; font-size: 14px; margin-bottom: 30px;">Item di sini dapat dipulihkan atau dihapus secara permanen.</p>

        @if ($folders->count() == 0 && $files->count() == 0)
            <p class="empty-text" style="margin-top: 80px; text-align: center;">Tempat sampah kosong.</p>
        @else
            @if ($folders->count() > 0)
                <div class="section-title">Daftar Folder</div>
                <div class="folder-grid">
                @foreach ($folders as $folder)
                    <div class="folder-card" style="justify-content: space-between;">
                            <div class="file-name" style="display: flex; align-items: center; gap: 10px; cursor: default; user-select: none;">
                                <span>📁</span>
                                <span>{{ $folder->name }}</span>
                            </div>
                            
                            <div class="dropdown">
                                <button onclick="toggleDropdown('folder-{{ $folder->id }}')" class="dropbtn">⋮</button>
                                <div id="folder-{{ $folder->id }}" class="dropdown-content">
                                    <form action="{{ url('/sampah/folder/'.$folder->id.'/restore') }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <button type="submit">Pulihkan</button>
                                    </form>
                                    <form action="{{ url('/sampah/folder/'.$folder->id.'/force-delete') }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="text-danger">Hapus Permanen</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($files->count() > 0)
                <div class="section-title">Daftar File</div>
                <div class="grid">
                    @foreach ($files as $file)
                        <div class="item-card" style="justify-content: space-between;">
                            <div class="file-name" style="display: flex; align-items: center; gap: 10px; cursor: default; user-select: none;">
                                <span>📄</span>
                                <span>{{ $file->name }}</span>
                            </div>

                            <div class="dropdown">
                                <button onclick="toggleDropdown('file-{{ $file->id }}')" class="dropbtn">⋮</button>
                                <div id="file-{{ $file->id }}" class="dropdown-content">
                                    <form action="{{ url('/sampah/file/'.$file->id.'/restore') }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <button type="submit">Pulihkan</button>
                                    </form>
                                    <form action="{{ url('/sampah/file/'.$file->id.'/force-delete') }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="text-danger">Hapus Permanen</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
        
    </div>

    <script>
        function toggleDropdown(id) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                if (dropdowns[i].id !== id) {
                    dropdowns[i].classList.remove('show');
                }
            }
            document.getElementById(id).classList.toggle("show");
        }

        window.onclick = function(event) {
            if (!event.target.matches('.dropbtn')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>

</body>

</html>