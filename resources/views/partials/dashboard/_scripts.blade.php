<script>
    // === VIEW MODE TOGGLE ===
    function toggleViewMode(mode) {
        var gridEl  = document.querySelector('.grid');
        var headerEl = document.querySelector('.list-header');
        var btnList = document.getElementById('btn-view-list');
        var btnGrid = document.getElementById('btn-view-grid');
        if (!gridEl) return;
        if (mode === 'grid') {
            gridEl.classList.add('view-grid');
            if (headerEl) headerEl.classList.add('hidden-header');
            btnGrid.style.background = '#e2e8f0';
            btnGrid.style.color = '#1b5c96';
            btnList.style.background = 'transparent';
            btnList.style.color = '#64748b';
            localStorage.setItem('driveViewMode', 'grid');
        } else {
            gridEl.classList.remove('view-grid');
            if (headerEl) headerEl.classList.remove('hidden-header');
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
        var btn   = document.querySelector('.folder-popup-wrapper .sidebar-btn');
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
    var _sel     = new Map();
    var _lastIdx = -1;

    function _cards() { return Array.from(document.querySelectorAll('.item-card[data-id]')); }
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
        var bar       = document.getElementById('selection-bar');
        var filterBar = document.getElementById('filter-bar');
        var cnt       = document.getElementById('selected-count');
        var saBtn     = document.getElementById('selectAllBtn');
        if (_sel.size > 0) {
            bar.style.opacity = '1';
            bar.style.visibility = 'visible';
            if(filterBar) { filterBar.style.opacity = '0'; filterBar.style.visibility = 'hidden'; }
            cnt.textContent = _sel.size + ' dipilih';
            if (saBtn) {
                if (_sel.size === _cards().length) {
                    saBtn.classList.add('selected');
                } else {
                    saBtn.classList.remove('selected');
                }
            }
        } else {
            bar.style.opacity = '0';
            bar.style.visibility = 'hidden';
            if(filterBar) { filterBar.style.opacity = '1'; filterBar.style.visibility = 'visible'; }
            if (saBtn) saBtn.classList.remove('selected');
        }
    }

    var _forceDeleteCallback = null;

    function showForceDeleteConfirm(callback) {
        _forceDeleteCallback = callback;
        document.getElementById('forceDeleteModal').style.display = 'flex';
    }

    function closeForceDeleteModal() {
        document.getElementById('forceDeleteModal').style.display = 'none';
        _forceDeleteCallback = null;
    }

    function bulkAction(action) {
        if (_sel.size === 0) return;
        
        var executeBulk = function() {
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
        };

        if (action === 'force-delete') {
            showForceDeleteConfirm(executeBulk);
        } else {
            executeBulk();
        }
    }

    // Init selection on load
    document.addEventListener('DOMContentLoaded', function() {
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
            var newItemId   = "{{ session('new_item_id') }}";
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

<!-- MODAL KONFIRMASI HAPUS PERMANEN -->
<div id="forceDeleteModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:white; padding:24px; border-radius:8px; width:100%; max-width:400px; box-shadow:0 4px 6px rgba(0,0,0,0.1);">
        <h3 style="margin:0 0 12px 0; font-size:18px; color:#202124;">Hapus permanen?</h3>
        <p style="margin:0 0 24px 0; font-size:14px; color:#5f6368;">Tindakan ini tidak dapat diurungkan.</p>
        <div style="display:flex; justify-content:flex-end; gap:12px;">
            <button onclick="closeForceDeleteModal()" style="background:white; border:1px solid #dadce0; padding:8px 16px; border-radius:4px; color:#1a73e8; cursor:pointer; font-weight:500; font-size:14px; transition: background 0.2s;" onmouseover="this.style.background='#f1f3f4'" onmouseout="this.style.background='white'">Batal</button>
            <button id="btnConfirmForceDelete" style="background:#d93025; border:none; padding:8px 16px; border-radius:4px; color:white; cursor:pointer; font-weight:500; font-size:14px; transition: background 0.2s;" onmouseover="this.style.background='#b3261e'" onmouseout="this.style.background='#d93025'" onclick="if(_forceDeleteCallback) _forceDeleteCallback(); closeForceDeleteModal();">Hapus Permanen</button>
        </div>
    </div>
</div>
