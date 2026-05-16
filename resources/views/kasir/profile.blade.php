<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Profil Kasir</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="dashboard-shell">
        <div class="min-h-screen xl:grid xl:grid-cols-[280px_minmax(0,1fr)]">
            @include('layout.kasir-sidebar')

            <main class="min-w-0 px-4 py-5 sm:px-6 lg:px-8 lg:py-8">
                <div class="mx-auto flex max-w-7xl flex-col gap-6">
                    @include('layout.kasir-topbar')

                    <section class="dashboard-panel p-5 sm:p-8 max-w-3xl">
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Profil Kasir</h2>
                            <p class="text-[15px] text-slate-500 mt-1">Lihat dan lengkapi detail data diri khusus untuk operator kasir.</p>
                        </div>

                        <form action="#" method="POST" class="space-y-8">
                            <!-- Hero Kasir Info -->
                            <div class="flex items-center gap-5 pb-6 border-b border-slate-100">
                                <div class="h-[84px] w-[84px] bg-slate-100 border-[4px] border-kasir-pastel rounded-full flex items-center justify-center text-kasir-primary text-[32px] font-bold shadow-sm">
                                    {{ substr($user->name ?? 'Kasir', 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-[20px] font-bold text-slate-800">{{ $user->name ?? 'Kasir Utama' }}</h3>
                                    <p class="text-[14px] font-semibold text-slate-500 mt-1">Kode ID: <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md ml-1">{{ $user->usercode ?? 'KSR-001' }}</span></p>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="space-y-2">
                                        <label class="text-[14px] font-bold text-slate-700">Nama Lengkap</label>
                                        <input type="text" value="{{ $user->name ?? '' }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-[15px] font-medium text-slate-900 outline-none transition focus:border-kasir-primary focus:ring-1 focus:ring-kasir-primary/50" placeholder="Masukkan nama lengkap">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[14px] font-bold text-slate-700">Email Valid</label>
                                        <input type="email" value="{{ $user->email ?? '' }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-[15px] font-medium text-slate-900 outline-none transition focus:border-kasir-primary focus:ring-1 focus:ring-kasir-primary/50" placeholder="contoh@email.com">
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[14px] font-bold text-slate-700">Username Login</label>
                                    <input type="text" value="{{ explode('@', $user->email ?? 'kasir')[0] }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-[15px] font-medium text-slate-500 cursor-not-allowed outline-none" readonly>
                                </div>
                            </div>

                            <!-- Security Notes (Password) -->
                            <div class="rounded-2xl border border-orange-200 bg-orange-50/50 p-6 relative overflow-hidden">
                                <!-- Background accent -->
                                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-orange-100/50"></div>
                                
                                <div class="flex items-start gap-4 relative z-10">
                                    <div class="bg-orange-100 text-orange-600 rounded-2xl p-3 shrink-0">
                                        <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 2a5 5 0 00-5 5v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2H9V7a1 1 0 112 0v2h2V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-[15px] font-bold text-orange-950">Ganti Kata Sandi (Password)</h4>
                                        <p class="text-[14px] text-orange-850 mt-1.5 leading-relaxed">
                                            Mohon maaf, demi batas otoritas dan keamanan sistem, <span class="font-bold">Kasir tidak dapat mengubah kata sandi sendiri</span>. Jika Anda memerlukan pengaturan ulang kata sandi (reset pass), silakan langsung menghubungi akun <b>Admin Owner</b>.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2 flex items-center justify-end">
                                <button type="button" class="rounded-2xl bg-slate-900 px-6 py-3.5 text-[15px] font-bold text-white transition hover:opacity-90 shadow-lg shadow-slate-900/10">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            </main>
        </div>
    </body>
</html>
