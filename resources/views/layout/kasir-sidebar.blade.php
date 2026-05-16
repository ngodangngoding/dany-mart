<aside class="sidebar">
    <div class="brand-row">
        <div class="brand-lockup">
            <div class="brand-icon">
                <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M4.5 5.5h11l-1 6.5H6L4.5 5.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"></path>
                    <path d="M4.5 5.5 4 3.5H2.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"></path>
                    <path d="M7.25 15.5a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5ZM13 15.5a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5Z" fill="currentColor"></path>
                </svg>
            </div>
            <div>
                <h1 class="brand-title">Pos System</h1>
                <p class="brand-subtitle">Sistem kasir</p>
            </div>
        </div>
    </div>

    <nav class="nav-list" aria-label="Menu kasir">
        <a href="{{ route('kasir.dashboard') }}" class="nav-link {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M4 5.5A1.5 1.5 0 0 1 5.5 4h9A1.5 1.5 0 0 1 16 5.5v9a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 4 14.5v-9Z" stroke="currentColor" stroke-width="1.5"></path>
                <path d="M7 8h6M7 12h3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
            </svg>
            <span>Kasir</span>
        </a>
        <a href="{{ route('kasir.history') }}" class="nav-link {{ request()->routeIs('kasir.history') ? 'active' : '' }}">
            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M5.5 4.5h9A1.5 1.5 0 0 1 16 6v8a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 4 14V6a1.5 1.5 0 0 1 1.5-1.5Z" stroke="currentColor" stroke-width="1.5"></path>
                <path d="M7 8h6M7 10.75h6M7 13.5h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
            </svg>
            <span>Riwayat Transaksi</span>
        </a>
    </nav>

    <form method="POST" action="{{ route('logout.preview') }}" class="logout-form">
        @csrf
        <button type="submit" class="nav-link logout-button">
            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M8 5.5H6.5A1.5 1.5 0 0 0 5 7v6a1.5 1.5 0 0 0 1.5 1.5H8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                <path d="M11.5 7 14.5 10 11.5 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M14 10H8.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
            </svg>
            <span>Keluar</span>
        </button>
    </form>
</aside>
