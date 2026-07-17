@extends('layouts.app')
@section('title', 'Barang Keluar – PabrikPro')
@section('page_title', 'Riwayat Barang Keluar')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <p class="text-slate-400 text-sm">Daftar barang jadi yang telah diselesaikan dari proses produksi.</p>
        </div>
        {{-- Export Excel: bisa diganti pakai maatwebsite/excel --}}
        <a href="{{ route('production.outbound') }}?export=1"
           class="bg-emerald-600 text-white px-6 py-3 rounded-xl font-bold flex items-center gap-2 shadow-lg hover:bg-emerald-700 transition-all">
            <i data-lucide="file-spreadsheet"></i> EXPORT EXCEL
        </a>
    </div>

    @forelse($history as $date => $items)
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <div class="bg-slate-100 px-6 py-4 border-b font-black text-slate-700 flex items-center gap-2">
            <i data-lucide="calendar" class="text-emerald-600"></i>
            {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="p-4 w-1/4">Waktu</th>
                    <th class="p-4 w-1/2">Nama Barang</th>
                    <th class="p-4 w-1/4">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $h)
                <tr class="border-b hover:bg-slate-50 transition-colors">
                    <td class="p-4 text-slate-500 font-medium">{{ $h->created_at->format('H:i') }} WIB</td>
                    <td class="p-4 font-black text-slate-700">{{ strtoupper($h->nama_barang) }}</td>
                    <td class="p-4 font-black text-emerald-600">
                        {{ $h->qty }} <span class="text-[10px] text-slate-400 uppercase tracking-widest ml-1">Unit</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @empty
    <div class="bg-white rounded-3xl border shadow-sm p-20 text-center text-slate-300 font-bold">
        Belum ada riwayat barang keluar.
    </div>
    @endforelse

</div>
@endsection
