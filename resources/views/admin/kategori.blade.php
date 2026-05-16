@php
    $categories = [
        ['id' => 1, 'nama' => 'Makanan', 'deskripsi' => 'Kategori untuk produk makanan ringan dan berat'],
        ['id' => 2, 'nama' => 'Minuman', 'deskripsi' => 'Kategori untuk berbagai jenis minuman'],
        ['id' => 3, 'nama' => 'Alat Tulis', 'deskripsi' => 'Kategori untuk perlengkapan kantor dan sekolah'],
        ['id' => 4, 'nama' => 'Kebutuhan Rumah Tangga', 'deskripsi' => 'Kategori untuk sabun, detergen, dan lainnya'],
    ];
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS System - Kategori Produk</title>
    <link rel="stylesheet" href="/css/admin.css">
    <script src="/js/admin.js" defer></script>
</head>

<body>
    <div class="app-container">
        @include('layout.admin-sidebar')

        <main class="main-content">
            @include('layout.admin-topbar', ['title' => 'Admin', 'subtitle' => 'Manajemen Kategori', 'user' => 'Dimsum (Admin)'])

            <div class="action-header">
                <div class="table-filters">
                    <input type="text" placeholder="Cari kategori...">
                </div>
                <div class="buttons">
                    <button class="btn-add" type="button" onclick="toggleModal('modalTambahKategori', true)">+ Tambah
                        Kategori</button>
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">No</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $index => $cat)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $cat['nama'] }}</strong></td>
                                <td>{{ $cat['deskripsi'] }}</td>
                                <td>
                                    <button class="btn-icon" type="button"
                                        onclick="openEditModal({{ $cat['id'] }}, '{{ $cat['nama'] }}', '{{ $cat['deskripsi'] }}')">Edit</button>
                                    <button class="btn-icon delete" type="button">Hapus</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>

        <!-- Modal Tambah Kategori -->
        <div id="modalTambahKategori" class="modal">
            <div class="modal-content modal-medium">
                <div class="modal-header">
                    <h2>Tambah Kategori Baru</h2>
                    <span class="close" onclick="toggleModal('modalTambahKategori', false)">&times;</span>
                </div>
                <form class="modal-form">
                    <div class="form-group full">
                        <label>Nama Kategori *</label>
                        <input type="text" placeholder="Contoh: Makanan">
                    </div>
                    <div class="form-group full">
                        <label>Deskripsi</label>
                        <textarea rows="3" placeholder="Penjelasan singkat kategori..."></textarea>
                    </div>
                    <div class="modal-footer-flex">
                        <button type="button" class="btn-batal-outline"
                            onclick="toggleModal('modalTambahKategori', false)">Batal</button>
                        <button type="submit" class="btn-simpan-full">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Kategori -->
        <div id="modalEditKategori" class="modal">
            <div class="modal-content modal-medium">
                <div class="modal-header">
                    <h2>Edit Kategori</h2>
                    <span class="close" onclick="toggleModal('modalEditKategori', false)">&times;</span>
                </div>
                <form class="modal-form">
                    <input type="hidden" id="edit-cat-id">
                    <div class="form-group full">
                        <label>Nama Kategori *</label>
                        <input type="text" id="edit-cat-nama">
                    </div>
                    <div class="form-group full">
                        <label>Deskripsi</label>
                        <textarea id="edit-cat-deskripsi" rows="3"></textarea>
                    </div>
                    <div class="modal-footer-flex">
                        <button type="button" class="btn-batal-outline"
                            onclick="toggleModal('modalEditKategori', false)">Batal</button>
                        <button type="submit" class="btn-simpan-full">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleModal(id, show) {
            const modal = document.getElementById(id);
            modal.style.display = show ? 'flex' : 'none';
        }

        function openEditModal(id, nama, deskripsi) {
            document.getElementById('edit-cat-id').value = id;
            document.getElementById('edit-cat-nama').value = nama;
            document.getElementById('edit-cat-deskripsi').value = deskripsi;
            toggleModal('modalEditKategori', true);
        }

        // Close modal when clicking outside
        window.onclick = function (event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>

</html>