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
    <div></div>
</div>

<!-- CONTAINER DAFTAR ITEM -->
<div class="grid">

    @foreach ($folders as $folder)
        <div class="item-card" data-id="{{ $folder->id }}" data-type="folder" data-url="{{ url('/folder/show/' . $folder->id) }}{{ isset($activeMenu) && $activeMenu !== 'drive' ? '?source=' . $activeMenu : '' }}">

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
            <div class="item-details" {!! ($activeMenu ?? 'drive') === 'favorit' ? 'style="display: flex; align-items: center; gap: 8px;"' : '' !!}>
                <span>
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
                </span>
                @if(($activeMenu ?? 'drive') === 'favorit')
                    <img src="{{ asset('images/dibintangi.png') }}" alt="Favorit" style="width: 16px; height: 16px;">
                @endif
            </div>

            <!-- Kolom 5: Menu Action -->
            <div class="dropdown">
                <button onclick="toggleDropdown('folder-{{ $folder->id }}')" class="dropbtn">⋮</button>
                <div id="folder-{{ $folder->id }}" class="dropdown-content">
                    @if(isset($activeMenu) && $activeMenu === 'sampah')
                        <form action="{{ url('/sampah/folder/'.$folder->id.'/restore') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit">Pulihkan</button>
                        </form>
                        <form action="{{ url('/sampah/folder/'.$folder->id.'/force-delete') }}" method="POST" style="margin: 0;" onsubmit="event.preventDefault(); var f = this; showForceDeleteConfirm(function(){ f.submit(); });">
                            @csrf
                            <button type="submit" style="color: red;">Hapus Permanen</button>
                        </form>
                    @else
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
                    @endif
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
            <div class="item-details">{{ pathinfo($file->name, PATHINFO_EXTENSION) ? strtoupper(pathinfo($file->name, PATHINFO_EXTENSION)) . ' File' : 'File' }}</div>

            <!-- Kolom 4: Ukuran -->
            <div class="item-details" {!! ($activeMenu ?? 'drive') === 'favorit' ? 'style="display: flex; align-items: center; gap: 8px;"' : '' !!}>
                <span>
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
                </span>
                @if(($activeMenu ?? 'drive') === 'favorit')
                    <img src="{{ asset('images/dibintangi.png') }}" alt="Favorit" style="width: 16px; height: 16px;">
                @endif
            </div>

            <!-- Kolom 5: Menu Action -->
            <div class="dropdown">
                <button onclick="toggleDropdown('file-{{ $file->id }}')" class="dropbtn">⋮</button>
                <div id="file-{{ $file->id }}" class="dropdown-content">
                    @if(isset($activeMenu) && $activeMenu === 'sampah')
                        <form action="{{ url('/sampah/file/'.$file->id.'/restore') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit">Pulihkan</button>
                        </form>
                        <form action="{{ url('/sampah/file/'.$file->id.'/force-delete') }}" method="POST" style="margin: 0;" onsubmit="event.preventDefault(); var f = this; showForceDeleteConfirm(function(){ f.submit(); });">
                            @csrf
                            <button type="submit" style="color: red;">Hapus Permanen</button>
                        </form>
                    @else
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
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    <!-- PESAN JIKA KOSONG -->
    @if ($folders->isEmpty() && $files->isEmpty())
        <p class="empty-text">Tidak ada item yang ditemukan.</p>
    @endif

</div>
