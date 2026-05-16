<aside class="sidebar">
    <div class="brand">
        <div class="icon-cart">POS</div>
        <div>
            <strong>Pos System</strong>
            <br>
            <small>Sistem Admin</small>
        </div>
    </div>

    <nav class="menu" aria-label="Menu admin">
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Kasir</a>
        <a href="{{ route('admin.barang') }}" class="{{ request()->routeIs('admin.barang') ? 'active' : '' }}">Barang & Stok</a>
        <a href="{{ route('admin.kategori') }}" class="{{ request()->routeIs('admin.kategori') ? 'active' : '' }}">Kategori</a>
        <a href="{{ route('admin.riwayat') }}" class="{{ request()->routeIs('admin.riwayat') ? 'active' : '' }}">Riwayat Transaksi</a>
        <a href="{{ route('admin.laporan') }}" class="{{ request()->routeIs('admin.laporan') ? 'active' : '' }}">Laporan</a>
        <a href="{{ route('admin.pengeluaran') }}" class="{{ request()->routeIs('admin.pengeluaran') ? 'active' : '' }}">Pengeluaran</a>
        <a href="{{ route('admin.manajemen-akun') }}" class="{{ request()->routeIs('admin.manajemen-akun') ? 'active' : '' }}">Manajemen Akun</a>
        <a href="{{ route('admin.pengaturan') }}" class="{{ request()->routeIs('admin.pengaturan') ? 'active' : '' }}">Pengaturan</a>
    </nav>

    <form method="POST" action="{{ route('logout.preview') }}" class="logout">
        @csrf
        <button type="submit">Keluar</button>
    </form>
</aside>
