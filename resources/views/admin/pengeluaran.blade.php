<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>POS System - Pengeluaran Toko</title>
        <link rel="stylesheet" href="/css/admin.css">
        <script src="/js/admin.js" defer></script>
    </head>
    <body>
        <div class="app-container">
            @include('layout.admin-sidebar')

            <main class="main-content">
                @include('layout.admin-topbar', ['title' => 'Pengeluaran Toko', 'subtitle' => 'Catat dan kelola pengeluaran operasional', 'user' => 'Akbar Hidayat (Admin)'])

                <div class="action-header">
                    <div class="left-actions"></div>
                    <div class="right-buttons">
                        <button class="btn-outline-print" type="button">Cetak Pengeluaran</button>
                        <button class="btn-add-primary" type="button" onclick="toggleModal('modalPengeluaran', true)">+ Tambah Pengeluaran</button>
                    </div>
                </div>

                <div class="expense-banner">
                    <div class="banner-info">
                        <span>Total pengeluaran</span>
                        <h2>Rp 9.600.000</h2>
                    </div>
                    <div class="banner-count">
                        <strong>4 Pengeluaran tercatat</strong>
                    </div>
                </div>

                <div class="table-tools">
                    <div class="filter-group">
                        <select class="form-select-sm">
                            <option>Semua kategori</option>
                            <option>Listrik</option>
                            <option>Gaji</option>
                            <option>Perlengkapan</option>
                            <option>Sewa</option>
                        </select>
                        <button class="btn-filter-date" type="button">Filter periode</button>
                    </div>
                </div>

                <div class="table-container shadow-sm">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kategori pengeluaran</th>
                                <th>Deskripsi</th>
                                <th>Nominal</th>
                                <th>Pengguna</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>22/12/2025 12:00</td>
                                <td><span class="lbl-cat orange">Listrik</span></td>
                                <td>Bayar listrik bulan desember</td>
                                <td class="text-red">Rp 850.000</td>
                                <td>Owner</td>
                                <td>
                                    <button class="btn-icon" type="button">Edit</button>
                                    <button class="btn-icon delete" type="button">Hapus</button>
                                </td>
                            </tr>
                            <tr>
                                <td>22/12/2025 12:00</td>
                                <td><span class="lbl-cat green">Gaji</span></td>
                                <td>Gaji mingguan karyawan 1-15 oktober</td>
                                <td class="text-red">Rp 3.500.000</td>
                                <td>Owner</td>
                                <td>
                                    <button class="btn-icon" type="button">Edit</button>
                                    <button class="btn-icon delete" type="button">Hapus</button>
                                </td>
                            </tr>
                            <tr>
                                <td>22/12/2025 12:00</td>
                                <td><span class="lbl-cat purple">Perlengkapan</span></td>
                                <td>Beli kantong plastik</td>
                                <td class="text-red">Rp 250.000</td>
                                <td>Owner</td>
                                <td>
                                    <button class="btn-icon" type="button">Edit</button>
                                    <button class="btn-icon delete" type="button">Hapus</button>
                                </td>
                            </tr>
                            <tr>
                                <td>22/12/2025 12:00</td>
                                <td><span class="lbl-cat blue">Sewa</span></td>
                                <td>Sewa toko bulan oktober</td>
                                <td class="text-red">Rp 5.000.000</td>
                                <td>Owner</td>
                                <td>
                                    <button class="btn-icon" type="button">Edit</button>
                                    <button class="btn-icon delete" type="button">Hapus</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="table-footer-info">Menampilkan 1 - 4 dari 4 data</p>
            </main>
        </div>

        <div id="modalPengeluaran" class="modal">
            <div class="modal-content modal-medium">
                <div class="modal-header">
                    <div class="modal-title-section">
                        <h2>Tambah Pengeluaran Baru</h2>
                        <p>Catat pengeluaran operasional toko</p>
                    </div>
                    <span class="close" onclick="toggleModal('modalPengeluaran', false)">&times;</span>
                </div>

                <form class="modal-form">
                    <div class="form-group full">
                        <label>Tanggal *</label>
                        <input type="date" value="{{ now()->toDateString() }}" class="form-input">
                    </div>
                    <div class="form-group full">
                        <label>Kategori *</label>
                        <select class="form-input">
                            <option value="">Pilih kategori</option>
                            <option value="listrik">Listrik</option>
                            <option value="gaji">Gaji</option>
                            <option value="perlengkapan">Perlengkapan</option>
                            <option value="sewa">Sewa</option>
                        </select>
                    </div>
                    <div class="form-group full">
                        <label>Deskripsi (opsional)</label>
                        <textarea placeholder="Deskripsi singkat barang (opsional)" rows="3" class="form-input"></textarea>
                    </div>
                    <div class="form-group full">
                        <label>Nominal pengeluaran (Rp) *</label>
                        <input type="number" placeholder="Rp 0" class="form-input">
                    </div>
                    <div class="modal-footer-flex">
                        <button type="button" class="btn-batal-outline" onclick="toggleModal('modalPengeluaran', false)">Batal</button>
                        <button type="submit" class="btn-simpan-full">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
