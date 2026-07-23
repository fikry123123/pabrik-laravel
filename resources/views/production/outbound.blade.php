@extends('layouts.app')
@section('title', 'Barang Keluar – PabrikPro')
@section('page_title', 'Riwayat Barang Keluar')

@php($canManage = !auth()->user()->isReviewer())

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <p class="text-slate-400 text-sm">Daftar barang jadi yang telah diselesaikan dari proses produksi.</p>
        </div>
        {{-- Export Excel: bisa diganti pakai maatwebsite/excel --}}
        <a href="{{ route('production.outbound') }}?export=1"
           class="bg-emerald-600 text-white px-6 py-3 rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg hover:bg-emerald-700 transition-all whitespace-nowrap">
            <i data-lucide="file-spreadsheet"></i> EXPORT EXCEL
        </a>
    </div>

    @forelse($history as $date => $items)
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <div class="bg-slate-100 px-6 py-4 border-b font-black text-slate-700 flex items-center gap-2">
            <i data-lucide="calendar" class="text-emerald-600"></i>
            {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
        </div>
        <div class="overflow-x-auto">
        <table class="w-full text-left text-sm min-w-[520px]">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="p-4 w-1/4">Waktu</th>
                    <th class="p-4 {{ $canManage ? 'w-2/5' : 'w-1/2' }}">Nama Barang</th>
                    <th class="p-4 w-1/4">Qty</th>
                    @if($canManage)
                    <th class="p-4 text-right">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($items as $h)
                <tr class="border-b hover:bg-slate-50 transition-colors">
                    <td class="p-4 text-slate-500 font-medium whitespace-nowrap">{{ $h->created_at->format('H:i') }} WIB</td>
                    <td class="p-4 font-black text-slate-700">{{ strtoupper($h->nama_barang) }}</td>
                    <td class="p-4 font-black text-emerald-600 whitespace-nowrap">
                        {{ $h->qty }} <span class="text-[10px] text-slate-400 uppercase tracking-widest ml-1">Unit</span>
                    </td>
                    @if($canManage)
                    <td class="p-4">
                        <div class="flex justify-end gap-2">
                            {{-- Edit --}}
                            <button type="button"
                                    onclick="editBarangKeluar({{ $h->id }}, '{{ addslashes($h->nama_barang) }}', {{ $h->qty }})"
                                    class="text-blue-500 bg-blue-50 p-2 rounded-lg hover:bg-blue-100 transition-colors" title="Edit">
                                <i data-lucide="edit" size="16"></i>
                            </button>
                            {{-- Hapus --}}
                            <form method="POST" action="{{ route('production.outbound.destroy', $h) }}"
                                  onsubmit="return confirm('Hapus data barang keluar ini? Tindakan ini tidak bisa dibatalkan.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-500 bg-rose-50 p-2 rounded-lg hover:bg-rose-100 transition-colors" title="Hapus">
                                    <i data-lucide="trash-2" size="16"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-3xl border shadow-sm p-20 text-center text-slate-300 font-bold">
        Belum ada riwayat barang keluar.
    </div>
    @endforelse

</div>

@if($canManage)
{{-- ─── Modal Edit Barang Keluar ──────────────────────────────────────────── --}}
<div id="modal-edit-bk"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeEditBarangKeluar()"></div>

    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md border border-slate-200">
        <div class="flex items-center justify-between p-6 border-b">
            <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">
                <i data-lucide="edit" class="text-blue-500"></i> Edit Barang Keluar
            </h3>
            <button type="button" onclick="closeEditBarangKeluar()"
                    class="text-slate-400 hover:text-slate-700 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                <i data-lucide="x"></i>
            </button>
        </div>

        <form method="POST" id="form-edit-bk" action="" class="p-6 space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Nama Barang</label>
                <input type="text" name="nama_barang" id="bk-nama"
                       class="w-full p-3 border bg-slate-50 rounded-xl mt-1 font-bold focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all" required>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Qty (Unit)</label>
                <input type="number" name="qty" id="bk-qty" min="1"
                       class="w-full p-3 border bg-slate-50 rounded-xl mt-1 font-black text-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all" required>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white font-black py-3 rounded-xl shadow-lg hover:bg-blue-700 transition-all">
                    SIMPAN PERUBAHAN
                </button>
                <button type="button" onclick="closeEditBarangKeluar()" class="bg-slate-200 px-5 rounded-xl font-bold hover:bg-slate-300 transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@if($canManage)
@push('scripts')
<script>
    const outboundBaseUrl = "{{ url('production/outbound') }}";
    const modalBK = document.getElementById('modal-edit-bk');

    function editBarangKeluar(id, nama, qty) {
        document.getElementById('form-edit-bk').action = `${outboundBaseUrl}/${id}`;
        document.getElementById('bk-nama').value = nama;
        document.getElementById('bk-qty').value  = qty;

        modalBK.classList.remove('hidden');
        modalBK.classList.add('flex');
        document.body.classList.add('overflow-hidden');
        lucide.createIcons();
    }

    function closeEditBarangKeluar() {
        modalBK.classList.add('hidden');
        modalBK.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeEditBarangKeluar(); });

    lucide.createIcons();
</script>
@endpush
@endif
