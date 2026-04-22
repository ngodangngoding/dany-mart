<aside id="dashboard-sidebar" class="dashboard-sidebar w-full p-6 lg:w-[280px]">
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-kasir-primary text-white">
                <svg class="h-6 w-6" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M4.5 5.5h11l-1 6.5H6L4.5 5.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" />
                    <path d="M4.5 5.5 4 3.5H2.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                    <path d="M7.25 15.5a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5ZM13 15.5a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5Z" fill="currentColor" />
                </svg>
            </div>
            <div>
                <h1 class="text-lg font-bold text-kasir-primary">Pos System</h1>
                <p class="text-xs text-slate-400">Sistem kasir</p>
            </div>
        </div>

        <button
            type="button"
            data-sidebar-toggle
            data-target="dashboard-sidebar"
            aria-label="Collapse sidebar"
            aria-pressed="true"
            class="dashboard-collapse-button"
        >
            <span class="text-sm font-semibold">&lt;&lt;</span>
        </button>
    </div>

    <nav class="mt-10 space-y-2">
        <a href="{{ route('kasir.dashboard') }}" class="dashboard-nav-link {{ request()->routeIs('kasir.dashboard') || request()->routeIs('kasir.index') ? 'dashboard-nav-link-active' : 'text-slate-700' }}">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M4 5.5A1.5 1.5 0 0 1 5.5 4h9A1.5 1.5 0 0 1 16 5.5v9a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 4 14.5v-9Z" stroke="currentColor" stroke-width="1.5" />
                <path d="M7 8h6M7 12h3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            <span>Kasir</span>
        </a>

        <a href="{{ route('kasir.history') ?? '/kasir/history' }}" class="dashboard-nav-link {{ request()->routeIs('kasir.history') ? 'dashboard-nav-link-active' : 'text-slate-700' }} w-full text-left">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M5.5 4.5h9A1.5 1.5 0 0 1 16 6v8a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 4 14V6a1.5 1.5 0 0 1 1.5-1.5Z" stroke="currentColor" stroke-width="1.5" />
                <path d="M7 8h6M7 10.75h6M7 13.5h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            <span>Riwayat Transaksi</span>
        </a>
    </nav>

    <div class="mt-auto pt-8">
        <button type="button" class="dashboard-nav-link w-full text-left text-rose-500 hover:bg-rose-50 hover:text-rose-600">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M8 5.5H6.5A1.5 1.5 0 0 0 5 7v6a1.5 1.5 0 0 0 1.5 1.5H8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                <path d="M11.5 7 14.5 10 11.5 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M14 10H8.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            <span>Keluar</span>
        </button>
    </div>
</aside>
