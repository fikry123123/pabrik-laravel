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
        <form method="POST" action="{{ route('inventory.store') }}" id="form-tambah-bahan" class="flex gap-4">
            @csrf
            <input type="text"   name="nama"   placeholder="Nama Bahan"        class="flex-1 p-3 border rounded-xl bg-slate-50" required>
            <input type="number" name="stok"   placeholder="Stok" step="0.1"   class="w-32 p-3 border rounded-xl bg-slate-50" required>
            <input type="text"   name="satuan" placeholder="Satuan (Pcs/Kg)"   class="w-36 p-3 border rounded-xl bg-slate-50" required>
            <button type="submit" class="bg-slate-900 text-white px-6 py-3 rounded-xl font-bold">Simpan</button>
        </form>

        {{-- Form Edit (hidden by default) --}}
        <form id="form-edit-bahan" method="POST" action="" class="flex gap-4" style="display: none;">
            @csrf
            @method('PUT')
            <input type="text"   name="nama"   placeholder="Nama Bahan"      class="flex-1 p-3 border rounded-xl bg-slate-50" required>
            <input type="number" name="stok"   placeholder="Stok" step="0.1" class="w-32 p-3 border rounded-xl bg-slate-50" required>
            <input type="text"   name="satuan" placeholder="Satuan"           class="w-36 p-3 border rounded-xl bg-slate-50" required>
            <button type="submit" class="bg-amber-500 text-white px-6 py-3 rounded-xl font-bold">Update</button>
            <button type="button" onclick="resetEditBahan()" class="bg-slate-200 px-4 rounded-xl">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </form>
    </div>
    @else
    <div class="bg-amber-50 text-amber-600 p-4 rounded-xl font-bold border border-amber-200 flex items-center gap-2">
        <i data-lucide="eye"></i> Mode Reviewer: Anda hanya dapat melihat data.
    </div>
    @endif

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="p-4">Nama</th>
                    <th class="p-4">Stok</th>
                    @if(!auth()->user()->isReviewer())
                    <th class="p-4 text-right">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($materials as $b)
                <tr class="border-b">
                    <td class="p-4 font-bold text-slate-700">{{ $b->nama }}</td>
                    <td class="p-4">{{ $b->stok }} <span class="text-xs text-slate-400">{{ $b->satuan }}</span></td>
                    @if(!auth()->user()->isReviewer())
                    <td class="p-4 text-right flex justify-end gap-2">
                        {{-- Edit --}}
                        <button
                            onclick="setEditBahan({{ $b->id }}, '{{ addslashes($b->nama) }}', {{ $b->stok }}, '{{ $b->satuan }}')"
                            class="text-blue-500 bg-blue-50 p-2 rounded-lg">
                            <i data-lucide="edit" size="16"></i>
                        </button>
                        {{-- Hapus --}}
                        <form method="POST" action="{{ route('inventory.destroy', $b) }}"
                              onsubmit="return confirm('Hapus? Resep yang pakai bahan ini akan ikut terhapus!')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-rose-500 bg-rose-50 p-2 rounded-lg">
                                <i data-lucide="trash-2" size="16"></i>
                            </button>
                        </form>
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
