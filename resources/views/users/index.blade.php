@extends('layouts.app')
@section('title', 'Manajemen User – PabrikPro')
@section('page_title', 'Manajemen User')

@section('content')
<div class="space-y-6">

    <div class="space-y-6">
        {{-- Form Tambah / Edit --}}
        <div class="bg-white p-6 rounded-2xl border shadow-sm max-w-3xl" id="user-form-wrapper">
            <h3 id="form-user-title" class="font-bold mb-4">Tambah Akses User Baru</h3>

            {{-- Alerts --}}
            @if(session('success'))
            <div class="mb-4 p-3 rounded-lg bg-emerald-50 text-emerald-700 font-bold">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="mb-4 p-3 rounded-lg bg-rose-50 text-rose-700 font-bold">{{ session('error') }}</div>
            @endif
            @if($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-amber-50 text-amber-700">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Form Tambah --}}
            <form method="POST" action="{{ route('users.store') }}" id="form-tambah-user" class="flex flex-col sm:flex-row gap-3 sm:gap-4 sm:items-end">
                @csrf
                <div class="flex-1">
                    <label class="text-xs font-bold text-slate-400 uppercase">Username</label>
                    <input type="text" name="username" class="w-full p-3 border rounded-xl bg-slate-50 mt-1" required>
                </div>
                <div class="flex-1">
                    <label class="text-xs font-bold text-slate-400 uppercase">Password</label>
                    <input type="password" name="password" class="w-full p-3 border rounded-xl bg-slate-50 mt-1" required>
                </div>
                <div class="w-full sm:w-40">
                    <label class="text-xs font-bold text-slate-400 uppercase">Role</label>
                    <select name="role" class="w-full p-3 border rounded-xl bg-slate-50 mt-1" required>
                        <option value="editor">Editor</option>
                        <option value="reviewer">Reviewer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div id="create-feature-fields" class="w-full sm:w-auto">
                    <label class="text-xs font-bold text-slate-400 uppercase">Akses Kelola Fitur</label>
                    <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
                        @foreach($features as $feature)
                        <label class="flex items-center gap-1"><input type="checkbox" name="features[]" value="{{ $feature }}"> {{ $feature === 'bahan_baku' ? 'Bahan Baku' : ($feature === 'resep' ? 'Resep' : ($feature === 'produksi' ? 'Produksi' : 'Barang Keluar')) }}</label>
                        @endforeach
                    </div>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-bold h-[50px] whitespace-nowrap">Simpan</button>
            </form>

            {{-- Form Edit (hidden by default) --}}
            <form method="POST" id="form-edit-user" action="" class="flex flex-col sm:flex-row gap-3 sm:gap-4 sm:items-end hidden">
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
                <div class="w-full sm:w-40">
                    <label class="text-xs font-bold text-slate-400 uppercase">Role</label>
                    <select name="role" id="edit-role" class="w-full p-3 border rounded-xl bg-slate-50 mt-1" required>
                        <option value="editor">Editor</option>
                        <option value="reviewer">Reviewer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div id="edit-feature-fields" class="w-full sm:w-auto">
                    <label class="text-xs font-bold text-slate-400 uppercase">Akses Kelola Fitur</label>
                    <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
                        @foreach($features as $feature)
                        <label class="flex items-center gap-1"><input type="checkbox" name="features[]" value="{{ $feature }}" class="edit-feature"> {{ $feature === 'bahan_baku' ? 'Bahan Baku' : ($feature === 'resep' ? 'Resep' : ($feature === 'produksi' ? 'Produksi' : 'Barang Keluar')) }}</label>
                        @endforeach
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 sm:flex-none bg-amber-500 text-white px-6 py-3 rounded-xl font-bold h-[50px]">Update</button>
                    <button type="button" onclick="resetUserForm()" class="bg-slate-200 px-4 rounded-xl h-[50px]">
                        <i data-lucide="x"></i>
                    </button>
                </div>
            </form>
        </div>

        {{-- Data Users --}}
        <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">

            {{-- Desktop: tabel --}}
            <table class="hidden md:table w-full text-left text-sm">
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
                        <td class="p-4">
                            <div class="flex justify-end gap-2">
                                <button onclick='editUser({{ $u->id }}, @json($u->username), @json($u->role), @json($u->permissions->where("can_manage", true)->pluck("feature")->values()))'
                                        class="text-blue-500 bg-blue-50 p-2 rounded-lg hover:bg-blue-100 transition-colors" title="Edit">
                                    <i data-lucide="edit" size="16"></i>
                                </button>
                                @if($u->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $u) }}"
                                      onsubmit="return confirm('Hapus akses user ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-rose-500 bg-rose-50 p-2 rounded-lg hover:bg-rose-100 transition-colors" title="Hapus">
                                        <i data-lucide="trash-2" size="16"></i>
                                    </button>
                                </form>
                                @else
                                <div class="bg-slate-100 px-3 py-2 rounded-lg">
                                    <span class="text-xs text-slate-400 font-bold">Anda Sendiri</span>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Mobile: kartu --}}
            <div class="md:hidden divide-y">
                @foreach($users as $u)
                <div class="p-4 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-black text-slate-800 truncate">{{ $u->username }}</p>
                        <span class="inline-block mt-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                            {{ $u->role === 'admin' ? 'bg-emerald-100 text-emerald-700' : ($u->role === 'editor' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ $u->role }}
                        </span>
                    </div>
                    <div class="flex gap-2 flex-shrink-0">
                        <button onclick='editUser({{ $u->id }}, @json($u->username), @json($u->role), @json($u->permissions->where("can_manage", true)->pluck("feature")->values()))'
                                class="text-blue-500 bg-blue-50 p-2.5 rounded-lg" title="Edit">
                            <i data-lucide="edit" size="16"></i>
                        </button>
                        @if($u->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy', $u) }}"
                              onsubmit="return confirm('Hapus akses user ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-rose-500 bg-rose-50 p-2.5 rounded-lg" title="Hapus">
                                <i data-lucide="trash-2" size="16"></i>
                            </button>
                        </form>
                        @else
                        <span class="bg-slate-100 px-3 py-2 rounded-lg text-xs text-slate-400 font-bold whitespace-nowrap">Anda</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- TAB: Pengaturan Akses --}}
    <div id="content-permissions" class="hidden space-y-6">
        <div class="bg-blue-50 text-blue-700 p-4 rounded-xl border border-blue-200">
            <p class="font-bold flex items-center gap-2">
                <i data-lucide="info" size="18"></i>
                Atur fitur mana saja yang dapat diakses setiap role. Fitur yang tidak diaktifkan hanya bisa dilihat (read-only).
            </p>
        </div>

        @foreach($users->whereNotIn('role', ['admin', 'reviewer']) as $user)
        <div class="bg-white p-6 rounded-2xl border shadow-sm">
            <h3 class="font-bold text-lg">{{ $user->username }}</h3>
            <p class="mb-4 text-sm text-slate-500">Role: {{ ucfirst($user->role) }}. Semua fitur tetap dapat dilihat; checklist memberi hak tambah, ubah, dan hapus.</p>

            <form method="POST" action="{{ route('users.permissions.update', $user) }}" class="space-y-4">
                @csrf @method('PUT')

                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach($features as $feature)
                    @php($permission = $user->permissions->firstWhere('feature', $feature))
                    <label class="flex items-center gap-3 rounded-xl border p-4 cursor-pointer hover:bg-slate-50">
                        <input type="checkbox" name="features[]" value="{{ $feature }}" {{ $permission?->can_manage ? 'checked' : '' }} class="w-5 h-5 accent-blue-600">
                        <span class="font-bold text-slate-700">{{ $feature === 'bahan_baku' ? 'Data Bahan Baku' : ($feature === 'resep' ? 'Resep Produk' : ($feature === 'produksi' ? 'Produksi' : 'Barang Keluar')) }}</span>
                    </label>
                    @endforeach
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-green-700 transition-colors flex items-center gap-2">
                        <i data-lucide="save" size="18"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
    const usersBaseUrl = "{{ url('users') }}";

    function switchTab(tab) {
        // Hide all content
        document.getElementById('content-users').classList.add('hidden');
        document.getElementById('content-permissions').classList.add('hidden');

        // Reset tab buttons
        document.getElementById('tab-users').classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
        document.getElementById('tab-users').classList.add('text-slate-500');
        document.getElementById('tab-permissions').classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
        document.getElementById('tab-permissions').classList.add('text-slate-500');

        // Show selected content
        if (tab === 'users') {
            document.getElementById('content-users').classList.remove('hidden');
            document.getElementById('tab-users').classList.remove('text-slate-500');
            document.getElementById('tab-users').classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
        } else {
            document.getElementById('content-permissions').classList.remove('hidden');
            document.getElementById('tab-permissions').classList.remove('text-slate-500');
            document.getElementById('tab-permissions').classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
        }
    }

    function toggleFeatureFields(prefix, role) {
        const fields = document.getElementById(`${prefix}-feature-fields`);
        const enabled = role === 'editor';
        fields.classList.toggle('hidden', !enabled);
        fields.querySelectorAll('input').forEach(input => input.disabled = !enabled);
    }

    document.querySelector('#form-tambah-user select[name="role"]').addEventListener('change', event => toggleFeatureFields('create', event.target.value));
    document.getElementById('edit-role').addEventListener('change', event => toggleFeatureFields('edit', event.target.value));
    toggleFeatureFields('create', document.querySelector('#form-tambah-user select[name="role"]').value);

    function editUser(id, username, role, features) {
        document.getElementById('form-tambah-user').classList.add('hidden');
        const formEdit = document.getElementById('form-edit-user');
        formEdit.classList.remove('hidden');
        formEdit.action = `${usersBaseUrl}/${id}`;

        document.getElementById('edit-username').value = username;
        document.getElementById('edit-role').value     = role;
        document.getElementById('edit-password').value = '';
        document.querySelectorAll('.edit-feature').forEach(input => {
            input.checked = features.includes(input.value);
        });
        toggleFeatureFields('edit', role);

        document.getElementById('user-form-wrapper').scrollIntoView({ behavior: 'smooth' });
    }

    function resetUserForm() {
        document.getElementById('form-tambah-user').classList.remove('hidden');
        document.getElementById('form-edit-user').classList.add('hidden');
    }

    lucide.createIcons();
</script>
@endpush
