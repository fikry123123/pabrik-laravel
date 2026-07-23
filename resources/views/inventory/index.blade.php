@extends('layouts.app')
@section('title', 'Data Bahan Baku – PabrikPro')
@section('page_title', 'Data Bahan Baku')

@section('content')
<div class="space-y-6">

    {{-- Form Tambah / Edit --}}
    @if(!auth()->user()->isReviewer())
    <div class="bg-white p-6 rounded-2xl border shadow-sm">
        <h3 class="font-bold mb-4" id="form-title">Tambah Bahan Baku Baru</h3>

        {{-- Form Tambah --}}
        <form method="POST" action="{{ route('inventory.store') }}" id="form-tambah-bahan" class="flex flex-col sm:flex-row gap-3 sm:gap-4">
            @csrf
            <input type="text"   name="nama"   placeholder="Nama Bahan"        class="flex-1 p-3 border rounded-xl bg-slate-50" required>
            <input type="number" name="stok"   placeholder="Stok" step="0.1"   class="w-full sm:w-32 p-3 border rounded-xl bg-slate-50" required>
            <input type="text"   name="satuan" placeholder="Satuan (Pcs/Kg)"   class="w-full sm:w-36 p-3 border rounded-xl bg-slate-50" required>
            <button type="submit" class="bg-slate-900 text-white px-6 py-3 rounded-xl font-bold whitespace-nowrap">Simpan</button>
        </form>

        {{-- Form Edit (hidden by default) --}}
        <form id="form-edit-bahan" method="POST" action="" class="flex flex-col sm:flex-row gap-3 sm:gap-4" style="display: none;">
            @csrf
            @method('PUT')
            <input type="text"   name="nama"   placeholder="Nama Bahan"      class="flex-1 p-3 border rounded-xl bg-slate-50" required>
            <input type="number" name="stok"   placeholder="Stok" step="0.1" class="w-full sm:w-32 p-3 border rounded-xl bg-slate-50" required>
            <input type="text"   name="satuan" placeholder="Satuan"           class="w-full sm:w-36 p-3 border rounded-xl bg-slate-50" required>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 sm:flex-none bg-amber-500 text-white px-6 py-3 rounded-xl font-bold">Update</button>
                <button type="button" onclick="resetEditBahan()" class="bg-slate-200 px-4 rounded-xl">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="bg-amber-50 text-amber-600 p-4 rounded-xl font-bold border border-amber-200 flex items-center gap-2">
        <i data-lucide="eye"></i> Mode Reviewer: Anda hanya dapat melihat data.
    </div>
    @endif

    {{-- Data Bahan Baku --}}
    @php $canManage = ! auth()->user()->isReviewer(); @endphp
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">

        {{-- Desktop: tabel --}}
        <table class="hidden md:table w-full text-left text-sm">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="p-4">Nama</th>
                    <th class="p-4">Stok</th>
                    @if($canManage)
                    <th class="p-4 text-right">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($materials as $b)
                <tr class="border-b">
                    <td class="p-4 font-bold text-slate-700">{{ $b->nama }}</td>
                    <td class="p-4">{{ $b->stok }} <span class="text-xs text-slate-400">{{ $b->satuan }}</span></td>
                    @if($canManage)
                    <td class="p-4">
                        <div class="flex justify-end gap-2">
                            <button onclick="setEditBahan({{ $b->id }}, '{{ addslashes($b->nama) }}', {{ $b->stok }}, '{{ $b->satuan }}')"
                                    class="text-blue-500 bg-blue-50 p-2 rounded-lg hover:bg-blue-100 transition-colors" title="Edit">
                                <i data-lucide="edit" size="16"></i>
                            </button>
                            <form method="POST" action="{{ route('inventory.destroy', $b) }}"
                                  onsubmit="return confirm('Hapus? Resep yang pakai bahan ini akan ikut terhapus!')">
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
                    <td colspan="3" class="p-10 text-center text-slate-400 font-bold">Belum ada bahan baku.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Mobile: kartu --}}
        <div class="md:hidden divide-y">
            @forelse($materials as $b)
            <div class="p-4 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-black text-slate-800 truncate">{{ $b->nama }}</p>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Stok: <span class="font-bold text-slate-700">{{ $b->stok }}</span>
                        <span class="text-xs text-slate-400">{{ $b->satuan }}</span>
                    </p>
                </div>
                @if($canManage)
                <div class="flex gap-2 flex-shrink-0">
                    <button onclick="setEditBahan({{ $b->id }}, '{{ addslashes($b->nama) }}', {{ $b->stok }}, '{{ $b->satuan }}')"
                            class="text-blue-500 bg-blue-50 p-2.5 rounded-lg" title="Edit">
                        <i data-lucide="edit" size="16"></i>
                    </button>
                    <form method="POST" action="{{ route('inventory.destroy', $b) }}"
                          onsubmit="return confirm('Hapus? Resep yang pakai bahan ini akan ikut terhapus!')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-rose-500 bg-rose-50 p-2.5 rounded-lg" title="Hapus">
                            <i data-lucide="trash-2" size="16"></i>
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @empty
            <div class="p-10 text-center text-slate-400 font-bold">Belum ada bahan baku.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const inventoryBaseUrl = "{{ url('inventory') }}";

    function setEditBahan(id, nama, stok, satuan) {
        // Hide add form, show edit form
        document.getElementById('form-tambah-bahan').style.display = 'none';
        const editForm = document.getElementById('form-edit-bahan');
        editForm.style.display = 'flex';

        // Update title
        document.getElementById('form-title').innerText = 'Edit Bahan Baku';

        // Set action & values
        editForm.action = `${inventoryBaseUrl}/${id}`;
        editForm.querySelector('[name="nama"]').value   = nama;
        editForm.querySelector('[name="stok"]').value   = stok;
        editForm.querySelector('[name="satuan"]').value = satuan;

        lucide.createIcons();
    }

    function resetEditBahan() {
        // Show add form, hide edit form
        document.getElementById('form-tambah-bahan').style.display = 'flex';
        document.getElementById('form-edit-bahan').style.display = 'none';
        document.getElementById('form-title').innerText = 'Tambah Bahan Baku Baru';
    }

    lucide.createIcons();
</script>
@endpush
