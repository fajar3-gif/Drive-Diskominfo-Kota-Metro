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
                    @if(isset($folder))
                        <input type="hidden" name="parent_id" value="{{ $folder->id }}">
                    @endif
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
            @if(isset($folder))
                <input type="hidden" name="folder_id" value="{{ $folder->id }}">
            @endif
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
        <a href="{{ url('/dashboard') }}" class="sidebar-menu-item {{ ($activeMenu ?? 'drive') === 'drive' ? 'active' : '' }}">
            <img src="{{ asset('images/cloud.png') }}" alt="Drive">
            Drive
        </a>
        <a href="{{ url('/terbaru') }}" class="sidebar-menu-item {{ ($activeMenu ?? 'drive') === 'terbaru' ? 'active' : '' }}">
            <img src="{{ asset('images/terbaru.png') }}" alt="Terbaru">
            Terbaru
        </a>
        <a href="{{ url('/favorit') }}" class="sidebar-menu-item {{ ($activeMenu ?? 'drive') === 'favorit' ? 'active' : '' }}">
            <img src="{{ asset('images/dibintangi.png') }}" alt="Favorit">
            Favorit
        </a>
        <a href="{{ url('/sampah') }}" class="sidebar-menu-item {{ ($activeMenu ?? 'drive') === 'sampah' ? 'active' : '' }}">
            <img src="{{ asset('images/sampah.png') }}" alt="Sampah">
            Sampah
        </a>
    </div>

    <!-- INDIKATOR PENYIMPANAN -->
    @php
        $usedStorage = \App\Models\FileItem::where('user_id', Auth::id())->sum('size');
        $quotaBytes  = 1 * 1024 * 1024 * 1024; // 1 GB
        $percentage  = ($usedStorage / $quotaBytes) * 100;
        if ($percentage > 100) $percentage = 100;

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
