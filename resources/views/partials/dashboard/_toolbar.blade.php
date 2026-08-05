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
                        'today'   => 'Hari ini',
                        '7days'   => '7 hari terakhir',
                        '30days'  => '30 hari terakhir',
                        default   => 'Dimodifikasi'
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
        <button onclick="clearSelection()" style="background:none;border:none;cursor:pointer;width:28px;height:28px;border-radius:4px;display:flex;align-items:center;justify-content:center;" title="Batalkan pilihan" onmouseover="this.style.background='rgba(60,64,67,0.10)'" onmouseout="this.style.background='none'">
            <img src="{{ asset('images/close.png') }}" style="width:14px;height:14px;opacity:0.65;">
        </button>
        <span id="selected-count" style="font-weight:500;color:#3c4043;font-size:14px;margin-right:8px;">0 dipilih</span>
        <div style="width:1px;height:16px;background:#dadce0;margin-right:8px;"></div>
        @if(request()->is('sampah') || request('source') === 'sampah')
            <button onclick="bulkAction('restore')" title="Pulihkan" style="background:none;border:none;cursor:pointer;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;" onmouseover="this.style.background='rgba(60,64,67,0.08)'" onmouseout="this.style.background='none'">
                <img src="{{ asset('images/pulihkan.png') }}" style="width: 16px; height: 16px; opacity: 0.7;">
            </button>
            <button onclick="bulkAction('force-delete')" title="Hapus Permanen" style="background:none;border:none;cursor:pointer;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;" onmouseover="this.style.background='rgba(60,64,67,0.08)'" onmouseout="this.style.background='none'">
                <img src="{{ asset('images/sampah.png') }}" style="width: 16px; height: 16px; opacity: 0.7; filter: hue-rotate(320deg) saturate(3) brightness(0.9);">
            </button>
        @else
            <button onclick="bulkAction('download')" title="Download" style="background:none;border:none;cursor:pointer;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;" onmouseover="this.style.background='rgba(60,64,67,0.08)'" onmouseout="this.style.background='none'">
                <img src="{{ asset('images/download.png') }}" style="width: 16px; height: 16px; opacity: 0.7;">
            </button>
            <button onclick="bulkAction('trash')" title="Hapus" style="background:none;border:none;cursor:pointer;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;" onmouseover="this.style.background='rgba(60,64,67,0.08)'" onmouseout="this.style.background='none'">
                <img src="{{ asset('images/sampah.png') }}" style="width: 16px; height: 16px; opacity: 0.7;">
            </button>
        @endif
    </div>
</div>
