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
            @include('layout.admin-topbar', ['title' => 'Manajemen Akun', 'subtitle' => 'Kelola akun admin dan kasir', 'user' => auth()->user()->name . ' (Admin)'])

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
                <form class="table-filters" method="GET" action="{{ route('admin.users.index') }}">
                    <select name="role" onchange="this.form.submit()" class="form-select-sm">
                        <option value="">Semua role</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r }}" {{ $role === $r ? 'selected' : '' }}>
                                {{ ucfirst($r) }}
                            </option>
                        @endforeach
                    </select>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, username, atau email...">
                    <button class="btn-excel" type="submit">Cari</button>
                    @if ($search !== '' || $role !== '')
                        <a class="btn-reset-link" href="{{ route('admin.users.index') }}">Reset</a>
                    @endif
                </form>
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
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    @if ($user->photo)
                                        <img src="{{ asset('storage/' . $user->photo) }}"
                                            class="account-photo-preview"
                                            alt="{{ $user->name }}"
                                            style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                                    @else
                                        <div class="account-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $user->name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td><span class="badge-role">{{ ucfirst($user->role) }}</span></td>
                                <td>
                                    <button class="btn-icon" type="button"
                                        onclick="openEditModal(
                                            '{{ route('admin.users.update', $user->id) }}',
                                            {{ $user->id }},
                                            @js($user->name),
                                            @js($user->username),
                                            @js($user->email),
                                            @js($user->role),
                                            '{{ $user->photo ? asset('storage/' . $user->photo) : '' }}'
                                        )">Edit</button>
                                    @if ($user->id !== auth()->id())
                                        <form class="inline-form" method="POST"
                                            action="{{ route('admin.users.destroy', $user->id) }}"
                                            onsubmit="return confirm('Hapus akun {{ $user->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-icon delete" type="submit">Hapus</button>
                                        </form>
                                    @else
                                        <span style="font-size:12px;color:#999;">Akun aktif</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-table">Belum ada akun pengguna.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="table-footer-info">
                    {{ $users->links() }}
                </div>
            @endif
        </main>
    </div>

    {{-- Modal Tambah Akun --}}
    <div id="modalAkun" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title-section">
                    <h2>Tambah Akun Baru</h2>
                    <p>Buat akun untuk admin atau kasir.</p>
                </div>
                <span class="close" onclick="toggleModal('modalAkun', false)">&times;</span>
            </div>
            <form class="modal-form" method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group full photo-upload-row">
                    <img id="account-photo-preview" class="account-photo-preview"
                        src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 96 96'%3E%3Crect width='96' height='96' rx='48' fill='%23eef9f1'/%3E%3Ctext x='48' y='56' text-anchor='middle' font-size='28' font-family='Arial' fill='%2371a32a'%3EIMG%3C/text%3E%3C/svg%3E"
                        alt="Preview photo">
                    <div>
                        <label>Photo akun</label>
                        <input type="file" name="photo" accept="image/*" data-photo-input="#account-photo-preview">
                    </div>
                </div>
                <div class="form-group">
                    <label for="tambah-name">Nama lengkap *</label>
                    <input id="tambah-name" type="text" name="name" value="{{ old('name') }}" placeholder="Nama lengkap" required>
                </div>
                <div class="form-group">
                    <label for="tambah-username">Username *</label>
                    <input id="tambah-username" type="text" name="username" value="{{ old('username') }}" placeholder="username" required>
                </div>
                <div class="form-group">
                    <label for="tambah-email">Email *</label>
                    <input id="tambah-email" type="email" name="email" value="{{ old('email') }}" placeholder="email@domain.com" required>
                </div>
                <div class="form-group">
                    <label for="tambah-role">Role *</label>
                    <select id="tambah-role" name="role" required>
                        <option value="">Pilih role</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r }}" {{ old('role') === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="tambah-password">Password *</label>
                    <input id="tambah-password" type="password" name="password" placeholder="Min. 8 karakter" required>
                </div>
                <div class="form-group">
                    <label for="tambah-password-confirm">Konfirmasi Password *</label>
                    <input id="tambah-password-confirm" type="password" name="password_confirmation" placeholder="Ulangi password" required>
                </div>
                <div class="modal-footer full">
                    <button type="button" class="btn-batal" onclick="toggleModal('modalAkun', false)">Batal</button>
                    <button type="submit" class="btn-simpan">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Akun --}}
    <div id="modalEditAkun" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title-section">
                    <h2>Edit Akun</h2>
                    <p>Perbarui detail akun pengguna.</p>
                </div>
                <span class="close" onclick="toggleModal('modalEditAkun', false)">&times;</span>
            </div>
            <form id="formEditAkun" class="modal-form" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group full photo-upload-row">
                    <img id="edit-photo-preview" class="account-photo-preview"
                        src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 96 96'%3E%3Crect width='96' height='96' rx='48' fill='%23eef9f1'/%3E%3Ctext x='48' y='56' text-anchor='middle' font-size='28' font-family='Arial' fill='%2371a32a'%3E?%3C/text%3E%3C/svg%3E"
                        alt="Preview photo">
                    <div>
                        <label>Photo akun</label>
                        <input type="file" name="photo" accept="image/*" data-photo-input="#edit-photo-preview">
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit-name">Nama lengkap *</label>
                    <input id="edit-name" type="text" name="name" placeholder="Nama lengkap" required>
                </div>
                <div class="form-group">
                    <label for="edit-username">Username *</label>
                    <input id="edit-username" type="text" name="username" placeholder="username" required>
                </div>
                <div class="form-group">
                    <label for="edit-email">Email *</label>
                    <input id="edit-email" type="email" name="email" placeholder="email@domain.com" required>
                </div>
                <div class="form-group">
                    <label for="edit-role">Role *</label>
                    <select id="edit-role" name="role" required>
                        @foreach ($roles as $r)
                            <option value="{{ $r }}">{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit-password">Password baru</label>
                    <input id="edit-password" type="password" name="password" placeholder="Kosongkan jika tidak ingin diganti">
                </div>
                <div class="form-group">
                    <label for="edit-password-confirm">Konfirmasi Password</label>
                    <input id="edit-password-confirm" type="password" name="password_confirmation" placeholder="Ulangi password baru">
                </div>
                <div class="modal-footer full">
                    <button type="button" class="btn-batal" onclick="toggleModal('modalEditAkun', false)">Batal</button>
                    <button type="submit" class="btn-simpan">Simpan</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        function toggleModal(id, show) {
            const modal = document.getElementById(id);
            modal.style.display = show ? 'flex' : 'none';
        }

        function openEditModal(actionEdit, id, name, username, email, role, photoUrl) {
            document.getElementById('formEditAkun').action = actionEdit;

            document.getElementById('edit-name').value = name;
            document.getElementById('edit-username').value = username;
            document.getElementById('edit-email').value = email;

            const roleSelect = document.getElementById('edit-role');
            for (let i = 0; i < roleSelect.options.length; i++) {
                roleSelect.options[i].selected = roleSelect.options[i].value === role;
            }

            const preview = document.getElementById('edit-photo-preview');
            if (photoUrl) {
                preview.src = photoUrl;
            } else {
                preview.src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 96 96'%3E%3Crect width='96' height='96' rx='48' fill='%23eef9f1'/%3E%3Ctext x='48' y='56' text-anchor='middle' font-size='28' font-family='Arial' fill='%2371a32a'%3E" + name.charAt(0).toUpperCase() + "%3C/text%3E%3C/svg%3E";
            }

            // Reset password fields setiap buka modal
            document.getElementById('edit-password').value = '';
            document.getElementById('edit-password-confirm').value = '';

            toggleModal('modalEditAkun', true);
        }

        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }

        // Auto-open tambah modal jika ada error dari store
        @if ($errors->any() && !old('_method') && old('name'))
            toggleModal('modalAkun', true);
        @endif
    </script>
</body>

</html>
