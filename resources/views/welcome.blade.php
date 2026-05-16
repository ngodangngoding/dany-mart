<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Kasir Login</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="kasir-shell">
        @php
            $loginAction = \Illuminate\Support\Facades\Route::has('login') ? route('login') : '#';
        @endphp

        <div class="kasir-stage">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.08),_transparent_34%),linear-gradient(180deg,_rgba(255,255,255,0.02),_rgba(0,0,0,0.08))]"></div>

            @if (session('error'))
                <aside class="pointer-events-none absolute left-1/2 top-8 z-20 w-[calc(100%-2rem)] max-w-sm -translate-x-1/2 sm:top-9" aria-live="polite">
                    <div data-toast class="kasir-toast pointer-events-auto justify-between rounded-2xl px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-kasir-danger-soft text-kasir-danger">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M6 6L14 14M14 6L6 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-kasir-danger">{{ session('error', 'Password Incorrect') }}</p>
                                <p class="text-xs text-red-400">Please check your credentials and try again.</p>
                            </div>
                        </div>

                        <button
                            type="button"
                            data-toast-close
                            class="ml-3 inline-flex h-8 w-8 items-center justify-center rounded-full text-kasir-danger/70 transition hover:bg-red-50 hover:text-kasir-danger"
                            aria-label="Close error notification"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path d="M6 6L14 14M14 6L6 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                </aside>
            @endif

            <main class="relative z-10 flex w-full justify-center">
                <section class="kasir-card">
                    <div class="mb-8 text-center">
                        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Kasir</h1>
                        <p class="mt-2 text-sm text-slate-500">Masuk untuk mengakses dashboard kasir.</p>
                    </div>

                    <form method="POST" action="{{ $loginAction }}" class="space-y-5">
                        @csrf

                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-slate-500">Email</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                placeholder="kasir@toko.com"
                                class="kasir-input"
                                required
                            >
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-medium text-slate-500">Password</label>
                            <div class="relative">
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autocomplete="current-password"
                                    placeholder="Masukkan password"
                                    class="kasir-input pr-20"
                                    required
                                >

                                <button
                                    type="button"
                                    data-password-toggle
                                    data-target="password"
                                    aria-pressed="false"
                                    class="absolute inset-y-0 right-3 inline-flex items-center gap-2 text-sm font-medium text-slate-400 transition hover:text-slate-600"
                                    aria-label="Toggle password visibility"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                        <path d="M2.5 10C4.3 6.9 6.9 5.3 10 5.3c3.1 0 5.7 1.6 7.5 4.7-1.8 3.1-4.4 4.7-7.5 4.7-3.1 0-5.7-1.6-7.5-4.7Z" stroke="currentColor" stroke-width="1.4" />
                                        <path d="M7.2 12.8 12.8 7.2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" />
                                        <path d="M5.8 14.2 14.2 5.8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" />
                                    </svg>
                                    <span data-password-label>Hide</span>
                                </button>
                            </div>
                        </div>

                        @unless (\Illuminate\Support\Facades\Route::has('login'))
                            <p class="text-xs text-amber-600">
                                Route login asli belum tersedia. Halaman ini baru menyiapkan tampilan frontend.
                            </p>
                        @endunless

                        <button type="submit" class="kasir-login-button">
                            Log In
                        </button>
                    </form>
                </section>
            </main>
        </div>

        {{-- Contoh toast manual jika ingin ditampilkan tanpa session --}}
        {{--
        <div class="absolute left-1/2 top-6 z-20 w-[calc(100%-2rem)] max-w-sm -translate-x-1/2 sm:top-8">
            <div class="flex items-center gap-3 rounded-2xl border border-red-200 bg-white px-4 py-3 shadow-sm">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-red-50 text-red-500">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M6 6L14 14M14 6L6 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-red-600">Password Incorrect</p>
                    <p class="text-xs text-red-400">Please check your credentials and try again.</p>
                </div>
            </div>
        </div>
        --}}
    </body>
</html>
