<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login Preview</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
        <link rel="stylesheet" href="/css/kasir-static.css">
        <script src="/js/kasir-static.js" defer></script>
    </head>
    <body class="auth-shell">
        <main class="auth-stage">
            <section class="auth-card">
                <h1 class="auth-title">Dany-Mart Preview</h1>
                <form method="POST" action="{{ route('login.preview.store') }}" class="form-stack" novalidate>
                    @csrf

                    <div class="field">
                        <label for="email">Email or Username</label>
                        <input id="email" name="email" type="email" class="input" autocomplete="off">
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <div class="password-field">
                            <input id="password" name="password" type="password" class="input" autocomplete="off">
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
                        <button type="submit" name="role" value="kasir" class="btn-primary">Masuk sebagai Kasir</button>
                        <button type="submit" name="role" value="admin" class="btn-secondary">Masuk sebagai Admin</button>
                    </div>
                </form>
            </section>
        </main>
    </body>
</html>
