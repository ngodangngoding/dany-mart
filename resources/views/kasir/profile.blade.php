<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Profil Kasir</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
        <link rel="stylesheet" href="/css/kasir-static.css">
    </head>
    <body class="dashboard-shell">
        <div class="app-layout two-column">
            @include('layout.kasir-sidebar')

            <main class="main-content">
                <div class="content-stack">
                    @include('layout.kasir-topbar')

                    <section class="profile-panel">
                        <div>
                            <h2 class="modal-title">Profil Kasir</h2>
                            <p class="page-subtitle">Lihat dan lengkapi detail data diri khusus untuk operator kasir.</p>
                        </div>

                        @if (session('success'))
                            <div style="background:#eef9f1;border:1px solid #71a32a;border-radius:8px;padding:12px 16px;margin-bottom:20px;color:#4a6b1c;font-size:14px;font-weight:500;">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:12px 16px;margin-bottom:20px;color:#dc2626;font-size:14px;font-weight:500;">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form class="profile-form" method="POST" action="{{ route('kasir.profile.update') }}">
                            @csrf
                            @method('PUT')
                            <div class="profile-hero">
                                @if ($user->photo)
                                    <img src="{{ asset('storage/' . $user->photo) }}" class="initial-avatar" style="object-fit:cover;" alt="Foto profil">
                                @else
                                    <div class="initial-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                @endif
                                <div>
                                    <h3>{{ $user->name }}</h3>
                                    <p class="muted">Role: <strong style="text-transform:uppercase;">{{ $user->role }}</strong></p>
                                </div>
                            </div>

                            <div class="profile-grid">
                                <div class="field">
                                    <label for="profile-name">Nama Lengkap</label>
                                    <input id="profile-name" name="name" type="text" value="{{ old('name', $user->name) }}" class="input" placeholder="Masukkan nama lengkap" required>
                                </div>
                                <div class="field">
                                    <label for="profile-email">Email Valid</label>
                                    <input id="profile-email" name="email" type="email" value="{{ old('email', $user->email) }}" class="input" placeholder="contoh@email.com" required>
                                </div>
                            </div>

                            <div class="field">
                                <label for="profile-username">Username Login</label>
                                <input id="profile-username" type="text" value="{{ $user->username }}" class="input" readonly style="background:#f9fafb;cursor:not-allowed;color:#6b7280;">
                            </div>

                            <div class="security-note">
                                <div class="icon-circle">
                                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 2a5 5 0 0 0-5 5v2a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2H9V7a1 1 0 1 1 2 0v2h2V7a3 3 0 0 0-3-3Z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4>Ganti Kata Sandi (Password)</h4>
                                    <p>Kasir tidak dapat mengubah kata sandi sendiri. Jika memerlukan reset password, hubungi Admin Owner.</p>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="dark-button">Simpan Perubahan</button>
                            </div>
                        </form>
                    </section>
                </div>
            </main>
        </div>
    </body>
</html>
