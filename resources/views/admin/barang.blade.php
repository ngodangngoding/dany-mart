<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>POS System - Stok Barang</title>
        <link rel="stylesheet" href="/css/admin.css">
        <script src="/js/admin.js" defer></script>
    </head>
    <body>
        <div class="app-container">
            @include('layout.admin-sidebar')

            <main class="main-content">
                @include('layout.admin-topbar', ['title' => 'Admin', 'subtitle' => 'Toko Altap', 'user' => 'Dimsum (Admin)'])

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">{{ $errors->first() }}</div>
                @endif

                <div class="action-header">
                    <form class="table-filters" method="GET" action="{{ route('admin.barang') }}">
                        <select name="category_id">
                            <option value="">Semua kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ (string) $categoryId === (string) $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau kode barang...">
                        <button class="btn-excel" type="submit">Cari</button>
                        @if ($search !== '' || $categoryId)
                            <a class="btn-reset-link" href="{{ route('admin.barang') }}">Reset</a>
                        @endif
                    </form>
                    <div class="buttons">
                        <button class="btn-add" type="button" onclick="toggleModal('modalTambah', true)">+ Tambah Barang</button>
                    </div>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kode barang</th>
                                <th>Nama barang</th>
                                <th>Kategori</th>
                                <th>Satuan</th>
                                <th>Harga beli</th>
                                <th>Harga jual</th>
                                <th>Stok</th>
                                <th style="width: 240px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr>
                                    <td>{{ $product->code }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category ? $product->category->name : '-' }}</td>
                                    <td>{{ $product->unit }}</td>
                                    <td>Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>
                                        <button class="btn-icon stock" type="button"
                                            onclick="openProductStockModal('{{ route('admin.barang.add-stock', $product) }}', @js($product->code), @js($product->name), {{ $product->stock }})">Stok</button>
                                        <button class="btn-icon" type="button"
                                            onclick="openEditModal('{{ route('admin.barang.update', $product) }}', {{ $product->category_id }}, @js($product->name), @js($product->unit), {{ $product->purchase_price }}, {{ $product->selling_price }}, {{ $product->stock }})">Edit</button>
                                        <button class="btn-icon" type="button"
                                            onclick="openHistoryModal('{{ route('admin.barang.stock-histories', $product) }}')">Riwayat</button>
                                        <form class="inline-form" method="POST" action="{{ route('admin.barang.destroy', $product) }}"
                                            onsubmit="return confirm('Hapus barang {{ $product->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-icon delete" type="submit">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="empty-table">Belum ada barang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($products->hasPages())
                    <div class="table-footer-info">
                        {{ $products->links() }}
                    </div>
                @endif
            </main>

            <div id="modalTambah" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Tambah Barang Baru</h2>
                        <span class="close" onclick="toggleModal('modalTambah', false)">&times;</span>
                    </div>
                    <form class="modal-form" method="POST" action="{{ route('admin.barang.store') }}">
                        @csrf
                        <div class="form-group full">
                            <label>Nama barang *</label>
                            <input name="name" type="text" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Harga Beli *</label>
                            <input name="purchase_price" type="number" min="0" value="{{ old('purchase_price') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Harga Jual *</label>
                            <input name="selling_price" type="number" min="0" value="{{ old('selling_price') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Stok Awal *</label>
                            <input name="stock" type="number" min="0" value="{{ old('stock', 0) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Kategori *</label>
                            <select name="category_id" required>
                                <option value="">Pilih</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ (string) old('category_id') === (string) $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Satuan *</label>
                            <select name="unit" required>
                                <option value="">Pilih</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit }}" {{ old('unit') === $unit ? 'selected' : '' }}>{{ $unit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer full">
                            <button type="button" class="btn-batal" onclick="toggleModal('modalTambah', false)">Batal</button>
                            <button type="submit" class="btn-simpan">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="modalEdit" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Edit Barang</h2>
                        <span class="close" onclick="toggleModal('modalEdit', false)">&times;</span>
                    </div>
                    <form id="formEditBarang" class="modal-form" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group full">
                            <label>Nama barang *</label>
                            <input id="edit-name" name="name" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>Harga Beli *</label>
                            <input id="edit-purchase-price" name="purchase_price" type="number" min="0" required>
                        </div>
                        <div class="form-group">
                            <label>Harga Jual *</label>
                            <input id="edit-selling-price" name="selling_price" type="number" min="0" required>
                        </div>
                        <div class="form-group">
                            <label>Stok *</label>
                            <input id="edit-stock" name="stock" type="number" min="0" required>
                        </div>
                        <div class="form-group">
                            <label>Kategori *</label>
                            <select id="edit-category-id" name="category_id" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Satuan *</label>
                            <select id="edit-unit" name="unit" required>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit }}">{{ $unit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer full">
                            <button type="button" class="btn-batal" onclick="toggleModal('modalEdit', false)">Batal</button>
                            <button type="submit" class="btn-simpan">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="modalTambahStok" class="modal">
                <div class="modal-content modal-medium">
                    <div class="modal-header">
                        <div class="modal-title-section">
                            <h2>Tambah Stok Barang</h2>
                            <p>Tambahkan jumlah stok untuk barang yang sudah terdaftar.</p>
                        </div>
                        <span class="close" onclick="toggleModal('modalTambahStok', false)">&times;</span>
                    </div>
                    <form id="formTambahStok" class="modal-form" method="POST">
                        @csrf
                        <div class="form-group full">
                            <label>Kode barang</label>
                            <input id="stock-product-code" type="text" class="form-input" readonly>
                        </div>
                        <div class="form-group full">
                            <label>Nama barang</label>
                            <input id="stock-product-name" type="text" class="form-input" readonly>
                        </div>
                        <div class="form-group">
                            <label>Stok saat ini</label>
                            <input id="stock-current" type="number" class="form-input" readonly>
                        </div>
                        <div class="form-group">
                            <label>Jumlah stok ditambahkan *</label>
                            <input name="added_stock" type="number" min="1" class="form-input" placeholder="0" required>
                        </div>
                        <div class="form-group full">
                            <label>Catatan</label>
                            <textarea name="note" class="form-input" rows="3" placeholder="Contoh: Restock dari supplier"></textarea>
                        </div>
                        <div class="modal-footer-flex">
                            <button type="button" class="btn-batal-outline" onclick="toggleModal('modalTambahStok', false)">Batal</button>
                            <button type="submit" class="btn-simpan-full">Simpan Stok</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="modalRiwayatStok" class="modal">
                <div class="modal-content modal-medium">
                    <div class="modal-header">
                        <div class="modal-title-section">
                            <h2>Riwayat Stok</h2>
                            <p id="history-product-name">-</p>
                        </div>
                        <span class="close" onclick="toggleModal('modalRiwayatStok', false)">&times;</span>
                    </div>
                    <div id="history-list" class="history-list">
                        <div class="empty-table">Memuat riwayat...</div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function openProductStockModal(action, code, name, stock) {
                document.getElementById('formTambahStok').action = action;
                document.getElementById('stock-product-code').value = code;
                document.getElementById('stock-product-name').value = name;
                document.getElementById('stock-current').value = stock;
                toggleModal('modalTambahStok', true);
            }

            function openEditModal(action, categoryId, name, unit, purchasePrice, sellingPrice, stock) {
                document.getElementById('formEditBarang').action = action;
                document.getElementById('edit-category-id').value = categoryId;
                document.getElementById('edit-name').value = name;
                document.getElementById('edit-unit').value = unit;
                document.getElementById('edit-purchase-price').value = purchasePrice;
                document.getElementById('edit-selling-price').value = sellingPrice;
                document.getElementById('edit-stock').value = stock;
                toggleModal('modalEdit', true);
            }

            async function openHistoryModal(url) {
                const list = document.getElementById('history-list');
                const title = document.getElementById('history-product-name');

                list.innerHTML = '<div class="empty-table">Memuat riwayat...</div>';
                title.textContent = '-';
                toggleModal('modalRiwayatStok', true);

                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                const data = await response.json();

                title.textContent = `${data.product.code} - ${data.product.name}`;

                if (!data.histories.length) {
                    list.innerHTML = '<div class="empty-table">Belum ada riwayat stok.</div>';
                    return;
                }

                list.innerHTML = data.histories.map((history) => `
                    <div class="history-item">
                        <strong>+${history.added_stock}</strong>
                        <span>Stok akhir: ${history.current_stock}</span>
                        <small>${history.created_at} oleh ${history.user}</small>
                        <p>${history.note || '-'}</p>
                    </div>
                `).join('');
            }

            window.onclick = function (event) {
                if (event.target.className === 'modal') {
                    event.target.style.display = 'none';
                }
            }
        </script>
    </body>
</html>
