@extends('layouts.app')

@section('title', 'Kelola Akun')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800">Kelola Akun Pengguna</h2>
    <p class="text-sm text-slate-500 mt-1">Tambahkan akun Petugas RPH, Admin, dan Pimpinan di sini.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-6 border-b border-slate-100 bg-slate-50/50">
        <button onclick="toggleModal('modalTambah')" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold rounded-xl hover:from-emerald-600 hover:to-teal-600 transition-all shadow-md shadow-emerald-500/30 flex items-center gap-2 transform hover:-translate-y-0.5">
            <i class="fas fa-user-plus"></i> Tambah Akun
        </button>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">Nama</th>
                    <th class="px-6 py-4">Username</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($users as $index => $user)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 text-slate-500">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 font-medium text-slate-800">{{ $user->name }}</td>
                    <td class="px-6 py-4 text-slate-600">{{ $user->username }}</td>
                    <td class="px-6 py-4 text-slate-600">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-slate-600"><span class="px-2.5 py-1 text-xs font-bold rounded-lg uppercase {{ $user->role === 'admin' ? 'bg-red-100 text-red-600' : ($user->role === 'petugas' ? 'bg-blue-100 text-blue-600' : 'bg-emerald-100 text-emerald-600') }}">{{ str_replace('_', ' ', $user->role) }}</span></td>
                    <td class="px-6 py-4">
                        <div class="flex justify-center gap-2">
                            <button onclick="editUser({{ $user }})" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if(auth()->id() !== $user->id)
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus akun ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div id="modalTambah" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform scale-95 opacity-0 transition-all duration-300">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800">Tambah Akun</h3>
            <button onclick="toggleModal('modalTambah')" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="{{ route('users.store') }}" method="POST" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama</label>
                    <input type="text" name="name" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Username</label>
                    <input type="text" name="username" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Role</label>
                    <select name="role" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                        <option value="petugas">Petugas RPH</option>
                        <option value="admin">Admin</option>
                        <option value="pimpinan">Pimpinan</option>
                        <option value="pekerja_idul_adha">Pekerja Idul Adha</option>
                    </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modalTambah')" class="px-4 py-2 text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl font-medium transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-500 text-white rounded-xl font-medium hover:bg-emerald-600 transition-colors">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform scale-95 opacity-0 transition-all duration-300">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800">Edit Akun</h3>
            <button onclick="toggleModal('modalEdit')" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="formEdit" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama</label>
                    <input type="text" name="name" id="editName" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Username</label>
                    <input type="text" name="username" id="editUsername" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" id="editEmail" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Role</label>
                    <select name="role" id="editRole" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                        <option value="petugas">Petugas RPH</option>
                        <option value="admin">Admin</option>
                        <option value="pimpinan">Pimpinan</option>
                        <option value="pekerja_idul_adha">Pekerja Idul Adha</option>
                    </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modalEdit')" class="px-4 py-2 text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl font-medium transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-500 text-white rounded-xl font-medium hover:bg-emerald-600 transition-colors">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(id) {
        const modal = document.getElementById(id);
        const modalContent = modal.querySelector('div');
        
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        } else {
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }
    }

    function editUser(user) {
        document.getElementById('editName').value = user.name;
        document.getElementById('editUsername').value = user.username;
        document.getElementById('editEmail').value = user.email;
        document.getElementById('editRole').value = user.role;
        document.getElementById('formEdit').action = `/users/${user.id}`;
        toggleModal('modalEdit');
    }
</script>
@endsection
