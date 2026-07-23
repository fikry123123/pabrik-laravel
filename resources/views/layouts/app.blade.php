<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PabrikPro')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .sidebar-item.active { background:#1e293b; color:#38bdf8; border-right:4px solid #38bdf8; }
    </style>
</head>
<body class="bg-slate-50 font-sans">

<div class="flex h-screen overflow-hidden">

    {{-- ─── Backdrop (mobile only) ────────────────────────────────────────────── --}}
    <div id="sidebar-backdrop"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-30 lg:hidden opacity-0 pointer-events-none transition-opacity duration-300"></div>

    {{-- ─── Sidebar ─────────────────────────────────────────────────────────── --}}
    <aside id="sidebar"
           class="fixed lg:static inset-y-0 left-0 w-72 bg-[#0f172a] text-slate-400 flex flex-col shadow-2xl
                  z-40 flex-shrink-0 -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
        <div class="p-6 lg:p-8 border-b border-slate-800 flex items-center justify-between">
            <h1 class="text-2xl font-black text-white tracking-tighter flex items-center gap-2">
                <i data-lucide="factory" class="text-blue-500"></i>
                PABRIK<span class="text-blue-500">PRO</span>
            </h1>
            {{-- Close button (mobile only) --}}
            <button type="button" data-sidebar-close
                    class="lg:hidden text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition-colors">
                <i data-lucide="x"></i>
            </button>
        </div>

        <div class="p-4 bg-slate-800/50 mx-4 mt-4 rounded-xl flex items-center gap-3 border border-slate-700/50">
            <div class="w-10 h-10 bg-slate-700 rounded-full flex items-center justify-center text-white flex-shrink-0">
                <i data-lucide="user"></i>
            </div>
            <div class="flex-1 overflow-hidden">
                <p class="text-sm font-bold text-white truncate">{{ strtoupper(auth()->user()->username) }}</p>
                <p class="text-[10px] font-black tracking-widest uppercase
                    {{ auth()->user()->role === 'admin' ? 'text-emerald-400' : (auth()->user()->role === 'editor' ? 'text-blue-400' : 'text-amber-400') }}">
                    {{ auth()->user()->role }}
                </p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-rose-400 transition-colors p-2" title="Logout">
                    <i data-lucide="log-out" size="18"></i>
                </button>
            </form>
        </div>

        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <a href="{{ route('dashboard') }}" data-nav-link
               class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }} w-full flex items-center gap-4 p-4 rounded-xl font-bold transition-all hover:bg-slate-800">
                <i data-lucide="layout-dashboard"></i> Dashboard
            </a>

            <div class="pt-4 pb-2 text-[10px] font-black uppercase tracking-widest px-4">Gudang & Produksi</div>

            <a href="{{ route('inventory.index') }}" data-nav-link
               class="sidebar-item {{ request()->routeIs('inventory.*') ? 'active' : '' }} w-full flex items-center gap-4 p-4 rounded-xl font-bold transition-all hover:bg-slate-800">
                <i data-lucide="package"></i> Data Bahan Baku
            </a>
            <a href="{{ route('recipes.index') }}" data-nav-link
               class="sidebar-item {{ request()->routeIs('recipes.*') ? 'active' : '' }} w-full flex items-center gap-4 p-4 rounded-xl font-bold transition-all hover:bg-slate-800">
                <i data-lucide="file-plus"></i> Resep Produk
            </a>
            <a href="{{ route('production.index') }}" data-nav-link
               class="sidebar-item {{ request()->routeIs('production.index') ? 'active' : '' }} w-full flex items-center gap-4 p-4 rounded-xl font-bold transition-all hover:bg-slate-800">
                <i data-lucide="hammer"></i> Proses Produksi
            </a>
            <a href="{{ route('production.outbound') }}" data-nav-link
               class="sidebar-item {{ request()->routeIs('production.outbound') ? 'active' : '' }} w-full flex items-center gap-4 p-4 rounded-xl font-bold transition-all hover:bg-slate-800">
                <i data-lucide="truck"></i> Barang Keluar
            </a>

            @if(auth()->user()->isAdmin())
            <div class="pt-4 pb-2 text-[10px] font-black uppercase tracking-widest px-4">Sistem</div>
            <a href="{{ route('users.index') }}" data-nav-link
               class="sidebar-item {{ request()->routeIs('users.*') ? 'active' : '' }} w-full flex items-center gap-4 p-4 rounded-xl font-bold transition-all hover:bg-slate-800">
                <i data-lucide="users"></i> Manajemen User
            </a>
            @endif
        </nav>
    </aside>

    {{-- ─── Main Content ──────────────────────────────────────────────────────── --}}
    <main class="flex-1 flex flex-col min-w-0">
        <header class="h-16 md:h-20 bg-white border-b border-slate-200 flex items-center px-4 md:px-8 justify-between shadow-sm flex-shrink-0">
            <div class="flex items-center gap-3 md:gap-4 min-w-0">
                <button type="button" data-sidebar-open
                        class="lg:hidden p-2 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition-colors flex-shrink-0">
                    <i data-lucide="menu"></i>
                </button>
                <h2 class="text-base md:text-xl font-black text-slate-800 uppercase tracking-tight truncate">
                    @yield('page_title', 'Dashboard')
                </h2>
            </div>
            <div class="text-xs md:text-sm font-bold text-slate-400 flex-shrink-0 whitespace-nowrap">{{ now()->translatedFormat('d M Y') }}</div>
        </header>

        <div class="flex-1 p-4 md:p-8 overflow-y-auto">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="bg-blue-600 text-white p-4 rounded-xl mb-6 font-bold shadow-lg flex items-center gap-3">
                    <i data-lucide="check-circle" class="flex-shrink-0"></i> <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-rose-500 text-white p-4 rounded-xl mb-6 font-bold shadow-lg flex items-center gap-3">
                    <i data-lucide="alert-circle" class="flex-shrink-0"></i> <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

</div>

    <script>
        // ─── Mobile sidebar drawer ────────────────────────────────────────────
        (function () {
            const sidebar  = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const mqDesktop = window.matchMedia('(min-width: 1024px)');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
                document.body.classList.add('overflow-hidden');
            }
            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                document.body.classList.remove('overflow-hidden');
            }

            document.querySelectorAll('[data-sidebar-open]').forEach(b => b.addEventListener('click', openSidebar));
            document.querySelectorAll('[data-sidebar-close]').forEach(b => b.addEventListener('click', closeSidebar));
            backdrop.addEventListener('click', closeSidebar);

            // Auto-hide saat pindah menu (hanya di mobile)
            document.querySelectorAll('[data-nav-link]').forEach(link => {
                link.addEventListener('click', () => { if (!mqDesktop.matches) closeSidebar(); });
            });

            // Tutup dengan tombol Escape
            document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });

            // Reset state saat berpindah ke desktop
            mqDesktop.addEventListener('change', e => { if (e.matches) closeSidebar(); });
        })();
    </script>

    <script>lucide.createIcons();</script>
    @stack('scripts')
</body>
</html>
