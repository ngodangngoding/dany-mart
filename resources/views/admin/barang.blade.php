@php
    $products = [
        ['kode' => 'BRG-001', 'nama' => 'Air Mineral 600ml', 'kat' => 'minuman', 'satuan' => 'Botol', 'beli' => 3000, 'jual' => 5000, 'stok' => 120, 'min' => 20],
        ['kode' => 'BRG-002', 'nama' => 'Es Teh Jumbo', 'kat' => 'minuman', 'satuan' => 'Cup', 'beli' => 4000, 'jual' => 8000, 'stok' => 81, 'min' => 20],
    ];
@endphp

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>POS System - Stok Barang</title>
        <link rel="stylesheet" href="/css/admin.css">
        <script src="/js/admin.js" defer></script>
    </head>
    <body>
        <div class="app-container">
            @include('layout.admin-sidebar')

            <main class="main-content">
                @include('layout.admin-topbar', ['title' => 'Admin', 'subtitle' => 'Toko Altap', 'user' => 'Dimsum (Admin)'])

                <div class="action-header">
                    <div class="table-filters">
                        <select>
                            <option>Semua kategori</option>
                        </select>
                        <input type="text" placeholder="Cari nama atau kode barang...">
                    </div>
                    <div class="buttons">
                        <button class="btn-excel" type="button">Expor Excel</button>
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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $product['kode'] }}</td>
                                    <td>{{ $product['nama'] }}</td>
                                    <td>{{ ucfirst($product['kat']) }}</td>
                                    <td>{{ $product['satuan'] }}</td>
                                    <td>Rp {{ number_format($product['beli'], 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($product['jual'], 0, ',', '.') }}</td>
                                    <td>{{ $product['stok'] }}</td>
                                    <td>
                                        <button class="btn-icon stock" type="button" onclick="openStockModal('{{ $product['kode'] }}', '{{ $product['nama'] }}', {{ $product['stok'] }})">Tambah Stok</button>
                                        <button class="btn-icon" type="button">Edit</button>
                                        <button class="btn-icon delete" type="button">Hapus</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </main>

            <div id="modalTambah" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Tambah Barang Baru</h2>
                        <span class="close" onclick="toggleModal('modalTambah', false)">&times;</span>
                    </div>
                    <form class="modal-form">
                        <div class="form-group full">
                            <label>Nama barang *</label>
                            <input type="text">
                        </div>
                        <div class="form-group">
                            <label>Kode barang *</label>
                            <input type="text">
                        </div>
                        <div class="form-group">
                            <label>Stok Awal</label>
                            <input type="number">
                        </div>
                        <div class="form-group full">
                            <label>Deskripsi</label>
                            <textarea></textarea>
                        </div>
                        <div class="form-group">
                            <label>Kategori *</label>
                            <select>
                                <option>Pilih</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Satuan *</label>
                            <select>
                                <option>Pilih</option>
                            </select>
                        </div>
                        <div class="modal-footer full">
                            <button type="button" class="btn-batal" onclick="toggleModal('modalTambah', false)">Batal</button>
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
                    <form class="modal-form">
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
                            <input type="number" min="1" class="form-input" placeholder="0">
                        </div>
                        <div class="form-group full">
                            <label>Catatan</label>
                            <textarea class="form-input" rows="3" placeholder="Contoh: Restock dari supplier"></textarea>
                        </div>
                        <div class="modal-footer-flex">
                            <button type="button" class="btn-batal-outline" onclick="toggleModal('modalTambahStok', false)">Batal</button>
                            <button type="submit" class="btn-simpan-full">Simpan Stok</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
