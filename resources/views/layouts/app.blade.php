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
        aside { transition: margin 0.3s ease-in-out; }
    </style>
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden font-sans">

    {{-- ─── Sidebar ─────────────────────────────────────────────────────────── --}}
    <aside id="sidebar" class="w-72 bg-[#0f172a] text-slate-400 flex flex-col shadow-2xl z-20 flex-shrink-0">
        <div class="p-8 border-b border-slate-800">
            <h1 class="text-2xl font-black text-white tracking-tighter flex items-center gap-2">
                <i data-lucide="factory" class="text-blue-500"></i>
                PABRIK<span class="text-blue-500">PRO</span>
            </h1>
        </div>

        <div class="p-4 bg-slate-800/50 mx-4 mt-4 rounded-xl flex items-center gap-3 border border-slate-700/50">
            <div class="w-10 h-10 bg-slate-700 rounded-full flex items-center justify-center text-white">
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
            <a href="{{ route('dashboard') }}"
               class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }} w-full flex items-center gap-4 p-4 rounded-xl font-bold transition-all hover:bg-slate-800">
                <i data-lucide="layout-dashboard"></i> Dashboard
            </a>

            <div class="pt-4 pb-2 text-[10px] font-black uppercase tracking-widest px-4">Gudang & Produksi</div>

            <a href="{{ route('inventory.index') }}"
               class="sidebar-item {{ request()->routeIs('inventory.*') ? 'active' : '' }} w-full flex items-center gap-4 p-4 rounded-xl font-bold transition-all hover:bg-slate-800">
                <i data-lucide="package"></i> Data Bahan Baku
            </a>
            <a href="{{ route('recipes.index') }}"
               class="sidebar-item {{ request()->routeIs('recipes.*') ? 'active' : '' }} w-full flex items-center gap-4 p-4 rounded-xl font-bold transition-all hover:bg-slate-800">
                <i data-lucide="file-plus"></i> Resep Produk
            </a>
            <a href="{{ route('production.index') }}"
               class="sidebar-item {{ request()->routeIs('production.index') ? 'active' : '' }} w-full flex items-center gap-4 p-4 rounded-xl font-bold transition-all hover:bg-slate-800">
                <i data-lucide="hammer"></i> Proses Produksi
            </a>
            <a href="{{ route('production.outbound') }}"
               class="sidebar-item {{ request()->routeIs('production.outbound') ? 'active' : '' }} w-full flex items-center gap-4 p-4 rounded-xl font-bold transition-all hover:bg-slate-800">
                <i data-lucide="truck"></i> Barang Keluar
            </a>

            @if(auth()->user()->isAdmin())
            <div class="pt-4 pb-2 text-[10px] font-black uppercase tracking-widest px-4">Sistem</div>
            <a href="{{ route('users.index') }}"
               class="sidebar-item {{ request()->routeIs('users.*') ? 'active' : '' }} w-full flex items-center gap-4 p-4 rounded-xl font-bold transition-all hover:bg-slate-800">
                <i data-lucide="users"></i> Manajemen User
            </a>
            @endif
        </nav>
    </aside>

    {{-- ─── Main Content ──────────────────────────────────────────────────────── --}}
    <main class="flex-1 flex flex-col min-w-0">
        <header class="h-20 bg-white border-b border-slate-200 flex items-center px-8 justify-between shadow-sm">
            <div class="flex items-center gap-4">
                <button onclick="document.getElementById('sidebar').classList.toggle('-ml-72')"
                        class="p-2 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition-colors">
                    <i data-lucide="menu"></i>
                </button>
                <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight">
                    @yield('page_title', 'Dashboard')
                </h2>
            </div>
            <div class="text-sm font-bold text-slate-400">{{ now()->translatedFormat('d M Y') }}</div>
        </header>

        <div class="flex-1 p-8 overflow-y-auto">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="bg-blue-600 text-white p-4 rounded-xl mb-6 font-bold shadow-lg flex items-center gap-3">
                    <i data-lucide="check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-rose-500 text-white p-4 rounded-xl mb-6 font-bold shadow-lg flex items-center gap-3">
                    <i data-lucide="alert-circle"></i> {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>lucide.createIcons();</script>
    @stack('scripts')
</body>
</html>
