<!DOCTYPE html>
<html lang="id">

<head>
    <title>Drive Saya</title>
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
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .item-card {
            background: white;
            border: 1px solid #666;
            padding: 10px;
            border-radius: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .empty-text {
            color: #666;
            font-size: 14px;
        }

        /* --- CSS BARU UNTUK MENU DROPDOWN TITIK TIGA --- */
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
            z-index: 10; /* Agar muncul di atas elemen lain */
        }

        /* Styling untuk isi menu (Download & Hapus) */
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

        /* Class untuk memunculkan dropdown via JavaScript */
        .show { display: block; }
        /* ------------------------------------------------ */

    </style>
</head>

<body>

    <div class="sidebar">
        <h2 class="logo-title">My Drive</h2>

        <div style="border: 1px solid #aaa; padding: 15px; margin-bottom: 20px; background-color: white;">
            <h3 style="margin-top: 0; font-size: 16px; margin-bottom: 10px;">Buat Folder Baru</h3>
            <form action="{{ url('/folder/create') }}" method="POST"
                style="display: flex; flex-direction: column; gap: 10px;">
                @csrf
                <input type="text" name="name" placeholder="Nama Folder..." required
                    style="padding: 10px; border: 1px solid #999; border-radius: 0; font-family: inherit;">
                <button type="submit"
                    style="padding: 10px; background-color: #0b57d0; color: white; border: none; border-radius: 0; cursor: pointer; font-weight: bold;">
                    + Tambah Folder
                </button>
            </form>
        </div>

        <div style="border: 1px solid #aaa; padding: 15px; margin-bottom: 25px; background-color: white;">
            <h3 style="margin-top: 0; font-size: 16px; margin-bottom: 10px;">Upload File</h3>
            <form action="{{ url('/file/upload') }}" method="POST" enctype="multipart/form-data"
                style="display: flex; flex-direction: column; gap: 10px;">
                @csrf
                <input type="file" name="file" required
                    style="padding: 5px; border: 1px solid #999; border-radius: 0; background: #fff; font-family: inherit;">
                <button type="submit"
                    style="padding: 10px; background-color: #188038; color: white; border: none; border-radius: 0; cursor: pointer; font-weight: bold;">
                    ↑ Upload File
                </button>
            </form>
        </div>

        <a href="{{ url('/sampah') }}"
            style="display: block; padding: 12px; background: white; color: #1f1f1f; border: 2px solid #ccc; text-decoration: none; font-weight: bold; text-align: center; margin-bottom: 20px;">
            🗑️ Menu Sampah
        </a>
        
        <form action="{{ url('/logout') }}" method="POST"
            style="margin-top: auto; display: flex; flex-direction: column;">
            @csrf
            <button type="submit" class="logout-btn">Keluar Akun</button>
        </form>
    </div>

    <div class="main-content">
        
        <form action="{{ url('/dashboard') }}" method="GET"
            style="display: flex; gap: 10px; margin-bottom: 30px;">
            <input type="text" name="telusuri" placeholder="Telusuri folder atau file..."
                value="{{ request('telusuri') }}"
                style="flex: 1; padding: 12px; border: 2px solid #ccc; border-radius: 0; font-family: inherit; font-size: 16px;">
            <button type="submit"
                style="padding: 12px 25px; background-color: #1f1f1f; color: white; border: none; border-radius: 0; cursor: pointer; font-weight: bold; font-size: 16px;">
                Cari
            </button>
            @if (request('telusuri'))
                <a href="{{ url('/dashboard') }}"
                    style="padding: 12px 20px; background-color: #d93025; color: white; text-decoration: none; font-weight: bold; display: flex; align-items: center; border-radius: 0;">
                    Reset
                </a>
            @endif
        </form>

        <div class="section-title">Daftar Folder</div>
        <div class="grid">
            @forelse ($folders as $folder)
                <div class="item-card" style="justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span>📁</span>
                        <span>{{ $folder->name }}</span>
                    </div>
                    
                    <!-- MENU TITIK TIGA FOLDER -->
                    <div class="dropdown">
                        <button onclick="toggleDropdown('folder-{{ $folder->id }}')" class="dropbtn">⋮</button>
                        <div id="folder-{{ $folder->id }}" class="dropdown-content">
                            <!-- Link download disiapkan sementara (#) -->
                            <a href="#">Download</a>
                            <form action="{{ url('/folder/'.$folder->id.'/delete') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit">Hapus</button>
                            </form>
                        </div>
                    </div>

                </div>
            @empty
                <p class="empty-text">Belum ada folder yang dibuat.</p>
            @endforelse
        </div>

        <div class="section-title">Daftar File</div>
        <div class="grid">
            @forelse ($files as $file)
                <div class="item-card" style="justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span>📄</span>
                        <span>{{ $file->name }}</span>
                    </div>

                    <!-- MENU TITIK TIGA FILE -->
                    <div class="dropdown">
                        <button onclick="toggleDropdown('file-{{ $file->id }}')" class="dropbtn">⋮</button>
                        <div id="file-{{ $file->id }}" class="dropdown-content">
                            <!-- Link download disiapkan sementara (#) -->
                            <a href="#">Download</a>
                            <form action="{{ url('/file/'.$file->id.'/delete') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit">Hapus</button>
                            </form>
                        </div>
                    </div>

                </div>
            @empty
                <p class="empty-text">Belum ada file yang diunggah.</p>
            @endforelse
        </div>
        
    </div>

    <!-- SCRIPT MANUAL UNTUK MENGONTROL DROPDOWN TITIK TIGA -->
    <script>
        // Fungsi untuk memunculkan atau menyembunyikan menu saat titik tiga diklik
        function toggleDropdown(id) {
            // Tutup semua menu yang mungkin sedang terbuka
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                if (dropdowns[i].id !== id) {
                    dropdowns[i].classList.remove('show');
                }
            }
            // Buka menu yang diklik
            document.getElementById(id).classList.toggle("show");
        }

        // Fungsi agar menu tertutup saat mengklik area kosong di layar
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