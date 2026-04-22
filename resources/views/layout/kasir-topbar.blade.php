<header class="px-1 py-1 flex items-center justify-between w-full mb-4">
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-slate-900">Kasir</h2>
        <p class="mt-1 text-sm text-slate-400">Dany-Mart</p>
    </div>

    <a href="{{ route('kasir.profile') ?? '/kasir/profile' }}" class="flex flex-col items-center gap-1.5 cursor-pointer group">
        <div class="h-12 w-12 rounded-full overflow-hidden ring-2 ring-transparent group-hover:ring-kasir-primary transition shadow-sm">
            <img
                src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='32' fill='%23609824'/%3E%3Ccircle cx='32' cy='25' r='12' fill='%23f4f7ef'/%3E%3Cpath d='M15 53c3-10 11-16 17-16s14 6 17 16' fill='%23f4f7ef'/%3E%3C/svg%3E"
                alt="Foto profil"
                class="h-full w-full object-cover"
            >
        </div>
        <span class="text-xs font-semibold text-slate-500 group-hover:text-kasir-primary transition">Kasir</span>
    </a>
</header>
