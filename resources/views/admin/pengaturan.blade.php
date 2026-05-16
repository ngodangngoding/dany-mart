<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS System - Pengaturan</title>
    <link rel="stylesheet" href="/css/admin.css">
    <script src="/js/admin.js" defer></script>
</head>

<body>
    <div class="app-container">
        @include('layout.admin-sidebar')

        <main class="main-content">
            @include('layout.admin-topbar', ['title' => 'Pengaturan', 'subtitle' => 'Kelola konfigurasi toko dan keamanan akun', 'user' => 'Akbar Hidayat (Admin)'])

            <div class="settings-grid">
                <div class="settings-nav shadow-sm">
                    <div class="nav-item active" onclick="openTab(event, 'profil')">
                        <span class="nav-icon">TOKO</span>
                        <div class="nav-text">
                            <strong>Profil Toko</strong>
                            <br>
                            <small>Nama, alamat, dan kontak</small>
                        </div>
                    </div>
                    <div class="nav-item" onclick="openTab(event, 'akun')">
                        <span class="nav-icon">AKUN</span>
                        <div class="nav-text">
                            <strong>Manajemen Akun</strong>
                            <br>
                            <small>Password dan profil admin</small>
                        </div>
                    </div>
                    <div class="nav-item" onclick="openTab(event, 'sistem')">
                        <span class="nav-icon">SYS</span>
                        <div class="nav-text">
                            <strong>Sistem</strong>
                            <br>
                            <small>Pajak</small>
                        </div>
                    </div>
                </div>

                <div class="settings-content shadow-sm">
                    <div id="profil" class="tab-content active">
                        <div class="content-header">
                            <h3>Informasi Profil Toko</h3>
                            <p>Data ini akan muncul secara otomatis pada cetakan struk belanja.</p>
                        </div>
                        <form class="settings-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Nama Toko</label>
                                    <input type="text" value="Toko Altap" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>No. Telepon</label>
                                    <input type="text" value="08123456789" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Alamat Lengkap</label>
                                <textarea class="form-control"
                                    rows="3">Jl. Raya Merdeka No. 123, Jakarta Selatan</textarea>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn-save">Simpan Profil</button>
                            </div>
                        </form>
                    </div>

                    <div id="akun" class="tab-content">
                        <div class="content-header">
                            <h3>Manajemen Akun</h3>
                            <p>Perbarui informasi login dan kata sandi admin.</p>
                        </div>
                        <form class="settings-form">
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" value="Akbar Hidayat" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" value="admin_altap" class="form-control">
                            </div>
                            <hr class="divider">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Password Baru</label>
                                    <input type="password" placeholder="********" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Konfirmasi Password</label>
                                    <input type="password" placeholder="********" class="form-control">
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn-save">Update Keamanan</button>
                            </div>
                        </form>
                    </div>

                    <div id="sistem" class="tab-content">
                        <div class="content-header">
                            <h3>Konfigurasi Sistem</h3>
                            <p>Atur parameter teknis aplikasi kasir anda.</p>
                        </div>
                        <form class="settings-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>PPN (%)</label>
                                    <input type="number" value="11" class="form-control">
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn-save">Simpan Sistem</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>