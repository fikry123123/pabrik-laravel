@extends('layouts.app')
@section('title', 'Dashboard – PabrikPro')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-8">

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
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
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Produksi</p>
                <h3 class="text-3xl font-black mt-1 text-slate-800">{{ $total_production }} Unit</h3>
            </div>
            <div class="w-12 h-12 bg-slate-50 text-slate-600 rounded-xl flex items-center justify-center">
                <i data-lucide="truck"></i>
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

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Bahan Baku Tipis</p>
                <h3 class="text-3xl font-black mt-1 text-rose-500">{{ $lowStockCount }} Jenis</h3>
            </div>
            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center">
                <i data-lucide="alert-triangle"></i>
            </div>
        </div>
    </div>

        <div class="grid gap-6 xl:grid-cols-3 mb-6">
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-slate-400 uppercase text-xs font-bold tracking-widest">Grafik Produksi Bulanan</p>
                        <h4 class="text-lg font-black text-slate-800">Tren Bulanan</h4>
                    </div>
                    <span class="text-xs font-semibold text-slate-500">Unit</span>
                </div>
                @php
                    $monthlyMax = $monthlyProduction->max('total_qty') ?: 1;
                @endphp
                <div class="space-y-4">
                    @foreach($monthlyProduction as $month)
                        @php $barWidth = min(100, ($month->total_qty / $monthlyMax) * 100); @endphp
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-600">{{ $month->month }}</span>
                                <span class="font-bold text-slate-800">{{ $month->total_qty }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-200 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-sky-400" style="width: {{ $barWidth }}%"></div>
                            </div>
                        </div>
                    @endforeach
                    @if($monthlyProduction->isEmpty())
                        <div class="text-slate-400">Belum ada produksi bulanan.</div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-slate-400 uppercase text-xs font-bold tracking-widest">Grafik Produk Teratas</p>
                        <h4 class="text-lg font-black text-slate-800">Top Produk</h4>
                    </div>
                    <span class="text-xs font-semibold text-slate-500">Unit</span>
                </div>
                @php $topMax = $topProducts->max('total_qty') ?: 1; @endphp
                <div class="space-y-4">
                    @foreach($topProducts as $product)
                        @php $barWidth = min(100, ($product->total_qty / $topMax) * 100); @endphp
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-600">{{ $product->nama_barang }}</span>
                                <span class="font-bold text-slate-800">{{ $product->total_qty }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-200 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-lime-400" style="width: {{ $barWidth }}%"></div>
                            </div>
                        </div>
                    @endforeach
                    @if($topProducts->isEmpty())
                        <div class="text-slate-400">Belum ada data produksi.</div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-slate-400 uppercase text-xs font-bold tracking-widest">Stok Bahan Baku</p>
                        <h4 class="text-lg font-black text-slate-800">Kondisi Stok</h4>
                    </div>
                    <span class="text-xs font-semibold text-slate-500">Satuan</span>
                </div>
                @if($lowStockCount)
                    <div class="rounded-3xl p-4 mb-4 bg-rose-50 border border-rose-100 text-rose-700">
                        <div class="font-black">Stok tipis terdeteksi</div>
                        <p class="text-sm text-slate-600 mt-1">{{ $lowStockCount }} bahan baku memiliki stok kurang dari {{ $lowStockThreshold }}.</p>
                    </div>
                @endif
                @php $materialMax = $maxMaterialStock; @endphp
                <div class="space-y-3">
                    @forelse($materials->take(10) as $bahan)
                        @php $barWidth = min(100, ($bahan->stok / $materialMax) * 100); @endphp
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $bahan->nama }}</p>
                                    <p class="text-[11px] text-slate-500">{{ $bahan->satuan }}</p>
                                </div>
                                <span class="font-black {{ $bahan->stok < $lowStockThreshold ? 'text-rose-500' : 'text-slate-800' }}">{{ $bahan->stok }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-200 overflow-hidden">
                                <div class="h-full rounded-full {{ $bahan->stok < $lowStockThreshold ? 'bg-rose-500' : 'bg-blue-500' }}" style="width: {{ $barWidth }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-slate-400">Belum ada data bahan baku.</div>
                    @endforelse
                </div>
                @if($materials->count() > 10)
                    <div class="mt-4 text-xs text-slate-500">Menampilkan 10 bahan baku dari {{ $materials->count() }} total.</div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-slate-400 uppercase text-xs font-bold tracking-widest">Detail Produk Teratas</p>
                    <h4 class="text-lg font-black text-slate-800">Produk Populer</h4>
                </div>
            </div>
            <div class="space-y-3">
                @foreach($topProducts as $product)
                    @php $width = min(100, max(10, ($product->total_qty / max(1, $topProducts->max('total_qty'))) * 100)); @endphp
                    <div class="bg-slate-100 rounded-2xl overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-3 text-sm">
                            <span>{{ $product->nama_barang }}</span>
                            <span class="font-black">{{ $product->total_qty }}</span>
                        </div>
                        <div class="h-2 bg-slate-200">
                            <div class="h-2 bg-blue-500" style="width: {{ $width }}%"></div>
                        </div>
                    </div>
                @endforeach
                @if($topProducts->isEmpty())
                    <div class="text-slate-400">Tidak ada data produksi.</div>
                @endif
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
