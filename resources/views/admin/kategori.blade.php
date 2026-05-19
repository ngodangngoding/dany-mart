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

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="action-header">
                <form class="table-filters" method="GET" action="{{ route('admin.kategori.index') }}">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari kategori...">
                    <button class="btn-excel" type="submit">Cari</button>
                    @if ($search !== '')
                        <a class="btn-reset-link" href="{{ route('admin.kategori.index') }}">Reset</a>
                    @endif
                </form>
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
                            <th>Kode</th>
                            <th style="width: 130px;">Jumlah Produk</th>
                            <th style="width: 170px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{ $categories->firstItem() + $loop->index }}</td>
                                <td><strong>{{ $category->name }}</strong></td>
                                <td>{{ $category->code }}</td>
                                <td>{{ $category->products_count }}</td>
                                <td>
                                    <button class="btn-icon" type="button"
                                        onclick="openEditModal('{{ route('admin.kategori.update', $category) }}', @js($category->name))">Edit</button>
                                    <form class="inline-form" method="POST"
                                        action="{{ route('admin.kategori.destroy', $category) }}"
                                        onsubmit="return confirm('Hapus kategori {{ $category->name }}? Produk di kategori ini juga ikut terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-icon delete" type="submit">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-table">Belum ada kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <div class="table-footer-info">
                    {{ $categories->links() }}
                </div>
            @endif
        </main>

        <div id="modalTambahKategori" class="modal">
            <div class="modal-content modal-medium">
                <div class="modal-header">
                    <h2>Tambah Kategori Baru</h2>
                    <span class="close" onclick="toggleModal('modalTambahKategori', false)">&times;</span>
                </div>
                <form class="modal-form" method="POST" action="{{ route('admin.kategori.store') }}">
                    @csrf
                    <div class="form-group full">
                        <label for="category-name">Nama Kategori *</label>
                        <input id="category-name" name="name" type="text" value="{{ old('name') }}"
                            placeholder="Contoh: Makanan" required>
                    </div>
                    <div class="modal-footer-flex">
                        <button type="button" class="btn-batal-outline"
                            onclick="toggleModal('modalTambahKategori', false)">Batal</button>
                        <button type="submit" class="btn-simpan-full">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="modalEditKategori" class="modal">
            <div class="modal-content modal-medium">
                <div class="modal-header">
                    <h2>Edit Kategori</h2>
                    <span class="close" onclick="toggleModal('modalEditKategori', false)">&times;</span>
                </div>
                <form id="formEditKategori" class="modal-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group full">
                        <label for="edit-cat-nama">Nama Kategori *</label>
                        <input type="text" id="edit-cat-nama" name="name" required>
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

        function openEditModal(action, nama) {
            document.getElementById('formEditKategori').action = action;
            document.getElementById('edit-cat-nama').value = nama;
            toggleModal('modalEditKategori', true);
        }

        window.onclick = function (event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>

</html>
