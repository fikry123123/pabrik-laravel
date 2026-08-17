@extends('layouts.app')
@section('title', 'Proses Produksi – PabrikPro')
@section('page_title', 'Proses Produksi')

@php
    use App\Helpers\PermissionHelper;
    $canCreate = PermissionHelper::canCreate('produksi');
    $canUpdate = PermissionHelper::canUpdate('produksi');
    $canDelete = PermissionHelper::canDelete('produksi');
@endphp

@section('content')
<div class="space-y-6">

    {{-- Form Produksi Baru --}}
    @if($canCreate)
    <div class="bg-white p-5 md:p-8 rounded-3xl shadow-sm border max-w-2xl">
        <h3 class="text-lg font-black mb-6 text-amber-500 flex items-center gap-2">
            <i data-lucide="zap"></i> Eksekusi Produksi Baru
        </h3>
        <form method="POST" action="{{ route('production.start') }}" class="space-y-6">
            @csrf

            {{-- Pilih Produk --}}
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Pilih Barang</label>
                <select name="id_produk" id="select-produk" onchange="kalkulasiKapasitas()"
                        class="w-full p-4 border bg-slate-50 rounded-xl mt-1 font-bold" required>
                    <option value="">-- Pilih Barang yang Akan Dibuat --</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}">{{ $p->nama_produk }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Info Stok --}}
            <div id="info-produksi" class="hidden p-4 rounded-xl border border-blue-100 bg-blue-50">
                <p class="text-xs font-bold text-blue-600 uppercase mb-2">Status Ketersediaan Bahan</p>
                <ul id="list-bahan-dibutuhkan" class="text-sm font-medium text-slate-700 space-y-1 mb-4"></ul>
                <div class="flex items-center justify-between border-t border-blue-200 pt-3">
                    <span class="font-bold text-slate-700">Maksimal Bisa Dibuat:</span>
                    <span id="max-qty-label" class="text-xl font-black text-blue-700">0 Unit</span>
                </div>
            </div>

            {{-- Input Qty --}}
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Jumlah Produksi (Qty)</label>
                <input type="number" name="qty_produksi" id="input-qty-produksi" min="1" max="0"
                       class="w-full p-4 border bg-slate-50 rounded-xl mt-1 font-black text-xl"
                       placeholder="0" required disabled>
            </div>

            <button type="submit" id="btn-produksi"
                    class="w-full bg-slate-900 text-white font-black py-4 rounded-xl shadow-lg opacity-50 cursor-not-allowed" disabled>
                MULAI PROSES
            </button>
        </form>
    </div>
    @else
    <div class="bg-amber-50 text-amber-600 p-4 rounded-xl font-bold border border-amber-200 flex items-center gap-2">
        <i data-lucide="eye"></i> Mode Reviewer: Anda hanya dapat melihat data.
    </div>
    @endif

    {{-- Tabel WIP --}}
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <div class="p-4 bg-slate-50 border-b font-bold text-slate-700 flex justify-between items-center">
            Daftar Barang Dalam Proses (WIP)
            @if(!$canUpdate && !$canDelete)
            <span class="text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded-md">View Only</span>
            @endif
        </div>

        {{-- Desktop: tabel --}}
        <table class="hidden md:table w-full text-left text-sm">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="p-4">Barang</th>
                    <th class="p-4">Qty</th>
                    <th class="p-4">Tanggal Mulai</th>
                    @if($canUpdate || $canDelete)
                    <th class="p-4 text-right">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($wip_list as $w)
                <tr class="border-b">
                    <td class="p-4 font-bold text-slate-700">{{ $w->nama_produk }}</td>
                    <td class="p-4 font-black">{{ $w->qty }}</td>
                    <td class="p-4 text-xs text-slate-400">{{ $w->created_at->format('d M Y, H:i') }}</td>
                    @if($canUpdate || $canDelete)
                    <td class="p-4 text-right">
                        <form method="POST" action="{{ route('production.complete', $w) }}">
                            @csrf
                            <button class="bg-emerald-500 text-white font-bold px-4 py-2 rounded-lg text-xs hover:bg-emerald-600 shadow-md">
                                SELESAIKAN & KELUARKAN
                            </button>
                        </form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-10 text-center text-slate-400 font-bold">
                        Belum ada barang yang sedang diproses.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Mobile: kartu --}}
        <div class="md:hidden divide-y">
            @forelse($wip_list as $w)
            <div class="p-4 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-black text-slate-800 truncate">{{ $w->nama_produk }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $w->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Qty</p>
                        <p class="text-xl font-black text-slate-800 leading-none">{{ $w->qty }}</p>
                    </div>
                </div>
                @if($canUpdate || $canDelete)
                <form method="POST" action="{{ route('production.complete', $w) }}">
                    @csrf
                    <button class="w-full bg-emerald-500 text-white font-bold px-4 py-3 rounded-lg text-xs hover:bg-emerald-600 shadow-md">
                        SELESAIKAN & KELUARKAN
                    </button>
                </form>
                @endif
            </div>
            @empty
            <div class="p-10 text-center text-slate-400 font-bold">
                Belum ada barang yang sedang diproses.
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Data resep dari server → JavaScript
    @php
        $resepMap = [];
        foreach ($products as $p) {
            $resepMap[$p->id] = $p->reseps->map(function ($r) {
                return [
                    'id_bahan'   => $r->id_bahan,
                    'nama_bahan' => $r->bahanMentah->nama,
                    'satuan'     => $r->bahanMentah->satuan,
                    'stok'       => $r->bahanMentah->stok,
                    'qty_butuh'  => $r->qty_butuh,
                ];
            })->values();
        }
    @endphp
    const dbResep = @json($resepMap);

    function kalkulasiKapasitas() {
        const id        = document.getElementById('select-produk').value;
        const infoDiv   = document.getElementById('info-produksi');
        const listBahan = document.getElementById('list-bahan-dibutuhkan');
        const maxLabel  = document.getElementById('max-qty-label');
        const inputQty  = document.getElementById('input-qty-produksi');
        const btnProses = document.getElementById('btn-produksi');

        if (!id || !dbResep[id] || dbResep[id].length === 0) {
            infoDiv.classList.add('hidden');
            inputQty.disabled = true; inputQty.value = '';
            btnProses.disabled = true; btnProses.classList.add('opacity-50','cursor-not-allowed');
            return;
        }

        let maxProduksi = Infinity;
        listBahan.innerHTML = '';

        dbResep[id].forEach(r => {
            const potensi = Math.floor(r.stok / r.qty_butuh);
            if (potensi < maxProduksi) maxProduksi = potensi;

            const li = document.createElement('li');
            li.className = 'flex justify-between';
            li.innerHTML = `<span>${r.nama_bahan} (Butuh ${r.qty_butuh}/unit)</span>
                            <span class="font-black text-slate-500">Stok: ${r.stok} ${r.satuan}</span>`;
            listBahan.appendChild(li);
        });

        maxLabel.innerText = `${maxProduksi} Unit`;
        infoDiv.classList.remove('hidden');

        if (maxProduksi > 0) {
            inputQty.disabled = false; inputQty.max = maxProduksi;
            btnProses.disabled = false; btnProses.classList.remove('opacity-50','cursor-not-allowed');
        } else {
            inputQty.disabled = true;
            btnProses.disabled = true; btnProses.classList.add('opacity-50','cursor-not-allowed');
        }
        lucide.createIcons();
    }
</script>
@endpush
