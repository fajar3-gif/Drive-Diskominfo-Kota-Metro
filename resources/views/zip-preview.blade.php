<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $file->name }} - Pratinjau ZIP</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');
        
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #202124; /* Dark overlay Google Drive style */
            margin: 0; 
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .zip-modal {
            background-color: white;
            width: 90%;
            max-width: 950px;
            max-height: 80vh;
            border-radius: 4px;
            box-shadow: 0 24px 38px 3px rgba(0,0,0,0.14), 0 9px 46px 8px rgba(0,0,0,0.12), 0 11px 15px -7px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: modalIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes modalIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .zip-header {
            padding: 24px 30px 16px 30px;
            display: flex;
            align-items: baseline;
            gap: 12px;
        }

        .zip-title {
            font-size: 26px;
            font-weight: 400;
            color: #3c4043;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .zip-count {
            font-size: 16px;
            color: #5f6368;
            font-weight: 400;
        }

        .list-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            padding: 12px 30px;
            border-top: 1px solid #e0e0e0;
            border-bottom: 1px solid #e0e0e0;
            color: #70757a;
            font-size: 13px;
            font-weight: 500;
        }

        .list-body {
            flex: 1;
            overflow-y: auto;
            padding-bottom: 10px;
        }

        .list-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            padding: 12px 30px;
            align-items: center;
            border-bottom: 1px solid #f1f3f4;
            color: #3c4043;
            font-size: 14px;
        }
        
        .list-item:hover {
            background-color: #f8f9fa;
        }

        .item-name {
            display: flex;
            align-items: center;
            gap: 16px;
            font-weight: 400;
        }

        .item-icon {
            width: 20px;
            height: 20px;
            object-fit: contain;
            opacity: 0.6;
        }

        .item-detail {
            color: #5f6368;
            font-size: 13px;
        }

        .top-bar {
            position: absolute;
            top: 20px;
            right: 30px;
            display: flex;
            gap: 15px;
        }

        .top-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 4px;
            color: white;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }

        .top-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .empty-zip {
            padding: 40px;
            text-align: center;
            color: #5f6368;
            font-size: 15px;
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <a href="{{ url('/files/' . $file->id . '/download') }}" class="top-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Download ZIP
        </a>
        <button onclick="window.close()" class="top-btn" style="border: none; background: transparent; padding: 8px;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <div class="zip-modal">
        <div class="zip-header">
            <h1 class="zip-title">{{ $file->name }}</h1>
            <span class="zip-count">{{ count($zipContents) }} item</span>
        </div>
        
        <div class="list-header">
            <div>Nama</div>
            <div>Terakhir diubah</div>
            <div>Ukuran file</div>
        </div>

        <div class="list-body">
            @if(!empty($currentPath))
                @php
                    $parentPath = '';
                    $pathParts = explode('/', trim($currentPath, '/'));
                    if (count($pathParts) > 1) {
                        array_pop($pathParts);
                        $parentPath = implode('/', $pathParts) . '/';
                    }
                @endphp
                <a href="{{ url('/files/' . $file->id . '?path=' . urlencode($parentPath)) }}" class="list-item" style="text-decoration: none; display: flex; align-items: center; gap: 16px; border-bottom: 1px solid #f1f3f4; padding: 12px 30px; color: #3c4043; font-weight: 500;">
                    <span>Kembali</span>
                </a>
            @endif

            @if(count($zipContents) > 0)
                @foreach($zipContents as $item)
                    <div class="list-item">
                        <div class="item-name">
                            @if($item['is_folder'])
                                <img src="{{ asset('images/ikon-folder.png') }}" class="item-icon" alt="Folder" style="opacity: 0.8;">
                                <a href="{{ url('/files/' . $file->id . '?path=' . urlencode($item['full_path'])) }}" style="text-decoration: none; color: #1a73e8; font-weight: 500;">{{ $item['name'] }}</a>
                            @else
                                <span style="font-size: 18px; margin-right: 4px; color: #5f6368;">📄</span>
                                <span>{{ $item['name'] }}</span>
                            @endif
                        </div>
                        <div class="item-detail">
                            {{ $item['mtime'] ? date('d/m/Y, H.i', $item['mtime']) : '-' }}
                        </div>
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
