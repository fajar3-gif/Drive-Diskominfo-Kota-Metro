<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $file->name }} - Pratinjau ZIP</title>
    <link rel="stylesheet" href="{{ asset('css/zip-preview.css') }}">
</head>
<body>

    {{-- Tombol Download & Tutup (pojok kanan atas) --}}
    <div class="top-bar">
        <a href="{{ url('/files/' . $file->id . '/download') }}" class="top-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Download ZIP
        </a>
        <button onclick="window.close()" class="top-btn" style="border: none; background: transparent; padding: 8px;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    {{-- Modal Konten ZIP --}}
    <div class="zip-modal">

        {{-- Header: Nama File & Jumlah Item --}}
        <div class="zip-header">
            <h1 class="zip-title">{{ $file->name }}</h1>
            <span class="zip-count">{{ count($zipContents) }} item</span>
        </div>

        {{-- Header Kolom Tabel --}}
        <div class="list-header">
            <div>Nama</div>
            <div>Terakhir diubah</div>
            <div>Ukuran file</div>
        </div>

        {{-- Daftar Isi ZIP --}}
        <div class="list-body">

            {{-- Tombol Kembali (jika sedang di dalam subfolder) --}}
            @if(!empty($currentPath))
                @php
                    $parentPath = '';
                    $pathParts = explode('/', trim($currentPath, '/'));
                    if (count($pathParts) > 1) {
                        array_pop($pathParts);
                        $parentPath = implode('/', $pathParts) . '/';
                    }
                @endphp
                <a href="{{ url('/files/' . $file->id . '?path=' . urlencode($parentPath)) }}"
                   class="list-item"
                   style="text-decoration: none; display: flex; align-items: center; gap: 16px; border-bottom: 1px solid #f1f3f4; padding: 12px 30px; color: #3c4043; font-weight: 500;">
                    <span>← Kembali</span>
                </a>
            @endif

            {{-- Loop Item ZIP --}}
            @if(count($zipContents) > 0)
                @foreach($zipContents as $item)
                    <div class="list-item">

                        {{-- Kolom 1: Nama & Ikon --}}
                        <div class="item-name">
                            @if($item['is_folder'])
                                <img src="{{ asset('images/ikon-folder.png') }}" class="item-icon" alt="Folder" style="opacity: 0.8;">
                                <a href="{{ url('/files/' . $file->id . '?path=' . urlencode($item['full_path'])) }}"
                                   style="text-decoration: none; color: #1a73e8; font-weight: 500;">{{ $item['name'] }}</a>
                            @else
                                @php
                                    $ext = strtolower(pathinfo($item['name'], PATHINFO_EXTENSION));
                                    $iconMap = [
                                        'pdf'  => 'pdf.png',
                                        'doc'  => 'doc.png', 'docx' => 'doc.png',
                                        'zip'  => 'zip.png',
                                        'mp4'  => 'video.png', 'mkv' => 'video.png', 'avi' => 'video.png',
                                        'jpg'  => 'image.png', 'jpeg' => 'image.png', 'png' => 'image.png',
                                        'gif'  => 'image.png', 'webp' => 'image.png', 'svg' => 'image.png',
                                        'ppt'  => 'ppt.png',  'pptx' => 'ppt.png',
                                    ];
                                    $iconFile = $iconMap[$ext] ?? 'file.png';
                                @endphp
                                <img src="{{ asset('images/' . $iconFile) }}" alt="{{ strtoupper($ext) }}" style="width: 20px; height: 20px; object-fit: contain; margin-right: 4px;">
                                <span>{{ $item['name'] }}</span>
                            @endif
                        </div>

                        {{-- Kolom 2: Tanggal Modifikasi --}}
                        <div class="item-detail">
                            {{ $item['mtime'] ? date('d/m/Y, H.i', $item['mtime']) : '-' }}
                        </div>

                        {{-- Kolom 3: Ukuran File --}}
                        <div class="item-detail">
                            @php
                                if ($item['size'] === '-') {
                                    echo '-';
                                } else {
                                    $sizeInBytes = $item['size'];
                                    if ($sizeInBytes >= 1048576) {
                                        echo number_format($sizeInBytes / 1048576, 2) . ' MB';
                                    } elseif ($sizeInBytes >= 1024) {
                                        echo number_format($sizeInBytes / 1024, 2) . ' KB';
                                    } else {
                                        echo $sizeInBytes . ' B';
                                    }
                                }
                            @endphp
                        </div>

                    </div>
                @endforeach
            @else
                <div class="empty-zip">File ZIP ini kosong.</div>
            @endif

        </div>
    </div>

</body>
</html>
