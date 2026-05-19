<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login - Dany Mart POS</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
        <link rel="stylesheet" href="/css/kasir-static.css">
        <script src="/js/kasir-static.js" defer></script>
    </head>
    <body class="auth-shell">
        <main class="auth-stage">
            <section class="auth-card">
                <h1 class="auth-title">Dany Mart POS</h1>
                <p style="text-align:center;color:#666;margin-bottom:24px;font-size:14px;">Masuk ke sistem kasir</p>

                @if ($errors->any())
                    <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:10px 14px;margin-bottom:16px;color:#dc2626;font-size:13px;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.preview.store') }}" class="form-stack" novalidate>
                    @csrf

                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" class="input"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            placeholder="email@domain.com"
                            required>
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <div class="password-field">
                            <input id="password" name="password" type="password" class="input"
                                autocomplete="current-password"
                                placeholder="Password"
                                required>
                            <button type="button" class="password-toggle" data-password-toggle data-target="password" aria-label="Toggle password visibility" aria-pressed="false">
                                <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M2.5 10C4.3 6.9 6.9 5.3 10 5.3c3.1 0 5.7 1.6 7.5 4.7-1.8 3.1-4.4 4.7-7.5 4.7-3.1 0-5.7-1.6-7.5-4.7Z" stroke="currentColor" stroke-width="1.4"></path>
                                    <path d="M5.8 14.2 14.2 5.8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"></path>
                                </svg>
                                <span data-password-label>Show</span>
                            </button>
                        </div>
                    </div>

                    <div class="preview-login-actions">
                        <button type="submit" class="btn-primary" style="width:100%;">Masuk</button>
                    </div>
                </form>
            </section>
        </main>
    </body>
</html>
