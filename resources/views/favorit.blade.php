<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Drive - Favorit</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>

<body>

    @include('partials.dashboard._header')

    <div class="main-container">

        @include('partials.dashboard._sidebar', ['activeMenu' => 'favorit'])

        <div class="main-content">

            {{-- TOAST NOTIFICATION --}}
            @if(session('success') || session('error'))
                <div id="toast-notification" style="position: fixed; bottom: 30px; right: 30px; background: white; border-bottom: 1px solid #e2e8f0; border-top: none; border-left: none; border-right: none; border-radius: 0; padding: 16px 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 15px; z-index: 9999; animation: slideUp 0.3s ease-out; font-size: 14px; font-weight: 500; color: #1e293b; min-width: 300px; border-left: 4px solid {{ session('success') ? '#10b981' : '#ef4444' }};">
                    <img src="{{ session('success') ? asset('images/ceklis.png') : asset('images/silang.png') }}" alt="Ikon" style="width: 24px; height: 24px; object-fit: contain;">
                    <span>{{ session('success') ?? session('error') }}</span>
                    <button onclick="document.getElementById('toast-notification').style.display='none'" style="margin-left: auto; background: none; border: none; font-size: 20px; cursor: pointer; color: #94a3b8; padding: 0;">&times;</button>
                </div>
                <style>
                    @@keyframes slideUp {
                        from { transform: translateY(100px); opacity: 0; }
                        to   { transform: translateY(0); opacity: 1; }
                    }
                </style>
            @endif

            {{-- JUDUL HALAMAN UTAMA --}}
            <div style="display: flex; align-items: center; gap: 0; margin-bottom: 14px; overflow: hidden; white-space: nowrap; min-height: 32px;">
                <span style="flex-shrink: 0; font-size: 20px; font-weight: 600; color: #202124; padding: 4px 6px;">Favorit</span>
            </div>

            {{-- TOOLBAR: Filter, Selection Bar, View Toggle --}}
            @include('partials.dashboard._toolbar')

            {{-- DAFTAR FOLDER & FILE --}}
            @include('partials.dashboard._file-list', ['activeMenu' => 'favorit'])

        </div>
    </div>

    @include('partials.dashboard._scripts')

</body>
</html>
