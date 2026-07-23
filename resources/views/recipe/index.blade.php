@extends('layouts.app')
@section('title', 'Resep Produk – PabrikPro')
@section('page_title', 'Resep Produk')

@section('content')
<div class="space-y-8">

    {{-- Form Tambah / Edit --}}
    @if(!auth()->user()->isReviewer())
    <div class="bg-white p-5 md:p-8 rounded-3xl shadow-sm border max-w-2xl" id="recipe-form-wrapper">
        <h3 id="form-resep-title" class="text-lg font-black mb-6">Buat Master Barang & Resep Baru</h3>

        <form method="POST" id="form-resep" action="{{ route('recipes.store') }}" class="space-y-6">
            @csrf
            <span id="method-spoof"></span>
            <input type="hidden" name="id_produk" id="resep-id-produk" value="">

            {{-- Nama Produk --}}
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Nama Barang</label>
                <input type="text" name="nama_produk" id="resep-nama-produk"
                       class="w-full p-4 border bg-slate-50 rounded-xl mt-1 font-bold"
                       placeholder="Misal: Lemari Kaca" required>
            </div>

            {{-- BOM Rows --}}
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase flex justify-between items-end mb-2">
                    <span>Komponen Bahan Baku (BOM)</span>
                    <button type="button" onclick="tambahBarisResep()"
                            class="text-blue-600 bg-blue-50 px-3 py-1 rounded-lg flex items-center gap-1">
                        + Tambah Bahan
                    </button>
                </label>
                <div id="resep-container" class="space-y-3">
                    @include('recipe._bom_row')
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" id="resep-btn-submit"
                        class="flex-1 bg-blue-600 text-white font-black py-4 rounded-xl shadow-lg">
                    SIMPAN MASTER RESEP
                </button>
                <button type="button" onclick="resetResepForm()"
                        class="bg-slate-200 px-6 rounded-xl font-bold">
                    <i data-lucide="rotate-ccw"></i>
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="bg-amber-50 text-amber-600 p-4 rounded-xl font-bold border border-amber-200">
        Mode Reviewer: Anda tidak diizinkan menambahkan resep baru.
    </div>
    @endif

    {{-- Daftar Produk --}}
    @php $canManage = ! auth()->user()->isReviewer(); @endphp
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <div class="p-4 bg-slate-50 border-b font-bold text-slate-700">Daftar Master Produk & BOM</div>

        {{-- Desktop: tabel --}}
        <table class="hidden md:table w-full text-left text-sm">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="p-4">Nama Produk</th>
                    <th class="p-4">Komposisi Bahan</th>
                    @if($canManage)
                    <th class="p-4 text-right">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                <tr class="border-b align-top">
                    <td class="p-4 font-bold text-slate-700">{{ $p->nama_produk }}</td>
                    <td class="p-4">
                        <div class="flex flex-wrap gap-2">
                            @foreach($p->reseps as $r)
                            <span class="bg-slate-100 px-2 py-1 rounded text-[11px]">
                                {{ $r->bahanMentah->nama }} ({{ $r->qty_butuh }})
                            </span>
                            @endforeach
                        </div>
                    </td>
                    @if($canManage)
                    <td class="p-4">
                        <div class="flex justify-end gap-2">
                            <button onclick="editResep({{ $p->id }}, '{{ addslashes($p->nama_produk) }}')"
                                    class="text-blue-500 bg-blue-50 p-2 rounded-lg hover:bg-blue-100 transition-colors" title="Edit">
                                <i data-lucide="edit" size="16"></i>
                            </button>
                            <form method="POST" action="{{ route('recipes.destroy', $p) }}"
                                  onsubmit="return confirm('Hapus master produk dan resep ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-500 bg-rose-50 p-2 rounded-lg hover:bg-rose-100 transition-colors" title="Hapus">
                                    <i data-lucide="trash-2" size="16"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="p-10 text-center text-slate-400 font-bold">Belum ada produk terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Mobile: kartu --}}
        <div class="md:hidden divide-y">
            @forelse($products as $p)
            <div class="p-4 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <p class="font-black text-slate-800">{{ $p->nama_produk }}</p>
                    @if($canManage)
                    <div class="flex gap-2 flex-shrink-0">
                        <button onclick="editResep({{ $p->id }}, '{{ addslashes($p->nama_produk) }}')"
                                class="text-blue-500 bg-blue-50 p-2 rounded-lg" title="Edit">
                            <i data-lucide="edit" size="16"></i>
                        </button>
                        <form method="POST" action="{{ route('recipes.destroy', $p) }}"
                              onsubmit="return confirm('Hapus master produk dan resep ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-rose-500 bg-rose-50 p-2 rounded-lg" title="Hapus">
                                <i data-lucide="trash-2" size="16"></i>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Komposisi Bahan</p>
                    <div class="flex flex-wrap gap-2">
                        @forelse($p->reseps as $r)
                        <span class="bg-slate-100 px-2 py-1 rounded text-[11px] font-medium">
                            {{ $r->bahanMentah->nama }} ({{ $r->qty_butuh }})
                        </span>
                        @empty
                        <span class="text-xs text-slate-400 italic">Belum ada komponen</span>
                        @endforelse
                    </div>
                </div>
            </div>
            @empty
            <div class="p-10 text-center text-slate-400 font-bold">Belum ada produk terdaftar.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const dbResepData  = @json($products->mapWithKeys(fn($p) => [$p->id => $p->reseps->map(fn($r) => ['id_bahan' => $r->id_bahan, 'qty_butuh' => $r->qty_butuh])]));
    const routeStore   = "{{ route('recipes.store') }}";
    const routeUpdate  = "{{ url('recipes') }}"; // /{id} ditambah via JS

    const bomRowHtml = () => `
        <div class="flex gap-2 mt-2 items-center resep-row">
            <select name="id_bahan[]" class="flex-1 min-w-0 p-3 border bg-slate-50 rounded-xl" required>
                <option value="">-- Pilih Bahan Baku --</option>
                @foreach($materials as $b)
                <option value="{{ $b->id }}">{{ $b->nama }} ({{ $b->satuan }})</option>
                @endforeach
            </select>
            <input type="number" step="0.1" name="qty_butuh[]" class="w-20 sm:w-28 flex-shrink-0 p-3 border bg-slate-50 rounded-xl" placeholder="Qty" required>
            <button type="button" onclick="this.closest('.resep-row').remove()"
                    class="w-12 h-12 flex-shrink-0 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center hover:bg-rose-100 transition-colors">
                <i data-lucide="trash-2" size="20"></i>
            </button>
        </div>`;

    function tambahBarisResep(idBahan = '', qty = '') {
        const container = document.getElementById('resep-container');
        const tmp = document.createElement('div');
        tmp.innerHTML = bomRowHtml();
        const row = tmp.firstElementChild;
        container.appendChild(row);
        if (idBahan) row.querySelector('select').value = idBahan;
        if (qty)     row.querySelector('input').value  = qty;
        lucide.createIcons();
    }

    function editResep(id, nama) {
        const form = document.getElementById('form-resep');
        document.getElementById('form-resep-title').innerText = 'Edit Master Produk & Resep';
        form.action = `${routeUpdate}/${id}`;

        // Inject method PUT
        document.getElementById('method-spoof').innerHTML =
            '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('resep-id-produk').value  = id;
        document.getElementById('resep-nama-produk').value = nama;

        const btn = document.getElementById('resep-btn-submit');
        btn.innerText = 'UPDATE MASTER RESEP';
        btn.classList.replace('bg-blue-600','bg-amber-500');

        // Isi baris BOM
        document.getElementById('resep-container').innerHTML = '';
        if (dbResepData[id]) {
            dbResepData[id].forEach(item => tambahBarisResep(item.id_bahan, item.qty_butuh));
        }

        document.getElementById('recipe-form-wrapper').scrollIntoView({ behavior: 'smooth' });
    }

    function resetResepForm() {
        const form = document.getElementById('form-resep');
        form.reset();
        form.action = routeStore;
        document.getElementById('method-spoof').innerHTML = '';
        document.getElementById('form-resep-title').innerText = 'Buat Master Barang & Resep Baru';
        document.getElementById('resep-id-produk').value = '';

        const btn = document.getElementById('resep-btn-submit');
        btn.innerText = 'SIMPAN MASTER RESEP';
        btn.classList.replace('bg-amber-500','bg-blue-600');

        document.getElementById('resep-container').innerHTML = '';
        tambahBarisResep(); // satu baris kosong default
    }
</script>
@endpush
