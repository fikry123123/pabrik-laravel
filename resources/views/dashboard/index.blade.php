@extends('layouts.app')
@section('title', 'Dashboard – PabrikPro')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-8">

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Bahan Baku</p>
                <h3 class="text-3xl font-black mt-1">{{ $total_materials }} Jenis</h3>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <i data-lucide="box"></i>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Sedang Diproses (WIP)</p>
                <h3 class="text-3xl font-black mt-1 text-amber-500">{{ $total_wip }} Unit</h3>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center">
                <i data-lucide="settings"></i>
            </div>
        </div>
    </div>

    {{-- Proyeksi Kapasitas --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5 md:p-8">
        <h3 class="font-black text-slate-800 mb-6 flex items-center gap-2">
            <i data-lucide="bar-chart-2" class="text-blue-500"></i>
            Proyeksi Kapasitas Produksi
        </h3>
        {{-- Desktop: tabel --}}
        <table class="hidden md:table w-full text-left text-sm">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="p-4">Nama Produk</th>
                    <th class="p-4">Kebutuhan Resep</th>
                    <th class="p-4">Bisa Diproduksi (Max)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proyeksi as $p)
                <tr class="border-b">
                    <td class="p-4 font-bold">{{ $p['nama_produk'] }}</td>
                    <td class="p-4 text-xs text-slate-500">
                        {{ collect($p['bom'])->map(fn($b) => "{$b['qty_butuh']} {$b['satuan']} {$b['nama_bahan']}")->join(' + ') }}
                    </td>
                    <td class="p-4 font-black {{ $p['kapasitas'] > 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                        {{ $p['kapasitas'] }} Unit
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="p-10 text-center text-slate-400 font-bold">
                        Belum ada produk terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Mobile: kartu --}}
        <div class="md:hidden space-y-3">
            @forelse($proyeksi as $p)
            <div class="border rounded-2xl p-4 space-y-2">
                <div class="flex items-center justify-between gap-3">
                    <p class="font-black text-slate-800">{{ $p['nama_produk'] }}</p>
                    <span class="font-black text-sm whitespace-nowrap {{ $p['kapasitas'] > 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                        {{ $p['kapasitas'] }} Unit
                    </span>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Kebutuhan Resep</p>
                    <p class="text-xs text-slate-500">
                        {{ collect($p['bom'])->map(fn($b) => "{$b['qty_butuh']} {$b['satuan']} {$b['nama_bahan']}")->join(' + ') ?: '—' }}
                    </p>
                </div>
            </div>
            @empty
            <div class="p-10 text-center text-slate-400 font-bold">
                Belum ada produk terdaftar.
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
