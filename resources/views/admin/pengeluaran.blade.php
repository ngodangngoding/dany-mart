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
                <div class="left-actions"></div>
                <div class="right-buttons">
                    <button class="btn-outline-print" type="button" onclick="window.print()">Cetak Pengeluaran</button>
                    <button class="btn-add-primary" type="button" onclick="toggleModal('modalPengeluaran', true)">+
                        Tambah Pengeluaran</button>
                </div>
            </div>

            <div class="expense-banner">
                <div class="banner-info">
                    <span>Total pengeluaran</span>
                    <h2>Rp {{ number_format($totalAmount, 0, ',', '.') }}</h2>
                </div>
                <div class="banner-count">
                    <strong>{{ $expenses->total() }} Pengeluaran tercatat</strong>
                </div>
            </div>

            <div class="table-tools">
                <form class="filter-group" method="GET" action="{{ route('admin.pengeluaran.index') }}">
                    <select class="form-select-sm" name="category" onchange="this.form.submit()">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-select-sm"
                        placeholder="Dari tanggal">
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-select-sm"
                        placeholder="Sampai tanggal">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari deskripsi..."
                        class="form-select-sm">
                    <button class="btn-filter-date" type="submit">Filter</button>
                    @if ($search !== '' || $category !== '' || $dateFrom !== '' || $dateTo !== '')
                        <a class="btn-reset-link" href="{{ route('admin.pengeluaran.index') }}">Reset</a>
                    @endif
                </form>
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
                        @forelse ($expenses as $expense)
                            @php
                                $catColors = [
                                    'Listrik' => 'orange',
                                    'Gaji' => 'green',
                                    'Perlengkapan' => 'purple',
                                    'Sewa' => 'blue',
                                ];
                                $color = $catColors[$expense->expense_category] ?? 'orange';
                            @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}</td>
                                <td><span class="lbl-cat {{ $color }}">{{ $expense->expense_category }}</span></td>
                                <td>{{ $expense->description ?? '-' }}</td>
                                <td class="text-red">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                <td>{{ $expense->user->name ?? '-' }}</td>
                                <td>
                                    <button class="btn-icon" type="button" onclick="openEditModal(
                                                            '{{ route('admin.pengeluaran.update', $expense->id) }}',
                                                            '{{ $expense->date }}',
                                                            '{{ $expense->expense_category }}',
                                                            @js($expense->description ?? ''),
                                                            {{ $expense->amount }}
                                                        )">Edit</button>
                                    <form class="inline-form" method="POST"
                                        action="{{ route('admin.pengeluaran.destroy', $expense->id) }}"
                                        onsubmit="return confirm('Hapus pengeluaran ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-icon delete" type="submit">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-table">Belum ada data pengeluaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($expenses->hasPages())
                <div class="table-footer-info">
                    {{ $expenses->links() }}
                </div>
            @else
                <p class="table-footer-info">
                    Menampilkan {{ $expenses->count() }} dari {{ $expenses->total() }} data
                </p>
            @endif
        </main>
    </div>

    {{-- Modal Tambah Pengeluaran --}}
    <div id="modalPengeluaran" class="modal">
        <div class="modal-content modal-medium">
            <div class="modal-header">
                <div class="modal-title-section">
                    <h2>Tambah Pengeluaran Baru</h2>
                    <p>Catat pengeluaran operasional toko</p>
                </div>
                <span class="close" onclick="toggleModal('modalPengeluaran', false)">&times;</span>
            </div>

            <form class="modal-form" method="POST" action="{{ route('admin.pengeluaran.store') }}">
                @csrf
                <div class="form-group full">
                    <label for="tambah-date">Tanggal *</label>
                    <input id="tambah-date" type="date" name="date" value="{{ old('date', now()->toDateString()) }}"
                        class="form-input" required>
                </div>
                <div class="form-group full">
                    <label for="tambah-category">Kategori *</label>
                    <select id="tambah-category" name="expense_category" class="form-input" required>
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}" {{ old('expense_category') === $cat ? 'selected' : '' }}>{{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group full">
                    <label for="tambah-description">Deskripsi (opsional)</label>
                    <textarea id="tambah-description" name="description"
                        placeholder="Deskripsi singkat pengeluaran (opsional)" rows="3"
                        class="form-input">{{ old('description') }}</textarea>
                </div>
                <div class="form-group full">
                    <label for="tambah-amount">Nominal pengeluaran (Rp) *</label>
                    <input id="tambah-amount" type="number" name="amount" value="{{ old('amount') }}" placeholder="0"
                        min="1" class="form-input" required>
                </div>
                <div class="modal-footer-flex">
                    <button type="button" class="btn-batal-outline"
                        onclick="toggleModal('modalPengeluaran', false)">Batal</button>
                    <button type="submit" class="btn-simpan-full">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Pengeluaran --}}
    <div id="modalEditPengeluaran" class="modal">
        <div class="modal-content modal-medium">
            <div class="modal-header">
                <div class="modal-title-section">
                    <h2>Edit Pengeluaran</h2>
                    <p>Perbarui data pengeluaran operasional</p>
                </div>
                <span class="close" onclick="toggleModal('modalEditPengeluaran', false)">&times;</span>
            </div>

            <form id="formEditPengeluaran" class="modal-form" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group full">
                    <label for="edit-date">Tanggal *</label>
                    <input id="edit-date" type="date" name="date" class="form-input" required>
                </div>
                <div class="form-group full">
                    <label for="edit-category">Kategori *</label>
                    <select id="edit-category" name="expense_category" class="form-input" required>
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group full">
                    <label for="edit-description">Deskripsi (opsional)</label>
                    <textarea id="edit-description" name="description"
                        placeholder="Deskripsi singkat pengeluaran (opsional)" rows="3" class="form-input"></textarea>
                </div>
                <div class="form-group full">
                    <label for="edit-amount">Nominal pengeluaran (Rp) *</label>
                    <input id="edit-amount" type="number" name="amount" placeholder="0" min="1" class="form-input"
                        required>
                </div>
                <div class="modal-footer-flex">
                    <button type="button" class="btn-batal-outline"
                        onclick="toggleModal('modalEditPengeluaran', false)">Batal</button>
                    <button type="submit" class="btn-simpan-full">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleModal(id, show) {
            const modal = document.getElementById(id);
            modal.style.display = show ? 'flex' : 'none';
        }

        function openEditModal(action, date, category, description, amount) {
            document.getElementById('formEditPengeluaran').action = action;
            document.getElementById('edit-date').value = date;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-amount').value = amount;

            const catSelect = document.getElementById('edit-category');
            for (let i = 0; i < catSelect.options.length; i++) {
                catSelect.options[i].selected = catSelect.options[i].value === category;
            }

            toggleModal('modalEditPengeluaran', true);
        }

        window.onclick = function (event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }

        // Auto-open modal tambah jika ada validation error dari store
        @if ($errors->any() && old('_method') === null && old('date'))
            toggleModal('modalPengeluaran', true);
        @endif
    </script>
</body>

</html>