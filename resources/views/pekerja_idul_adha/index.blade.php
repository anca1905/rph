@extends('layouts.app')

@section('title', 'Kelola Pekerja Idul Adha')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800">Data Pekerja Idul Adha</h2>
    <p class="text-sm text-slate-500 mt-1">Kelola akun pekerja khusus untuk input laporan Idul Adha</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-6 border-b border-slate-100 bg-slate-50/50">
        <button onclick="toggleModal('modalTambah')" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold rounded-xl hover:from-emerald-600 hover:to-teal-600 transition-all shadow-md shadow-emerald-500/30 flex items-center gap-2 transform hover:-translate-y-0.5">
            <i class="fas fa-user-plus"></i> Tambah Pekerja
        </button>
    </div>

    <div class="w-full">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-200">
                <tr>
                    <th class="px-4 py-4 border-b border-slate-100 text-center w-16">No</th>
                    <th class="px-4 py-4 border-b border-slate-100">Nama Pekerja</th>
                    <th class="px-4 py-4 border-b border-slate-100">Username</th>
                    <th class="px-4 py-4 border-b border-slate-100">Email</th>
                    <th class="px-4 py-4 border-b border-slate-100">No. HP</th>
                    <th class="px-4 py-4 border-b border-slate-100 text-center sticky right-0 bg-slate-100 z-20 shadow-[-4px_0_6px_-4px_rgba(0,0,0,0.1)] w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-slate-600 divide-y divide-slate-50">
                @forelse($pekerja as $user)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-4 py-3 text-center">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $user->name }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-md bg-slate-100 text-slate-600 font-mono text-xs">{{ $user->username }}</span>
                        </td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ $user->no_hp ?? '-' }}</td>
                        <td class="px-4 py-3 text-center sticky right-0 bg-white group-hover:bg-slate-50 transition-colors z-10 shadow-[-4px_0_6px_-4px_rgba(0,0,0,0.1)]">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="editData({{ $user }})" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors" title="Edit Pekerja">
                                    <i class="fas fa-pen text-xs"></i>
                                </button>
                                <button onclick="hapusData('{{ route('pekerja.destroy', $user->id) }}', '{{ $user->name }}')" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="Hapus Pekerja">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-users-slash text-4xl mb-3 text-slate-300"></i>
                                <p class="font-medium text-slate-500">Belum ada data pekerja Idul Adha.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div id="modalTambah" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="toggleModal('modalTambah')"></div>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="relative bg-white rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:max-w-xl w-full flex flex-col max-h-[90vh] overflow-hidden border border-slate-100">
            <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4 flex justify-between items-center shrink-0">
                <h3 class="text-lg font-bold text-white"><i class="fas fa-user-plus mr-2 opacity-80"></i> Tambah Pekerja</h3>
                <button type="button" onclick="toggleModal('modalTambah')" class="text-emerald-100 hover:text-white transition-colors"><i class="fas fa-times text-xl"></i></button>
            </div>
            <form action="{{ route('pekerja.store') }}" method="POST" class="flex-1 overflow-y-auto">
                @csrf
                <div class="px-6 py-5 bg-slate-50/50 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Username <span class="text-red-500">*</span></label>
                        <input type="text" name="username" required class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:border-emerald-500 focus:outline-none" placeholder="Huruf kecil tanpa spasi">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Alamat Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nomor HP</label>
                        <input type="text" name="no_hp" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required minlength="8" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:border-emerald-500 focus:outline-none" placeholder="Minimal 8 karakter">
                    </div>
                </div>
                <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end gap-2 shrink-0">
                    <button type="button" onclick="toggleModal('modalTambah')" class="px-4 py-2 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition-colors text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-500 text-white font-semibold rounded-xl hover:bg-emerald-600 shadow-md shadow-emerald-500/30 transition-all text-sm"><i class="fas fa-save mr-1.5"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="toggleModal('modalEdit')"></div>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="relative bg-white rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:max-w-xl w-full flex flex-col max-h-[90vh] overflow-hidden border border-slate-100">
            <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4 flex justify-between items-center shrink-0">
                <h3 class="text-lg font-bold text-white"><i class="fas fa-pen mr-2 opacity-80"></i> Edit Pekerja</h3>
                <button type="button" onclick="toggleModal('modalEdit')" class="text-emerald-100 hover:text-white transition-colors"><i class="fas fa-times text-xl"></i></button>
            </div>
            <form id="formEdit" method="POST" class="flex-1 overflow-y-auto">
                @csrf
                @method('PUT')
                <div class="px-6 py-5 bg-slate-50/50 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="edit_name" required class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Username <span class="text-red-500">*</span></label>
                        <input type="text" name="username" id="edit_username" required class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Alamat Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="edit_email" required class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nomor HP</label>
                        <input type="text" name="no_hp" id="edit_nohp" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Password Baru</label>
                        <input type="password" name="password" minlength="8" class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:border-emerald-500 focus:outline-none" placeholder="Kosongkan jika tidak ingin ganti password">
                    </div>
                </div>
                <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end gap-2 shrink-0">
                    <button type="button" onclick="toggleModal('modalEdit')" class="px-4 py-2 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition-colors text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-500 text-white font-semibold rounded-xl hover:bg-emerald-600 shadow-md shadow-emerald-500/30 transition-all text-sm"><i class="fas fa-save mr-1.5"></i> Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Hapus (Hidden) -->
<form id="formHapus" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function toggleModal(modalID) {
        document.getElementById(modalID).classList.toggle('hidden');
    }

    function editData(data) {
        let actionUrl = "{{ route('pekerja.update', ':id') }}";
        actionUrl = actionUrl.replace(':id', data.id);
        document.getElementById('formEdit').action = actionUrl;
        
        document.getElementById('edit_name').value = data.name;
        document.getElementById('edit_username').value = data.username;
        document.getElementById('edit_email').value = data.email;
        document.getElementById('edit_nohp').value = data.no_hp || '';
        
        toggleModal('modalEdit');
    }

    function hapusData(url, name) {
        Swal.fire({
            title: 'Hapus Pekerja?',
            text: "Anda yakin ingin menghapus akun " + name + "?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.getElementById('formHapus');
                form.action = url;
                form.submit();
            }
        });
    }
</script>
@endsection
