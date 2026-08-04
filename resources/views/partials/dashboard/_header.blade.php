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
