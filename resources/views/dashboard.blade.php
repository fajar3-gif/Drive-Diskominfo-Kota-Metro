<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Drive</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
            background-color: #ffffff;
            color: #334155;
        }

        .header {
            background: #1b5c96;
            padding: 0 40px;
            height: 60px;
            box-sizing: border-box;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: none;
            z-index: 20;
            color: white;
        }

        .main-container {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        .sidebar {
            width: 220px;
            background: #f3f6f8;
            padding: 20px;
            display: flex;
            flex-direction: column;
            box-shadow: none;
            z-index: 10;
            border-right: 1px solid #e2e8f0;
        }

        .logo-title {
            margin-top: 0;
            font-size: 20px;
            margin-bottom: 30px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: white;
        }

        .sidebar-card {
            background: rgba(255, 255, 255, 0.35);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            padding: 20px;
            border-radius: 0;
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }
        
        .sidebar-card:hover {
            background: rgba(255, 255, 255, 0.45);
        }

        .sidebar-card h3 {
            color: #1e293b;
            margin-top: 0;
            font-size: 15px;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .sidebar-input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 0;
            background: rgba(255, 255, 255, 0.95);
            font-family: inherit;
            font-size: 14px;
            color: #1e293b;
            outline: none;
            transition: all 0.3s ease;
            box-sizing: border-box;
            margin-bottom: 10px;
        }

        .sidebar-input:focus {
            background: #fff;
            border-color: #fff;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
        }

        .sidebar-btn {
            width: 100%;
            padding: 4px 14px;
            height: 38px;
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            box-shadow: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .sidebar-btn:hover,
        .sidebar-btn.active {
            background-color: #1e40af;
            color: #ffffff;
        }

        .sidebar-btn-outline {
            width: 100%;
            padding: 4px 14px;
            height: 38px;
            background-color: #ffffff;
            color: #2563eb;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            box-shadow: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .sidebar-btn-outline:hover {
            background-color: #f3f4f6;
            color: #1d4ed8;
            border-color: #cbd5e1;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 10px;
        }

        .sidebar-menu-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px 14px;
            border-radius: 0;
            border-left: 4px solid transparent;
            color: #475569;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .sidebar-menu-item:hover,
        .sidebar-menu-item.active {
            background-color: #e0e7ff;
            color: #2563eb;
        }
        
        .sidebar-menu-item.active {
            font-weight: 600;
            border-left-color: #2563eb;
        }

        .sidebar-menu-item img {
            width: 22px;
            height: 22px;
            object-fit: contain;
            opacity: 0.6;
            transition: all 0.2s ease;
        }

        .sidebar-menu-item:hover img,
        .sidebar-menu-item.active img {
            opacity: 1;
            filter: invert(34%) sepia(87%) saturate(3020%) hue-rotate(217deg) brightness(96%) contrast(98%);
        }

        .sidebar-link {
            display: block;
            padding: 14px;
            background: rgba(255, 255, 255, 0.35);
            color: #1e293b;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 0;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: background-color 0.3s ease;
            backdrop-filter: blur(10px);
            margin-bottom: 20px;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.45);
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }

        .main-content {
            flex: 1;
            padding: 20px 40px 40px 40px;
            overflow-y: auto;
        }

        .search-input {
            width: 500px;
            max-width: 100%;
            padding: 8px 14px;
            height: 38px;
            border: none;
            border-radius: 4px;
            font-family: inherit;
            font-size: 15px;
            background: white;
            transition: all 0.3s ease;
            outline: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .search-input:focus {
            box-shadow: 0 2px 15px rgba(0,0,0,0.2);
        }

        .search-btn {
            padding: 10px 28px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            transition: background-color 0.3s ease;
        }

        .search-btn:hover {
            background: rgba(0, 0, 0, 0.10);
        }

        .reset-btn {
            padding: 10px 24px;
            background-color: rgba(239, 68, 68, 0.9);
            color: white;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .reset-btn:hover {
            background-color: rgba(220, 38, 38, 1);
        }

        /* --- PENGATURAN GRID UNTUK KOLOM DATA --- */
        .list-header {
            display: grid;
            /* Mengatur perbandingan lebar kolom: Nama (luas), Tanggal (sedang), Tipe (sedang), Ukuran (kecil), Menu (sangat kecil) */
            grid-template-columns: minmax(200px, 2.5fr) minmax(150px, 1.2fr) minmax(150px, 1.2fr) minmax(100px, 0.8fr) 40px;
            align-items: center;
            padding: 10px 14px;
            font-weight: 600;
            color: #64748b;
            font-size: 14px;
            margin-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
            gap: 15px;
        }

        .item-card {
            background: white;
            padding: 10px 14px;
            border-radius: 0;
            display: grid;
            /* Harus sama persis dengan grid-template-columns milik list-header */
            grid-template-columns: minmax(200px, 2.5fr) minmax(150px, 1.2fr) minmax(150px, 1.2fr) minmax(100px, 0.8fr) 40px;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid #e2e8f0; border-top: none; border-left: none; border-right: none;
            transition: background-color 0.2s ease;
        }

        .item-card:hover {
            background-color: #e8f0fe;
        }

        .grid {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        /* === GRID VIEW MODE === */
        .grid.view-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 12px;
            padding: 4px 0;
        }
        .grid.view-grid .item-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 16px 10px 10px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            gap: 8px;
            min-height: 100px;
            position: relative;
            cursor: pointer;
        }
        .grid.view-grid .item-card .file-name {
            flex-direction: column;
            gap: 6px;
            text-align: center;
            width: 100%;
        }
        .grid.view-grid .item-card .file-name img {
            width: 40px !important;
            height: 40px !important;
            margin-right: 0 !important;
        }
        .grid.view-grid .item-card .file-name span:last-child {
            font-size: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
        .grid.view-grid .item-card .item-details {
            display: none;
        }
        .grid.view-grid .item-card .dropdown {
            position: absolute;
            top: 4px;
            right: 4px;
            justify-content: center;
        }
        .grid.view-grid .item-card .select-checkbox {
            position: absolute;
            top: 6px;
            left: 6px;
            opacity: 0;
        }
        .grid.view-grid .item-card:hover .select-checkbox,
        .grid.view-grid .item-card.selected .select-checkbox {
            opacity: 1;
        }
        /* Sembunyikan list-header saat grid mode */
        .list-header.hidden-header {
            display: none;
        }

        .file-name {
            min-width: 0;
            overflow: hidden;
            display: flex; 
            align-items: center; 
            gap: 12px; 
            cursor: pointer; 
            user-select: none;
            font-weight: 500;
            color: #334155;
        }

        .file-name span:last-child {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Styling untuk data di tiap kolom tambahan */
        .item-details {
            color: #64748b;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ===== MULTI-SELECT ===== */
        .item-card.selected {
            background-color: #e8f0fe !important;
        }
        .select-checkbox {
            width: 18px;
            height: 18px;
            border: 2px solid #bcc0c4;
            border-radius: 3px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: white;
            background: white;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.15s, background 0.15s;
        }
        .item-card:hover .select-checkbox,
        .item-card.selected .select-checkbox {
            opacity: 1;
        }
        .item-card.selected .select-checkbox {
            background: #1a73e8;
            border-color: #1a73e8;
        }
        #selection-bar button {
            transition: background 0.15s;
        }

        .empty-text {
            color: #64748b;
            font-size: 14px;
            font-style: italic;
            padding: 10px 0;
        }

        .dropdown {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            text-align: right;
        }

        .dropbtn {
            background: none;
            border: none;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            padding: 0 8px;
            color: #64748b;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 28px;
            line-height: 1;
        }
        
        .dropbtn:hover {
            color: #1e293b;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 200px;
            border-bottom: 1px solid #e2e8f0; border-top: none; border-left: none; border-right: none;
            border-radius: 0;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            z-index: 10;
            overflow: hidden;
        }

        .dropdown-content a, .dropdown-content button {
            color: #334155;
            padding: 12px 18px;
            text-decoration: none;
            display: block;
            width: 100%;
            text-align: left;
            border: none;
            background: none;
            cursor: pointer;
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
            box-sizing: border-box;
            white-space: nowrap;
            transition: background 0.2s;
        }

        .dropdown-content a:hover, .dropdown-content button:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }

        .show { display: block; }
        
        /* Error message style */
        .error-msg {
            font-size: 12px;
            color: #fca5a5;
            margin-bottom: 10px;
            display: block;
        }

        /* Popup Tambah Folder */
        .folder-popup-wrapper {
            position: relative;
        }

        .folder-popup {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            width: 220px;
            background: white;
            border-radius: 0;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            border-bottom: 1px solid #e2e8f0; border-top: none; border-left: none; border-right: none;
            z-index: 50;
        }

        .folder-popup.show {
            display: block;
        }

        .folder-popup-title {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            margin: 0 0 12px 0;
        }

        .folder-popup-input {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 0;
            font-family: inherit;
            font-size: 13px;
            color: #1e293b;
            outline: none;
            transition: border-color 0.2s ease;
            box-sizing: border-box;
            margin-bottom: 12px;
        }

        .folder-popup-input:focus {
            border-color: #1b5c96;
            box-shadow: 0 0 0 3px rgba(27, 92, 150, 0.1);
        }

        .folder-popup-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .folder-popup-cancel {
            padding: 7px 14px;
            border-bottom: 1px solid #e2e8f0; border-top: none; border-left: none; border-right: none;
            background: #ffffff;
            border-radius: 0;
            cursor: pointer;
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
            color: #64748b;
            transition: all 0.2s ease;
        }

        .folder-popup-cancel:hover {
            background: #f1f5f9;
        }

        .folder-popup-submit {
            padding: 7px 14px;
            border: none;
            background: #1b5c96;
            border-radius: 0;
            cursor: pointer;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            color: #ffffff;
            transition: all 0.2s ease;
        }

        .folder-popup-submit:hover {
            background: #154877;
        }
    </style>
</head>

<body>

    <div class="header">
        <div style="display: flex; align-items: center; gap: 12px; width: 220px;">
            <img src="{{ asset('images/nih.png') }}" alt="Logo Kominfo" style="width: 50px; height: 50px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2)); flex-shrink: 0;">
            <h2 class="logo-title" style="margin: 0; font-size: 20px; font-weight: 700; letter-spacing: 0.5px;">KOMSAFE</h2>
        </div>

        <!-- Form Pencarian (Tengah) -->
        <form action="{{ url('/dashboard') }}" method="GET" style="display: flex; gap: 12px; flex: 1; max-width: 600px; margin: 0 40px; justify-content: center; align-items: center;">
            <div style="position: relative; flex: 1; display: flex; align-items: center;">
                <button type="submit" style="position: absolute; left: 14px; background: none; border: none; padding: 0; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Cari">
                    <img src="{{ asset('images/telusuri.png') }}" alt="Cari" style="width: 20px; height: 20px; opacity: 0.6; transition: opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
                </button>
                <input type="text" name="telusuri" placeholder="Telusuri folder atau file..."
                    value="{{ request('telusuri') }}" class="search-input" style="width: 100%; padding-left: 44px; padding-right: 44px; box-sizing: border-box;">
                @if (request('telusuri'))
                    <a href="{{ url('/dashboard') }}" style="position: absolute; right: 14px; display: flex; align-items: center; justify-content: center; text-decoration: none;" title="Hapus Pencarian">
                        <img src="{{ asset('images/close.png') }}" alt="Clear" style="width: 16px; height: 16px; opacity: 0.5; transition: opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'">
                    </a>
                @endif
            </div>
        </form>

        <!-- Profil User (Kanan) -->
        <div class="dropdown profile-dropdown" style="position: relative;">
            <button onclick="toggleDropdown('profile-menu')" class="dropbtn" style="padding: 0; border-radius: 50%; outline: none; display: flex; align-items: center; justify-content: center;">
                @if(Auth::user()->avatar)
                    <img src="{{ Auth::user()->avatar }}" alt="Profile" referrerpolicy="no-referrer" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=1b5c96&color=fff" alt="Profile" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                @endif
            </button>
            
            <!-- Popup Profil & Logout -->
            <div id="profile-menu" class="dropdown-content" style="right: 0; min-width: 250px; padding: 24px; text-align: center; border-radius: 0; box-shadow: 0 10px 30px rgba(0,0,0,0.12); border: 1px solid #f1f5f9; top: 60px;">
                <p style="margin: 0 0 16px 0; font-size: 14px; font-weight: 500; color: #475569; word-break: break-all;">
                    {{ Auth::user()->email }}
                </p>
                <div style="margin-bottom: 24px;">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="Profile" referrerpolicy="no-referrer" style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=1b5c96&color=fff" alt="Profile" style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    @endif
                </div>
            
                
                <form action="{{ url('/logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" style="width: 100%; padding: 12px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-top: none; border-left: none; border-right: none; border-radius: 0; cursor: pointer; font-size: 14px; font-weight: 600; color: #475569; text-align: center; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='#f1f5f9'; this.style.color='#0f172a';" onmouseout="this.style.backgroundColor='#f8fafc'; this.style.color='#475569';">
                        Keluar dari akun
                    </button>
                </form>
            </div>
            
        </div>
    </div>

    <div class="main-container">
        <div class="sidebar">
            <div style="display: flex; flex-direction: column; gap: 15px; margin-bottom: 20px;">
                <div class="folder-popup-wrapper">
                    <button type="button" class="sidebar-btn" onclick="toggleFolderPopup()">
                        <img src="{{ asset('images/tambah-folder.png') }}" alt="Tambah Folder" style="width: 20px; height: 20px; object-fit: contain; filter: brightness(0) invert(1);">
                        Tambah Folder
                    </button>
                    <div class="folder-popup" id="folderPopup">
                        <p class="folder-popup-title">Buat Folder Baru</p>
                        <form action="{{ url('/folder/create') }}" method="POST">
                            @csrf
                            <input type="text" name="name" class="folder-popup-input" id="folderNameInput" placeholder="Nama folder" autocomplete="off" required>
                            <div class="folder-popup-actions">
                                <button type="button" class="folder-popup-cancel" onclick="closeFolderPopup()">Batal</button>
                                <button type="submit" class="folder-popup-submit">Tambah</button>
                            </div>
            
                        </form>
                    </div>
            
                </div>
            

                <form action="{{ url('/file/upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm" style="margin: 0;">
                    @csrf
                    <input type="file" name="file" id="fileInput" style="display: none;" required onchange="document.getElementById('uploadForm').submit();">
                    <button type="button" class="sidebar-btn-outline" onclick="document.getElementById('fileInput').click();">
                        <img src="{{ asset('images/upload-file.png') }}" alt="Upload File" style="width: 20px; height: 20px; object-fit: contain; filter: invert(47%) sepia(15%) saturate(841%) hue-rotate(149deg) brightness(92%) contrast(87%);">
                        Upload File
                    </button>
                    @error('file')
                        <div style="font-size: 12px; color: #d93025; margin-top: 5px; text-align: center;">{{ $message }}</div>
                    @enderror
                </form>
            </div>
            

            <div class="sidebar-menu">
                <a href="{{ url('/dashboard') }}" class="sidebar-menu-item active">
                    <img src="{{ asset('images/cloud.png') }}" alt="Drive">
                    Drive
                </a>
                <a href="{{ url('/terbaru') }}" class="sidebar-menu-item">
                    <img src="{{ asset('images/terbaru.png') }}" alt="Terbaru">
                    Terbaru
                </a>
                <a href="{{ url('/favorit') }}" class="sidebar-menu-item">
                    <img src="{{ asset('images/dibintangi.png') }}" alt="Favorit">
                    Favorit
                </a>
                <a href="{{ url('/sampah') }}" class="sidebar-menu-item">
                    <img src="{{ asset('images/sampah.png') }}" alt="Sampah">
                    Sampah
                </a>
            </div>
            
            <!-- INDIKATOR PENYIMPANAN/ALGORITMANYA -->
            @php
                $usedStorage = \App\Models\FileItem::where('user_id', Auth::id())->sum('size');
                $quotaBytes = 1 * 1024 * 1024 * 1024; // 1 GB
                $percentage = ($usedStorage / $quotaBytes) * 100;
                if ($percentage > 100) $percentage = 100;
                
                // Konversi ke satuan yang mudah dibaca (MB / GB)
                if ($usedStorage >= 1073741824) {
                    $usedText = number_format($usedStorage / 1073741824, 1) . ' GB';
                } elseif ($usedStorage >= 1048576) {
                    $usedText = number_format($usedStorage / 1048576, 1) . ' MB';
                } elseif ($usedStorage >= 1024) {
                    $usedText = number_format($usedStorage / 1024, 1) . ' KB';
                } else {
                    $usedText = $usedStorage . ' B';
                }
            @endphp
            <div style="margin-top: auto; padding: 20px 5px 0 5px;">
                <div style="font-size: 14px; color: #1e293b; font-weight: 600; margin-bottom: 4px;">Penyimpanan</div>
                <div style="font-size: 13px; color: #475569; font-weight: 500; margin-bottom: 8px;">
                    {{ $usedText }} dari 1 GB terpakai
                </div>
                <div style="width: 100%; background-color: #cbd5e1; height: 6px; border-radius: 4px; overflow: hidden; margin-bottom: 8px;">
                    <div style="width: {{ $percentage }}%; background-color: #1a73e8; height: 100%; border-radius: 4px; transition: width 0.3s ease;"></div>
                </div>
            </div>
        </div>

        <div class="main-content">
            
            @if(session('success') || session('error'))
                <div id="toast-notification" style="position: fixed; bottom: 30px; right: 30px; background: white; border-bottom: 1px solid #e2e8f0; border-top: none; border-left: none; border-right: none; border-radius: 0; padding: 16px 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 15px; z-index: 9999; animation: slideUp 0.3s ease-out; font-size: 14px; font-weight: 500; color: #1e293b; min-width: 300px; border-left: 4px solid {{ session('success') ? '#10b981' : '#ef4444' }};">
                    <img src="{{ session('success') ? asset('images/ceklis.png') : asset('images/silang.png') }}" alt="Ikon" style="width: 24px; height: 24px; object-fit: contain;">
                    <span>{{ session('success') ?? session('error') }}</span>
                    <button onclick="document.getElementById('toast-notification').style.display='none'" style="margin-left: auto; background: none; border: none; font-size: 20px; cursor: pointer; color: #94a3b8; padding: 0;">&times;</button>
                </div>
            
                <style>
                    @keyframes slideUp {
                        from { transform: translateY(100px); opacity: 0; }
                        to { transform: translateY(0); opacity: 1; }
                    }
                </style>
                
            @endif

            <!-- JUDUL HALAMAN UTAMA -->
            <div style="display: flex; align-items: center; gap: 0; margin-bottom: 14px; overflow: hidden; white-space: nowrap; min-height: 32px;">
                <span style="flex-shrink: 0; font-size: 20px; font-weight: 600; color: #202124; padding: 4px 6px;">Drive</span>
            </div>

            <!-- TOOLBAR WRAPPER -->
            <div style="position: relative; min-height: 36px; margin-bottom: 12px; display: flex; align-items: center; width: 100%;">
            
            <!-- FILTER BAR -->
            <div id="filter-bar" style="position: absolute; top: 0; left: 0; width: 100%; display: flex; gap: 10px; transition: opacity 0.2s ease, visibility 0.2s ease; opacity: 1; visibility: visible; z-index: 5;">
                <!-- Type Filter -->
                <div class="dropdown" style="position: relative;">
                    <button class="filter-btn" onclick="toggleDropdown('type-filter-menu')" style="background-color: {{ request('type') ? '#e8eaed' : '#f1f5f9' }}; border: 1px solid {{ request('type') ? '#9aa0a6' : '#cbd5e1' }}; border-radius: 4px; padding: 4px 14px; height: 32px; box-sizing: border-box; font-size: 14px; color: #334155; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#e2e8f0'" onmouseout="this.style.backgroundColor='{{ request('type') ? '#e8eaed' : '#f1f5f9' }}'">
                        {{ request('type') == 'folder' ? 'Folder' : (request('type') == 'file' ? 'File' : 'Jenis') }} <span style="font-size: 10px;">▼</span>
                    </button>
                    <div id="type-filter-menu" class="dropdown-content" style="top: 100%; left: 0; min-width: 150px; margin-top: 4px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                        <a href="{{ url()->current() }}?type=&modified={{ request('modified') }}" style="{{ request('type') == '' ? 'background-color: #f1f5f9; font-weight: 500;' : '' }}">Semua Jenis</a>
                        <a href="{{ url()->current() }}?type=folder&modified={{ request('modified') }}" style="{{ request('type') == 'folder' ? 'background-color: #f1f5f9; font-weight: 500;' : '' }}">Folder</a>
                        <a href="{{ url()->current() }}?type=file&modified={{ request('modified') }}" style="{{ request('type') == 'file' ? 'background-color: #f1f5f9; font-weight: 500;' : '' }}">File</a>
                    </div>
                </div>

                <!-- Modified Filter -->
                <div class="dropdown" style="position: relative;">
                    <button class="filter-btn" onclick="toggleDropdown('modified-filter-menu')" style="background-color: {{ request('modified') ? '#e8eaed' : '#f1f5f9' }}; border: 1px solid {{ request('modified') ? '#9aa0a6' : '#cbd5e1' }}; border-radius: 4px; padding: 4px 14px; height: 32px; box-sizing: border-box; font-size: 14px; color: #334155; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#e2e8f0'" onmouseout="this.style.backgroundColor='{{ request('modified') ? '#e8eaed' : '#f1f5f9' }}'">
                        @php
                            $modLabel = match(request('modified')) {
                                'today' => 'Hari ini',
                                '7days' => '7 hari terakhir',
                                '30days' => '30 hari terakhir',
                                default => 'Dimodifikasi'
                            };
                        @endphp
                        {{ $modLabel }} <span style="font-size: 10px;">▼</span>
                    </button>
                    <div id="modified-filter-menu" class="dropdown-content" style="top: 100%; left: 0; min-width: 160px; margin-top: 4px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                        <a href="{{ url()->current() }}?type={{ request('type') }}&modified=" style="{{ request('modified') == '' ? 'background-color: #f1f5f9; font-weight: 500;' : '' }}">Kapan saja</a>
                        <a href="{{ url()->current() }}?type={{ request('type') }}&modified=today" style="{{ request('modified') == 'today' ? 'background-color: #f1f5f9; font-weight: 500;' : '' }}">Hari ini</a>
                        <a href="{{ url()->current() }}?type={{ request('type') }}&modified=7days" style="{{ request('modified') == '7days' ? 'background-color: #f1f5f9; font-weight: 500;' : '' }}">7 hari terakhir</a>
                        <a href="{{ url()->current() }}?type={{ request('type') }}&modified=30days" style="{{ request('modified') == '30days' ? 'background-color: #f1f5f9; font-weight: 500;' : '' }}">30 hari terakhir</a>
                    </div>
                </div>

                <!-- VIEW TOGGLES -->
                <div style="margin-left: auto; display: flex; align-items: center; background-color: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden; height: 32px;">
                    <button onclick="toggleViewMode('list')" id="btn-view-list" style="background: #e2e8f0; border: none; padding: 0 10px; height: 100%; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #1b5c96;" title="Tampilan Daftar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                    </button>
                    <div style="width: 1px; height: 100%; background: #cbd5e1;"></div>
                    <button onclick="toggleViewMode('grid')" id="btn-view-grid" style="background: transparent; border: none; padding: 0 10px; height: 100%; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #64748b;" title="Tampilan Petak">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    </button>
                </div>
            </div>

        <!-- SELECTION BAR -->
        <div id="selection-bar" style="position: absolute; top: 0; left: 0; width: 100%; height: 32px; box-sizing: border-box; display: flex; align-items: center; gap: 8px; padding: 0 14px; background: #e8f0fe; border: 1px solid #d2e3fc; border-radius: 4px; z-index: 10; transition: opacity 0.2s ease, visibility 0.2s ease; opacity: 0; visibility: hidden;">
            <button onclick="clearSelection()" style="background:none;border:none;cursor:pointer;width:28px;height:28px;border-radius:4px;display:flex;align-items:center;justify-content:center;" title="Batalkan pilihan" onmouseover="this.style.background='rgba(60,64,67,0.10)'" onmouseout="this.style.background='none'"><img src="{{ asset('images/close.png') }}" style="width:14px;height:14px;opacity:0.65;"></button>
            <span id="selected-count" style="font-weight:500;color:#3c4043;font-size:14px;margin-right:8px;">0 dipilih</span>
            <div style="width:1px;height:16px;background:#dadce0;margin-right:8px;"></div>
            <button onclick="bulkAction('download')" title="Download" style="background:none;border:none;cursor:pointer;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;" onmouseover="this.style.background='rgba(60,64,67,0.08)'" onmouseout="this.style.background='none'">
                <img src="{{ asset('images/download.png') }}" style="width: 16px; height: 16px; opacity: 0.7;">
            </button>
            <button onclick="bulkAction('trash')" title="Hapus" style="background:none;border:none;cursor:pointer;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;" onmouseover="this.style.background='rgba(60,64,67,0.08)'" onmouseout="this.style.background='none'">
                <img src="{{ asset('images/sampah.png') }}" style="width: 16px; height: 16px; opacity: 0.7;">
            </button>
                </div>
            </div>
            

        <!-- HEADER DAFTAR (GRID KOLOM) -->
        <div class="list-header">
            <div style="display: flex; align-items: center;">
                <span class="select-checkbox" id="selectAllBtn" style="opacity: 1; margin-right: 12px;" onclick="event.stopPropagation(); toggleSelectAll()" title="Pilih Semua">✓</span>
                <div style="cursor: pointer; display: flex; align-items: center;" onclick="window.location.href='?order={{ (isset($order) && $order === 'desc') ? 'asc' : 'desc' }}'" title="Urutkan {{ (isset($order) && $order === 'desc') ? 'A-Z' : 'Z-A' }}">
                    <span style="display: inline-block; width: 24px; text-align: center; margin-right: 8px;">{{ (isset($order) && $order === 'desc') ? '↓' : '↑' }}</span>
                    <span>Nama</span>
                </div>
            </div>
            
            <div>Tanggal ditambahkan</div>
            <div>Tipe</div>
            <div>Ukuran</div>
            <div></div> <!-- Kolom kosong untuk titik tiga (menu dropdown) -->
        </div>

        <!-- CONTAINER DAFTAR ITEM -->
        <div class="grid">

            <!-- LOOPING FILE -->
            @foreach ($folders as $folder)
                    <div class="item-card" data-id="{{ $folder->id }}" data-type="folder" data-url="{{ url('/folder/show/' . $folder->id) }}">

                        <!-- Kolom 1: Nama -->
                        <div class="file-name">
                            <span class="select-checkbox">✓</span>
                            <img src="{{ asset('images/ikon-folder.png') }}" alt="Folder" style="width: 24px; height: 24px; object-fit: contain; margin-right: 8px;">
                            <span>{{ $folder->name }}</span>
                        </div>
                        
                        <!-- Kolom 2: Tanggal -->
                        <div class="item-details">{{ $folder->created_at ? $folder->created_at->format('d/m/Y, H.i') : '-' }}</div>
                        
                        <!-- Kolom 3: Tipe -->
                        <div class="item-details">Folder</div>
                        
                        <!-- Kolom 4: Ukuran -->
                        <div class="item-details">
                            @php
                                $sizeInBytes = $folder->size ?? 0;
                                if ($sizeInBytes >= 1048576) {
                                    echo number_format($sizeInBytes / 1048576, 2) . ' MB';
                                } elseif ($sizeInBytes >= 1024) {
                                    echo number_format($sizeInBytes / 1024, 2) . ' KB';
                                } else {
                                    echo $sizeInBytes . ' B';
                                }
                            @endphp
                        </div>
                        
                        <!-- Kolom 5: Menu Action -->
                        <div class="dropdown">
                            <button onclick="toggleDropdown('folder-{{ $folder->id }}')" class="dropbtn">⋮</button>
                            <div id="folder-{{ $folder->id }}" class="dropdown-content">
                                <a href="#">Download</a>
                                <form action="{{ url('/folder/'.$folder->id.'/delete') }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit">Hapus</button>
                                </form>
                                <form action="{{ url('/folder/'.$folder->id.'/favorite') }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" style="display: flex; align-items: center; gap: 8px;">
                                        @if($folder->is_favorite ?? false)
                                            <img src="{{ asset('images/favorite.png') }}" alt="Ikon" style="width: 16px; height: 16px; opacity: 0.6;">
                                            Hapus dari favorit
                                        @else
                                            <img src="{{ asset('images/favorite.png') }}" alt="Ikon" style="width: 16px; height: 16px; opacity: 0.6;">
                                            Tambahkan ke favorit
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
            @endforeach

                <!-- LOOPING FILE -->
                @foreach ($files as $file)
                <div class="item-card" data-id="{{ $file->id }}" data-type="file" data-ext="{{ strtolower(pathinfo($file->name, PATHINFO_EXTENSION)) }}">

                    <!-- Kolom 1: Nama -->
                    <div class="file-name">
                        <span class="select-checkbox">✓</span>
                        @if(strtolower(pathinfo($file->name, PATHINFO_EXTENSION)) === 'pdf')
                            <img src="{{ asset('images/pdf.png') }}" alt="PDF" style="width: 24px; height: 24px; object-fit: contain; margin-right: 8px;">
                        @elseif(in_array(strtolower(pathinfo($file->name, PATHINFO_EXTENSION)), ['doc', 'docx']))
                            <img src="{{ asset('images/doc.png') }}" alt="Word" style="width: 24px; height: 24px; object-fit: contain; margin-right: 8px;">
                        @elseif(strtolower(pathinfo($file->name, PATHINFO_EXTENSION)) === 'zip')
                            <img src="{{ asset('images/zip.png') }}" alt="ZIP" style="width: 24px; height: 24px; object-fit: contain; margin-right: 8px;">
                        @elseif(in_array(strtolower(pathinfo($file->name, PATHINFO_EXTENSION)), ['mp4', 'mkv', 'avi']))
                            <img src="{{ asset('images/video.png') }}" alt="Video" style="width: 24px; height: 24px; object-fit: contain; margin-right: 8px;">
                        @elseif(in_array(strtolower(pathinfo($file->name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']))
                            <img src="{{ asset('images/image.png') }}" alt="Image" style="width: 24px; height: 24px; object-fit: contain; margin-right: 8px;">
                        @elseif(in_array(strtolower(pathinfo($file->name, PATHINFO_EXTENSION)), ['ppt', 'pptx']))
                            <img src="{{ asset('images/ppt.png') }}" alt="PPT" style="width: 24px; height: 24px; object-fit: contain; margin-right: 8px;">
                        @else
                            <img src="{{ asset('images/file.png') }}" alt="File" style="width: 24px; height: 24px; object-fit: contain; margin-right: 8px;">
                        @endif
                        <span>{{ $file->name }}</span>
                    </div>
            

                    <!-- Kolom 2: Tanggal -->
                    <div class="item-details">{{ $file->created_at ? $file->created_at->format('d/m/Y, H.i') : '-' }}</div>
                    
                    <!-- Kolom 3: Tipe -->
                    <!-- Jika di database ada kolom 'type', bisa pakai $file->type. Sementara ini kita isi generic atau ekstensinya -->
                    <div class="item-details">{{ pathinfo($file->name, PATHINFO_EXTENSION) ? strtoupper(pathinfo($file->name, PATHINFO_EXTENSION)) . ' File' : 'File' }}</div>
                    
                    <!-- Kolom 4: Ukuran -->
                    <div class="item-details">
                        @php
                            $sizeInBytes = $file->size ?? 0;
                            if ($sizeInBytes == 0 && $file->file_path) {
                                $physicalPath = storage_path('app/private/' . $file->file_path);
                                if (!file_exists($physicalPath)) {
                                    $physicalPath = storage_path('app/' . $file->file_path);
                                }
                                if (file_exists($physicalPath)) {
                                    $sizeInBytes = filesize($physicalPath);
                                    $file->update(['size' => $sizeInBytes]);
                                }
                            }
                            if ($sizeInBytes >= 1048576) {
                                echo number_format($sizeInBytes / 1048576, 2) . ' MB';
                            } elseif ($sizeInBytes >= 1024) {
                                echo number_format($sizeInBytes / 1024, 2) . ' KB';
                            } elseif ($sizeInBytes > 0) {
                                echo $sizeInBytes . ' B';
                            } else {
                                echo '-';
                            }
                        @endphp
                    </div>
            

                    <!-- Kolom 5: Menu Action -->
                    <div class="dropdown">
                        <button onclick="toggleDropdown('file-{{ $file->id }}')" class="dropbtn">⋮</button>
                        <div id="file-{{ $file->id }}" class="dropdown-content">
                            <a href="{{ url('/files/'.$file->id.'/download') }}">Download</a>
                            <form action="{{ url('/file/'.$file->id.'/delete') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit">Hapus</button>
                            </form>
                            <form action="{{ url('/file/'.$file->id.'/favorite') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" style="display: flex; align-items: center; gap: 8px;">
                                    @if($file->is_favorite)
                                        <img src="{{ asset('images/favorite.png') }}" alt="Ikon" style="width: 16px; height: 16px; opacity: 0.6;">
                                        Hapus dari favorit
                                    @else
                                        <img src="{{ asset('images/favorite.png') }}" alt="Ikon" style="width: 16px; height: 16px; opacity: 0.6;">
                                        Tambahkan ke favorit
                                    @endif
                                </button>
                            </form>
                        </div>
            
                    </div>
            
                </div>
            
            @endforeach

            <!-- PESAN JIKA KOSONG -->
            @if ($folders->isEmpty() && $files->isEmpty())
                <p class="empty-text">Tidak ada item yang ditemukan.</p>
            @endif

        </div>
        
    </div>
    </div>

    <script>
        // === VIEW MODE TOGGLE ===
        function toggleViewMode(mode) {
            var gridEl = document.querySelector('.grid');
            var headerEl = document.querySelector('.list-header');
            var btnList = document.getElementById('btn-view-list');
            var btnGrid = document.getElementById('btn-view-grid');
            if (!gridEl) return;
            if (mode === 'grid') {
                gridEl.classList.add('view-grid');
                if (headerEl) headerEl.classList.add('hidden-header');
                // Aktifkan tombol grid
                btnGrid.style.background = '#e2e8f0';
                btnGrid.style.color = '#1b5c96';
                btnList.style.background = 'transparent';
                btnList.style.color = '#64748b';
                localStorage.setItem('driveViewMode', 'grid');
            } else {
                gridEl.classList.remove('view-grid');
                if (headerEl) headerEl.classList.remove('hidden-header');
                // Aktifkan tombol list
                btnList.style.background = '#e2e8f0';
                btnList.style.color = '#1b5c96';
                btnGrid.style.background = 'transparent';
                btnGrid.style.color = '#64748b';
                localStorage.setItem('driveViewMode', 'list');
            }
        }

        // Restore view mode saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            var saved = localStorage.getItem('driveViewMode');
            if (saved === 'grid') toggleViewMode('grid');
        });

        // Popup Tambah Folder
        function toggleFolderPopup() {
            var popup = document.getElementById('folderPopup');
            var btn = document.querySelector('.folder-popup-wrapper .sidebar-btn');
            popup.classList.toggle('show');
            btn.classList.toggle('active', popup.classList.contains('show'));
            if (popup.classList.contains('show')) {
                setTimeout(function() {
                    document.getElementById('folderNameInput').focus();
                }, 100);
            }
        }

        function closeFolderPopup() {
            document.getElementById('folderPopup').classList.remove('show');
            document.querySelector('.folder-popup-wrapper .sidebar-btn').classList.remove('active');
            document.getElementById('folderNameInput').value = '';
        }

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
            // Tutup dropdown
            if (!event.target.closest('.dropbtn') && !event.target.closest('.filter-btn')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    dropdowns[i].classList.remove('show');
                }
            }
            // Tutup folder popup saat klik di luar
            if (!event.target.closest('.folder-popup-wrapper')) {
                closeFolderPopup();
            }
        }

                function previewFile(id, ext) {
            window.open('/files/' + id, '_blank');
        }

        // ===== MULTI-SELECT SYSTEM =====
        var _sel = new Map();
        var _lastIdx = -1;

        function _cards() {
            return Array.from(document.querySelectorAll('.item-card[data-id]'));
        }
        function _key(el) { return el.dataset.type + '-' + el.dataset.id; }

        function _selectCard(el) {
            _sel.set(_key(el), {id: el.dataset.id, type: el.dataset.type});
            el.classList.add('selected');
            
        }
        function _deselectCard(el) {
            _sel.delete(_key(el));
            el.classList.remove('selected');
            
        }
        function _toggleCard(el) {
            if (_sel.has(_key(el))) _deselectCard(el); else _selectCard(el);
        }
        function clearSelection() {
            _cards().forEach(function(el){ _deselectCard(el); });
            _sel.clear(); _lastIdx = -1; _updateBar();
        }
        function _selectRange(a, b) {
            var cs = _cards(), s = Math.min(a,b), e = Math.max(a,b);
            for (var i = s; i <= e; i++) _selectCard(cs[i]);
        }
        function toggleSelectAll() {
            var cs = _cards();
            if (cs.length === 0) return;
            if (_sel.size === cs.length) {
                clearSelection();
            } else {
                cs.forEach(function(el) { _selectCard(el); });
                _lastIdx = cs.length - 1;
                _updateBar();
            }
        }
        function _updateBar() {
            var bar = document.getElementById('selection-bar');
            var filterBar = document.getElementById('filter-bar');
            var cnt = document.getElementById('selected-count');
            var saBtn = document.getElementById('selectAllBtn');
            if (_sel.size > 0) {
                bar.style.opacity = '1';
                bar.style.visibility = 'visible';
                if(filterBar) {
                    filterBar.style.opacity = '0';
                    filterBar.style.visibility = 'hidden';
                }
                cnt.textContent = _sel.size + ' dipilih';
                if (saBtn) {
                    if (_sel.size === _cards().length) {
                        saBtn.style.background = '#1a73e8';
                        saBtn.style.borderColor = '#1a73e8';
                        saBtn.style.color = 'white';
                    } else {
                        saBtn.style.background = 'white';
                        saBtn.style.borderColor = '#bcc0c4';
                        saBtn.style.color = 'white';
                    }
                }
            } else {
                bar.style.opacity = '0';
                bar.style.visibility = 'hidden';
                if(filterBar) {
                    filterBar.style.opacity = '1';
                    filterBar.style.visibility = 'visible';
                }
                if (saBtn) {
                    saBtn.style.background = 'white';
                    saBtn.style.borderColor = '#bcc0c4';
                    saBtn.style.color = 'white';
                }
            }
        }
        function bulkAction(action) {
            if (_sel.size === 0) return;
            if (action === 'force-delete' && !confirm('Hapus permanen ' + _sel.size + ' item? Tidak dapat dibatalkan!')) return;
            var folderIds = [], fileIds = [];
            _sel.forEach(function(v){ if(v.type==='folder') folderIds.push(v.id); else fileIds.push(v.id); });
            var form = document.createElement('form');
            form.method = 'POST'; form.action = '/bulk/' + action; form.style.display = 'none';
            var t = document.createElement('input'); t.type='hidden'; t.name='_token';
            t.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(t);
            folderIds.forEach(function(id){ var i=document.createElement('input'); i.type='hidden'; i.name='folder_ids[]'; i.value=id; form.appendChild(i); });
            fileIds.forEach(function(id){ var i=document.createElement('input'); i.type='hidden'; i.name='file_ids[]'; i.value=id; form.appendChild(i); });
            document.body.appendChild(form); form.submit();
        }

        // Init selection on load
        document.addEventListener('DOMContentLoaded', function() {
            // Timer per-item: Map keyed by card element
            var _clickTimers = new Map();

            _cards().forEach(function(card, idx) {

                card.addEventListener('click', function(e) {
                    if (e.target.closest('.dropdown') || e.target.closest('.dropbtn')) return;

                    if (_clickTimers.has(card)) {
                        // === Klik 2x pada item SAMA: Preview file ===
                        clearTimeout(_clickTimers.get(card));
                        _clickTimers.delete(card);
                        if (card.dataset.type === 'file') {
                            previewFile(card.dataset.id, card.dataset.ext);
                        } else if (card.dataset.type === 'folder' && card.dataset.url) {
                            window.location.href = card.dataset.url;
                        }
                        return;
                    }

                    // === Klik 1x: Langsung seleksi ===
                    if (e.shiftKey && _lastIdx !== -1) {
                        _selectRange(_lastIdx, idx);
                    } else {
                        if (e.target.classList.contains('select-checkbox')) {
                            _toggleCard(card);
                        } else {
                            _selectCard(card);
                        }
                    }
                    _lastIdx = idx; _updateBar();

                    _clickTimers.set(card, setTimeout(function() {
                        _clickTimers.delete(card);
                    }, 300));
                });
            });

            @if(session('new_item_id') && session('new_item_type'))
                var newItemId = "{{ session('new_item_id') }}";
                var newItemType = "{{ session('new_item_type') }}";
                var newCard = Array.from(_cards()).find(function(c) {
                    return c.dataset.id === newItemId && c.dataset.type === newItemType;
                });
                if (newCard) {
                    _selectCard(newCard);
                    _updateBar();
                    newCard.scrollIntoView({block:'center', behavior:'smooth'});
                }
            @endif

            document.addEventListener('keydown', function(e) {
                if (e.shiftKey && (e.key === 'ArrowDown' || e.key === 'ArrowUp')) {
                    e.preventDefault();
                    var cs = _cards(); if (!cs.length) return;
                    var ni = _lastIdx === -1 ? (e.key==='ArrowDown'?0:cs.length-1)
                           : (e.key==='ArrowDown' ? Math.min(_lastIdx+1,cs.length-1) : Math.max(_lastIdx-1,0));
                    if (ni !== _lastIdx) {
                        if (_sel.has(_key(cs[ni]))) {
                            _deselectCard(cs[_lastIdx]);
                        } else {
                            _selectCard(cs[ni]);
                        }
                        _lastIdx = ni; _updateBar();
                        cs[ni].scrollIntoView({block:'nearest', behavior:'smooth'});
                    }
                }
                if (e.key === 'Escape') clearSelection();
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.item-card') && !e.target.closest('#selection-bar') && !e.target.closest('.dropdown-content')) {
                    clearSelection();
                }
            });
        });
    </script>
</body>
</html>








