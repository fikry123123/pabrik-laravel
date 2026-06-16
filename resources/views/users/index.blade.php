@extends('layouts.app')
@section('title', 'Manajemen User – PabrikPro')
@section('page_title', 'Manajemen User')

@section('content')
<div class="space-y-6">

    {{-- Form Tambah / Edit --}}
    <div class="bg-white p-6 rounded-2xl border shadow-sm max-w-3xl" id="user-form-wrapper">
        <h3 id="form-user-title" class="font-bold mb-4">Tambah Akses User Baru</h3>

        {{-- Form Tambah --}}
        <form method="POST" action="{{ route('users.store') }}" id="form-tambah-user" class="flex gap-4 items-end">
            @csrf
            <div class="flex-1">
                <label class="text-xs font-bold text-slate-400 uppercase">Username</label>
                <input type="text" name="username" class="w-full p-3 border rounded-xl bg-slate-50 mt-1" required>
            </div>
            <div class="flex-1">
                <label class="text-xs font-bold text-slate-400 uppercase">Password</label>
                <input type="password" name="password" class="w-full p-3 border rounded-xl bg-slate-50 mt-1" required>
            </div>
            <div class="w-40">
                <label class="text-xs font-bold text-slate-400 uppercase">Role</label>
                <select name="role" class="w-full p-3 border rounded-xl bg-slate-50 mt-1" required>
                    <option value="editor">Editor</option>
                    <option value="reviewer">Reviewer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-bold h-[50px]">Simpan</button>
        </form>

        {{-- Form Edit (hidden by default) --}}
        <form method="POST" id="form-edit-user" action="" class="flex gap-4 items-end hidden">
            @csrf @method('PUT')
            <div class="flex-1">
                <label class="text-xs font-bold text-slate-400 uppercase">Username</label>
                <input type="text" name="username" id="edit-username" class="w-full p-3 border rounded-xl bg-slate-50 mt-1" required>
            </div>
            <div class="flex-1">
                <label class="text-xs font-bold text-slate-400 uppercase">Password</label>
                <input type="password" name="password" id="edit-password" placeholder="Kosongkan jika tidak diubah"
                       class="w-full p-3 border rounded-xl bg-slate-50 mt-1">
            </div>
            <div class="w-40">
                <label class="text-xs font-bold text-slate-400 uppercase">Role</label>
                <select name="role" id="edit-role" class="w-full p-3 border rounded-xl bg-slate-50 mt-1" required>
                    <option value="editor">Editor</option>
                    <option value="reviewer">Reviewer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="bg-amber-500 text-white px-6 py-3 rounded-xl font-bold h-[50px]">Update</button>
            <button type="button" onclick="resetUserForm()" class="bg-slate-200 px-4 rounded-xl h-[50px]">
                <i data-lucide="x"></i>
            </button>
        </form>
    </div>

    {{-- Tabel Users --}}
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="p-4">Username</th>
                    <th class="p-4">Role</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                <tr class="border-b">
                    <td class="p-4 font-bold text-slate-700">{{ $u->username }}</td>
                    <td class="p-4">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                            {{ $u->role === 'admin' ? 'bg-emerald-100 text-emerald-700' : ($u->role === 'editor' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ $u->role }}
                        </span>
                    </td>
                    <td class="p-4 text-right flex justify-end gap-2">
                        <button onclick="editUser({{ $u->id }}, '{{ $u->username }}', '{{ $u->role }}')"
                                class="text-blue-500 bg-blue-50 p-2 rounded-lg">
                            <i data-lucide="edit" size="16"></i>
                        </button>
                        @if($u->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy', $u) }}"
                              onsubmit="return confirm('Hapus akses user ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-rose-500 bg-rose-50 p-2 rounded-lg">
                                <i data-lucide="trash-2" size="16"></i>
                            </button>
                        </form>
                        @else
                        <div class="bg-slate-100 px-3 py-2 rounded-lg">
                            <span class="text-xs text-slate-400 font-bold">Anda Sendiri</span>
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const usersBaseUrl = "{{ url('users') }}";

    function editUser(id, username, role) {
        document.getElementById('form-tambah-user').classList.add('hidden');
        const formEdit = document.getElementById('form-edit-user');
        formEdit.classList.remove('hidden');
        formEdit.action = `${usersBaseUrl}/${id}`;

        document.getElementById('edit-username').value = username;
        document.getElementById('edit-role').value     = role;
        document.getElementById('edit-password').value = '';

        document.getElementById('user-form-wrapper').scrollIntoView({ behavior: 'smooth' });
    }

    function resetUserForm() {
        document.getElementById('form-tambah-user').classList.remove('hidden');
        document.getElementById('form-edit-user').classList.add('hidden');
    }

    lucide.createIcons();
</script>
@endpush
