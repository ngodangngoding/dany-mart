@php
    $accounts = [
        ['name' => 'Akbar Hidayat', 'username' => 'admin_altap', 'email' => 'admin@danymart.com', 'role' => 'Admin', 'status' => 'Aktif', 'initial' => 'A'],
        ['name' => 'Kasir Utama', 'username' => 'kasir', 'email' => 'kasir@danymart.com', 'role' => 'Kasir', 'status' => 'Aktif', 'initial' => 'K'],
        ['name' => 'Owner Toko', 'username' => 'owner', 'email' => 'owner@danymart.com', 'role' => 'Owner', 'status' => 'Aktif', 'initial' => 'O'],
    ];
@endphp

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>POS System - Manajemen Akun</title>
        <link rel="stylesheet" href="/css/admin.css">
        <script src="/js/admin.js" defer></script>
    </head>
    <body>
        <div class="app-container">
            @include('layout.admin-sidebar')

            <main class="main-content">
                @include('layout.admin-topbar', ['title' => 'Manajemen Akun', 'subtitle' => 'Kelola akun admin, owner, dan kasir', 'user' => 'Akbar Hidayat (Admin)'])

                <div class="action-header">
                    <div class="table-filters">
                        <select>
                            <option>Semua role</option>
                            <option>Admin</option>
                            <option>Owner</option>
                            <option>Kasir</option>
                        </select>
                        <input type="text" placeholder="Cari nama, username, atau email...">
                    </div>
                    <div class="buttons">
                        <button class="btn-add" type="button" onclick="toggleModal('modalAkun', true)">+ Tambah Akun</button>
                    </div>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($accounts as $account)
                                <tr>
                                    <td>
                                        <div class="account-avatar">{{ $account['initial'] }}</div>
                                    </td>
                                    <td class="fw-bold">{{ $account['name'] }}</td>
                                    <td>{{ $account['username'] }}</td>
                                    <td>{{ $account['email'] }}</td>
                                    <td><span class="badge-role">{{ $account['role'] }}</span></td>
                                    <td>
                                        <button class="btn-icon" type="button" onclick="toggleModal('modalEditAkun', true)">Edit</button>
                                        <button class="btn-icon delete" type="button" onclick="toggleModal('modalHapusAkun', true)">Hapus</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </main>
        </div>

        <div id="modalAkun" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title-section">
                        <h2>Tambah Akun Baru</h2>
                        <p>Buat akun untuk admin, owner, atau kasir.</p>
                    </div>
                    <span class="close" onclick="toggleModal('modalAkun', false)">&times;</span>
                </div>
                <form class="modal-form">
                    <div class="form-group full photo-upload-row">
                        <img id="account-photo-preview" class="account-photo-preview" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 96 96'%3E%3Crect width='96' height='96' rx='48' fill='%23eef9f1'/%3E%3Ctext x='48' y='56' text-anchor='middle' font-size='28' font-family='Arial' fill='%2371a32a'%3EIMG%3C/text%3E%3C/svg%3E" alt="Preview photo">
                        <div>
                            <label>Photo akun</label>
                            <input type="file" accept="image/*" data-photo-input="#account-photo-preview">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nama lengkap *</label>
                        <input type="text" placeholder="Nama lengkap">
                    </div>
                    <div class="form-group">
                        <label>Username *</label>
                        <input type="text" placeholder="username">
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" placeholder="email@domain.com">
                    </div>
                    <div class="form-group">
                        <label>Role *</label>
                        <select>
                            <option>Admin</option>
                            <option>Owner</option>
                            <option>Kasir</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Password *</label>
                        <input type="password" placeholder="********">
                    </div>
                    </div>
                    <div class="modal-footer full">
                        <button type="button" class="btn-batal" onclick="toggleModal('modalAkun', false)">Batal</button>
                        <button type="submit" class="btn-simpan">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="modalEditAkun" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title-section">
                        <h2>Edit Akun</h2>
                        <p>Perbarui detail akun pengguna.</p>
                    </div>
                    <span class="close" onclick="toggleModal('modalEditAkun', false)">&times;</span>
                </div>
                <form class="modal-form">
                    <div class="form-group full photo-upload-row">
                        <img id="edit-account-photo-preview" class="account-photo-preview" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 96 96'%3E%3Crect width='96' height='96' rx='48' fill='%23eef9f1'/%3E%3Ctext x='48' y='56' text-anchor='middle' font-size='28' font-family='Arial' fill='%2371a32a'%3EK%3C/text%3E%3C/svg%3E" alt="Preview photo">
                        <div>
                            <label>Ganti photo</label>
                            <input type="file" accept="image/*" data-photo-input="#edit-account-photo-preview">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nama lengkap *</label>
                        <input type="text" value="Kasir Utama">
                    </div>
                    <div class="form-group">
                        <label>Username *</label>
                        <input type="text" value="kasir">
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" value="kasir@danymart.com">
                    </div>
                    <div class="form-group">
                        <label>Role *</label>
                        <select>
                            <option>Kasir</option>
                            <option>Admin</option>
                            <option>Owner</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Password baru</label>
                        <input type="password" placeholder="Kosongkan jika tidak diganti">
                    </div>
                    <div class="modal-footer full">
                        <button type="button" class="btn-batal" onclick="toggleModal('modalEditAkun', false)">Batal</button>
                        <button type="submit" class="btn-simpan">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="modalHapusAkun" class="modal">
            <div class="modal-content modal-medium">
                <div class="modal-header">
                    <div class="modal-title-section">
                        <h2>Hapus Akun</h2>
                        <p>Akun yang dihapus tidak akan tampil pada daftar pengguna.</p>
                    </div>
                    <span class="close" onclick="toggleModal('modalHapusAkun', false)">&times;</span>
                </div>
                <div class="delete-confirmation">
                    <p>Apakah kamu yakin ingin menghapus akun ini?</p>
                    <div class="modal-footer-flex">
                        <button type="button" class="btn-batal-outline" onclick="toggleModal('modalHapusAkun', false)">Batal</button>
                        <button type="button" class="btn-delete-full" onclick="toggleModal('modalHapusAkun', false)">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
