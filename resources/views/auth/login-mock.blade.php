<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Login Preview Kasir</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="kasir-shell">
        @php
            $showError = false;
        @endphp

        <div class="kasir-stage">
            @if ($showError)
                <aside class="pointer-events-none absolute left-1/2 top-8 z-20 w-[calc(100%-2rem)] max-w-sm -translate-x-1/2 sm:top-9" aria-live="polite">
                    <div data-toast class="kasir-toast pointer-events-auto justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-5 w-5 items-center justify-center rounded-full bg-kasir-danger-soft text-kasir-danger">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M6 6L14 14M14 6L6 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-kasir-danger sm:text-sm">Password Incorrect</p>
                        </div>

                        <button
                            type="button"
                            data-toast-close
                            class="ml-3 inline-flex h-6 w-6 items-center justify-center rounded-full text-kasir-danger/70 transition hover:bg-red-50 hover:text-kasir-danger"
                            aria-label="Close error notification"
                        >
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path d="M6 6L14 14M14 6L6 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                </aside>
            @endif

            <main class="relative z-10 flex w-full justify-center">
                <section class="kasir-card">
                    <header class="mb-7 text-center">
                        <h1 class="text-[1.7rem] font-bold tracking-tight text-slate-800">Kasir</h1>
                    </header>

                    <form action="#" method="POST" class="space-y-5" novalidate onsubmit="event.preventDefault(); window.location.href='/dashboard-preview';">
                        <div class="space-y-2">
                            <label for="email" class="block text-[11px] font-medium text-kasir-muted sm:text-xs">
                                Email or Username
                            </label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                placeholder=""
                                autocomplete="off"
                                class="kasir-input"
                            >
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="block text-[11px] font-medium text-kasir-muted sm:text-xs">
                                Password
                            </label>

                            <div class="relative">
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    placeholder=""
                                    autocomplete="off"
                                    class="kasir-input pr-20"
                                >

                                <button
                                    type="button"
                                    data-password-toggle
                                    data-target="password"
                                    aria-label="Toggle password visibility"
                                    aria-pressed="false"
                                    class="absolute inset-y-0 right-3 inline-flex items-center gap-1.5 text-xs font-medium text-kasir-muted transition hover:text-slate-600"
                                >
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                        <path d="M2.5 10C4.3 6.9 6.9 5.3 10 5.3c3.1 0 5.7 1.6 7.5 4.7-1.8 3.1-4.4 4.7-7.5 4.7-3.1 0-5.7-1.6-7.5-4.7Z" stroke="currentColor" stroke-width="1.4" />
                                        <path d="M7.2 12.8 12.8 7.2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" />
                                        <path d="M5.8 14.2 14.2 5.8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" />
                                    </svg>
                                    <span data-password-label>Hide</span>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="kasir-login-button">
                            Log In
                        </button>
                    </form>
                </section>
            </main>
        </div>
    </body>
</html>
