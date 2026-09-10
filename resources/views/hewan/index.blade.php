@extends('layouts.app')

@section('title', 'Data Master Hewan')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">

    @if (session('success'))
        <div id="alert-success"
            class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-xl flex items-center justify-between shadow-sm transition-all duration-300">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-lg"></i>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="document.getElementById('alert-success').remove()"
                class="text-emerald-400 hover:text-emerald-600 transition-colors focus:outline-none" title="Tutup">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div id="alert-session-error"
            class="mb-4 p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl flex items-center justify-between shadow-sm transition-all duration-300">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-lg"></i>
                <span class="font-medium text-sm">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="document.getElementById('alert-session-error').remove()"
                class="text-red-400 hover:text-red-600 transition-colors focus:outline-none" title="Tutup">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div id="alert-error"
            class="mb-4 p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl shadow-sm flex justify-between items-start transition-all duration-300">
            <div>
                <div class="flex items-center gap-2 mb-2 font-bold">
                    <i class="fas fa-exclamation-triangle"></i> Terjadi Kesalahan Input:
                </div>
                <ul class="list-disc list-inside text-sm font-medium ml-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" onclick="document.getElementById('alert-error').remove()"
                class="text-red-400 hover:text-red-600 transition-colors focus:outline-none" title="Tutup">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
    @endif

    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">

        <div class="flex flex-col md:flex-row justify-between items-center mb-6 border-b border-slate-100 pb-4 gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Daftar Registrasi Hewan</h2>
                <p class="text-xs text-slate-500 mt-1">Kelola data hewan masuk berdasarkan sistem pendataan RPH</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <button onclick="exportExcel()"
                    class="px-4 py-2 bg-emerald-50 text-emerald-700 border border-emerald-200 text-sm font-semibold rounded-xl hover:bg-emerald-100 transition-colors shadow-sm flex items-center gap-2">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>

                @if(auth()->user()->role != 'pimpinan')
                    <button onclick="bukaModalKelolaJenis()"
                        class="px-4 py-2 bg-blue-50 text-blue-600 text-sm font-semibold rounded-xl hover:bg-blue-100 transition-colors shadow-sm flex items-center gap-2">
                        <i class="fas fa-tags"></i> Kelola Jenis Hewan
                    </button>

                    <button onclick="toggleModal('modalTambah')"
                        class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition-colors shadow-sm flex items-center gap-2">
                        <i class="fas fa-plus"></i> Tambah Data
                    </button>
                    <button onclick="toggleModal('modalImport')"
                        class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-600 transition-all shadow-md flex items-center gap-2">
                        <i class="fas fa-file-import"></i> Import Excel
                    </button>
                @endif
            </div>
        </div>

        <div class="w-full">
            <table id="tabelHewan" class="w-full text-left text-sm whitespace-nowrap">
                <thead
                    class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-4 border-b border-slate-100 text-center">No</th>
                        <th class="px-4 py-4 border-b border-slate-100">No. Registrasi</th>
                        <th class="px-4 py-4 border-b border-slate-100">Tanggal Masuk</th>
                        <th class="px-4 py-4 border-b border-slate-100">Nama Pemilik</th>
                        <th class="px-4 py-4 border-b border-slate-100">Asal Hewan</th>
                        <th class="px-4 py-4 border-b border-slate-100">Jenis Hewan</th>
                        <th class="px-4 py-4 border-b border-slate-100">Jenis Kelamin</th>
                        <th class="px-4 py-4 border-b border-slate-100">Umur</th>
                        <th class="px-4 py-4 border-b border-slate-100">Berat (Kg)</th>
                        <th class="px-4 py-4 border-b border-slate-100">Status</th>
                        @if(auth()->user()->role != 'pimpinan')
                            <th
                                class="px-4 py-4 border-b border-slate-100 text-center sticky right-0 bg-slate-100 z-20 shadow-[-4px_0_6px_-4px_rgba(0,0,0,0.1)]">
                                Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="text-slate-600 divide-y divide-slate-50">
                    @if (isset($hewans) && $hewans->count() > 0)
                        @foreach ($hewans as $hewan)
                            <tr class="group hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 text-center">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-bold text-slate-800">{{ $hewan->no_registrasi }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($hewan->tanggal_masuk)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-700">{{ $hewan->nama_pemilik }}</td>
                                <td class="px-4 py-3">{{ $hewan->asal_hewan }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $hewan->jenis_hewan }}</td>
                                <td class="px-4 py-3">{{ $hewan->jenis_kelamin }}</td>
                                <td class="px-4 py-3">{{ $hewan->umur ?? '-' }}</td>
                                <td class="px-4 py-3 font-semibold">{{ $hewan->berat ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusGlobal = $hewan->status ?? 'Menunggu Antemortem';
                                        $badgeStyle = 'bg-slate-50 text-slate-600 border-slate-200';
                                        $icon = 'fa-info-circle';

                                        if ($statusGlobal == 'Menunggu Antemortem') {
                                            $badgeStyle = 'bg-amber-50 text-amber-600 border-amber-200';
                                            $icon = 'fa-hourglass-half';
                                        } elseif ($statusGlobal == 'Karantina Sementara') {
                                            $badgeStyle = 'bg-indigo-50 text-indigo-600 border-indigo-200';
                                            $icon = 'fa-bed';
                                        } elseif (str_contains($statusGlobal, 'Lolos Antemortem')) {
                                            $badgeStyle = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                                            $icon = 'fa-check-circle';
                                        } elseif (str_contains($statusGlobal, 'Ditolak')) {
                                            $badgeStyle = 'bg-rose-50 text-rose-600 border-rose-200';
                                            $icon = 'fa-times-circle';
                                        } elseif ($statusGlobal == 'Menunggu Dipotong') {
                                            $badgeStyle = 'bg-cyan-50 text-cyan-600 border-cyan-200';
                                            $icon = 'fa-clock';
                                        } elseif ($statusGlobal == 'Selesai Dipotong') {
                                            $badgeStyle = 'bg-teal-50 text-teal-600 border-teal-200';
                                            $icon = 'fa-cut';
                                        } elseif (str_contains($statusGlobal, 'Bersyarat')) {
                                            $badgeStyle = 'bg-orange-50 text-orange-600 border-orange-200';
                                            $icon = 'fa-exclamation-triangle';
                                        } elseif (str_contains($statusGlobal, 'Selesai Postmortem')) {
                                            $badgeStyle = 'bg-purple-50 text-purple-600 border-purple-200';
                                            $icon = 'fa-file-medical';
                                        }
                                    @endphp
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 border rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $badgeStyle }}">
                                        <i class="fas {{ $icon }} text-[9px]"></i> {{ $statusGlobal }}
                                    </span>
                                </td>
                                @if(auth()->user()->role != 'pimpinan')
                                    <td
                                        class="px-4 py-3 text-center sticky right-0 bg-white group-hover:bg-slate-50 transition-colors z-10 shadow-[-4px_0_6px_-4px_rgba(0,0,0,0.1)]">
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick="editData(this)" data-hewan="{{ json_encode($hewan) }}"
                                                class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors"
                                                title="Edit Data">
                                                <i class="fas fa-pen text-xs"></i>
                                            </button>
                                            <button
                                                onclick="konfirmasiHapus('{{ route('hewan.destroy', $hewan->id_hewan) }}', '{{ $hewan->no_registrasi }}')"
                                                class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                                title="Hapus Data">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div id="modalTambah" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            onclick="toggleModal('modalTambah')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div
                class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-3xl w-full">
                <div class="bg-white px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800"><i class="fas fa-cow text-green-600 mr-2"></i> Pendaftaran
                        Hewan Baru</h3>
                    <button type="button" onclick="toggleModal('modalTambah')"
                        class="text-slate-400 hover:text-red-500 transition-colors"><i
                            class="fas fa-times text-xl"></i></button>
                </div>
                <form action="{{ route('hewan.store') }}" method="POST">
                    @csrf
                    <div class="px-6 py-5 bg-slate-50/50 max-h-[70vh] overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal Masuk <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_masuk" required value="{{ date('Y-m-d') }}"
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Pemilik / Pemasok
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_pemilik" required
                                    placeholder="Nama lengkap pemilik hewan"
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>


                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">No. Registrasi
                                    (Otomatis)</label>
                                <input type="text" id="tambah_no_registrasi" name="no_registrasi" readonly
                                    class="w-full px-3 py-2 text-sm font-bold bg-slate-100 text-blue-700 border border-slate-200 rounded-lg cursor-not-allowed shadow-inner">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kecamatan Asal <span
                                        class="text-red-500">*</span></label>
                                <select name="kecamatan" id="tambah_kecamatan" required onchange="loadDesa('tambah')"
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Desa/Kelurahan Asal <span
                                        class="text-red-500">*</span></label>
                                <select name="desa" id="tambah_desa" required
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                                    <option value="">-- Pilih Desa/Kelurahan --</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jenis Hewan <span
                                        class="text-red-500">*</span></label>
                                <select name="jenis_hewan" id="jenis_hewan_select" required
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Sapi">Sapi</option>
                                    <option value="Kerbau">Kerbau</option>
                                    <option value="Kambing">Kambing</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jenis Kelamin <span
                                        class="text-red-500">*</span></label>
                                <select name="jenis_kelamin" required
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                                    <option value="Jantan">Jantan</option>
                                    <option value="Betina">Betina</option>
                                </select>
                            </div>

                            <div>
                                <label id="label_tambah_berat"
                                    class="block text-xs font-semibold text-slate-600 mb-1.5">Berat Hidup (Kg)</label>
                                <input type="number" step="0.01" id="input_tambah_berat" name="berat"
                                    placeholder="0.00"
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Umur (Bulan/Tahun)</label>
                                <input type="text" name="umur" placeholder="Cth: 2 Tahun"
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" onclick="toggleModal('modalTambah')"
                            class="px-5 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Batal</button>
                        <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white bg-green-600 rounded-xl hover:bg-green-700 transition-colors shadow-sm">Simpan
                            Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL IMPORT -->
    <div id="modalImport" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            onclick="toggleModal('modalImport')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full flex flex-col border border-slate-100">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-6 py-4 flex justify-between items-center shrink-0 rounded-t-2xl">
                    <h3 class="text-lg font-bold text-white"><i class="fas fa-file-import mr-2 opacity-80"></i>
                        Import Data dari Excel</h3>
                    <button type="button" onclick="toggleModal('modalImport')" class="text-blue-100 hover:text-white transition-colors focus:outline-none"><i class="fas fa-times text-xl"></i></button>
                </div>
                <form action="{{ route('hewan.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col">
                    @csrf
                    <div class="px-6 py-5 bg-slate-50/50">
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Upload File Excel (.xls, .xlsx, .csv)</label>
                            <input type="file" name="file_import" required accept=".xls,.xlsx,.csv"
                                class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-slate-500 mt-2">Pastikan kolom sesuai format Laporan Harian RPH. (TANGGAL, NAMA PEDAGANG, Sapi ♂, Sapi ♀, Kerbau ♂, Kerbau ♀, PERKIRAAN BH, Asal, AM/PM)</p>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end gap-3 rounded-b-2xl">
                        <button type="button" onclick="toggleModal('modalImport')"
                            class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl hover:from-blue-600 hover:to-indigo-600 shadow-md shadow-blue-500/30 transform hover:-translate-y-0.5 transition-all"><i class="fas fa-upload mr-1.5"></i> Proses Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="modalEdit" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            onclick="toggleModal('modalEdit')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div
                class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-3xl w-full">
                <div class="bg-white px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800"><i class="fas fa-pen text-emerald-600 mr-2"></i> Edit
                        Data Hewan</h3>
                    <button type="button" onclick="toggleModal('modalEdit')"
                        class="text-slate-400 hover:text-red-500 transition-colors"><i
                            class="fas fa-times text-xl"></i></button>
                </div>
                <form id="formEdit" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="px-6 py-5 bg-slate-50/50 max-h-[70vh] overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal Masuk <span
                                        class="text-red-500">*</span></label>
                                <input type="date" id="edit_tanggal_masuk" name="tanggal_masuk" required
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Pemilik / Pemasok
                                    <span class="text-red-500">*</span></label>
                                <input type="text" id="edit_nama_pemilik" name="nama_pemilik" required
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">No. Registrasi /
                                    Antrean</label>
                                <input type="text" id="edit_no_registrasi" name="no_registrasi" readonly
                                    class="w-full px-3 py-2 text-sm font-bold bg-slate-100 text-slate-500 border border-slate-200 rounded-lg cursor-not-allowed">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kecamatan Asal <span
                                        class="text-red-500">*</span></label>
                                <select name="kecamatan" id="edit_kecamatan" required onchange="loadDesa('edit')"
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Desa/Kelurahan Asal <span
                                        class="text-red-500">*</span></label>
                                <select name="desa" id="edit_desa" required
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                    <option value="">-- Pilih Desa/Kelurahan --</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jenis Hewan <span
                                        class="text-red-500">*</span></label>
                                <input type="text" id="edit_jenis_hewan" name="jenis_hewan" required
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jenis Kelamin <span
                                        class="text-red-500">*</span></label>
                                <select id="edit_jenis_kelamin" name="jenis_kelamin" required
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                    <option value="Jantan">Jantan</option>
                                    <option value="Betina">Betina</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Berat Hidup (Kg)</label>
                                <input type="number" step="0.01" id="edit_berat" name="berat"
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Umur (Bulan/Tahun)</label>
                                <input type="text" id="edit_umur" name="umur"
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" onclick="toggleModal('modalEdit')"
                            class="px-5 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Batal</button>
                        <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 transition-colors shadow-sm">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="modalHapus" class="fixed inset-0 z-[120] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            onclick="toggleModal('modalHapus')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
            <div
                class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md w-full">
                <div class="bg-white px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-base font-bold text-slate-800"><i
                            class="fas fa-exclamation-triangle text-red-500 mr-2"></i> Konfirmasi Hapus Data</h3>
                    <button onclick="toggleModal('modalHapus')"
                        class="text-slate-400 hover:text-red-500 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <div class="px-6 py-5 bg-slate-50/50 text-center">
                    <div
                        class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-trash-alt text-2xl"></i>
                    </div>
                    <p class="text-sm text-slate-600 mb-1">Apakah Anda yakin ingin menghapus data hewan dengan No.
                        Registrasi:</p>
                    <p id="text_hapus_no_registrasi" class="text-lg font-bold text-slate-800 mb-3"></p>
                    <p class="text-xs text-red-500 font-medium bg-red-50 py-2 px-3 rounded-lg"><i
                            class="fas fa-info-circle mr-1"></i> Data yang dihapus tidak dapat dikembalikan lagi.</p>
                </div>
                <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end gap-3">
                    <form id="formHapus" action="" method="POST" class="m-0 flex gap-3">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="toggleModal('modalHapus')"
                            class="px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm">
                            Ya, Hapus Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="modalJenisHewan" class="fixed inset-0 z-[110] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            onclick="toggleModal('modalJenisHewan')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
            <div
                class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md w-full">
                <div class="bg-white px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-base font-bold text-slate-800"><i class="fas fa-tags text-blue-600 mr-2"></i> Kelola
                        Jenis Hewan</h3>
                    <button onclick="toggleModal('modalJenisHewan')"
                        class="text-slate-400 hover:text-red-500 transition-colors"><i
                            class="fas fa-times text-lg"></i></button>
                </div>
                <div class="px-6 py-5 bg-slate-50/50">
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Jenis Hewan Baru <span
                                class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <input type="text" id="input_jenis_baru" placeholder="Contoh: Kuda, Domba"
                                class="flex-1 px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500"
                                onkeypress="if(event.key === 'Enter') simpanJenisHewanBaru()">
                            <button type="button" onclick="simpanJenisHewanBaru()"
                                class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">Tambah</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2">Daftar Jenis Hewan Saat Ini:</label>
                        <div id="list_jenis_hewan" class="flex flex-wrap gap-2 max-h-40 overflow-y-auto p-1"></div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end gap-3">
                    <button type="button" onclick="toggleModal('modalJenisHewan')"
                        class="px-5 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors shadow-sm">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>

    <script>
        const nextPH = "{{ $nextPH ?? 'PH-001' }}";

        const dataWilayah = {
            "Kolaka": ["Kolaka", "Lamokato", "Lalombaa", "Tahoa", "Watuliandu", "Sea", "Sabilambo"],
            "Wundulako": ["Wundulako", "Kowioha", "Lamekongga", "Ngapa", "Silea", "Tikonu", "Towondu"],
            "Kolakaasi": ["Kolakaasi", "Induha", "Kewokas", "Ulusalu"],
            "Baula": ["Baula", "Puubunga", "PuuLawulo", "Watalara"],
            "Pomalaa": ["Pomalaa", "Dawi-dawi", "Hakatutobu", "Kumpono", "Oko-oko", "Pelambua", "Pesouha", "Tonggoni"],
            "Watubangga": ["Watubangga", "Mataosu", "Peoho", "Polinggona", "Watubangga"],
            "Wolo": ["Wolo", "Iwoimendaa", "Laponangi", "Muara Lapao-pao", "Tolowe Ponrewu", "Ulu Wolo"],
            "Samaturu": ["Samaturu", "Maloho", "Puu Tamboli", "Sani-sani", "Tamboli", "Ulu Konaweha"],
            "Iwoimendaa": ["Iwoimendaa", "Ladahai", "Lasiroku", "Tamborasi"],
            "Tanggetada": ["Tanggetada", "Lamedai", "Lipu Macco", "Palewai", "Puu Naha", "Tondowolio"],
            "Toari": ["Toari", "Horongkuli", "Rahabite", "Rano Jaya", "Wonua Raya"],
            "Polinggona": ["Polinggona", "Lamondape", "Puu Hupo", "Tanggeau", "Wuko"],
            "Luar Kolaka": ["Lainnya"]
        };

        function populateKecamatan() {
            let options = '<option value="">-- Pilih Kecamatan --</option>';
            for (let kec in dataWilayah) {
                options += `<option value="${kec}">${kec}</option>`;
            }
            $('#tambah_kecamatan, #edit_kecamatan').html(options);
        }

        function loadDesa(prefix, selectedDesa = null) {
            let kecamatan = $('#' + prefix + '_kecamatan').val();
            let desaSelect = $('#' + prefix + '_desa');
            
            desaSelect.html('<option value="">-- Pilih Desa/Kelurahan --</option>');
            
            if (kecamatan && dataWilayah[kecamatan]) {
                let desas = dataWilayah[kecamatan];
                let options = '<option value="">-- Pilih Desa/Kelurahan --</option>';
                desas.forEach(function(desa) {
                    options += `<option value="${desa}" ${selectedDesa == desa ? 'selected' : ''}>${desa}</option>`;
                });
                desaSelect.html(options);
            }
        }

        $(document).ready(function() {
            populateKecamatan();
            $('#tabelHewan').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json",
                    "emptyTable": "Belum ada data hewan yang terdaftar saat ini.",
                },
                "classes": {
                    "sWrapper": "dataTables_wrapper dt-tailwindcss w-full",
                    "sFilterInput": "px-3 py-1.5 ml-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-green-500 text-sm",
                    "sLengthSelect": "px-3 py-1.5 mx-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-green-500 text-sm"
                },
                "ordering": false,
                "initComplete": function(settings, json) {
                    $(this).wrap(
                        '<div class="overflow-x-auto relative w-full border-b border-slate-200 rounded-b-lg"></div>'
                    );
                }
            });
        });

        function toggleModal(modalID) {
            const modal = document.getElementById(modalID);
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                modal.classList.add('block');
            } else {
                modal.classList.add('hidden');
                modal.classList.remove('block');
            }
        }

        function exportExcel() {
            let dt = $('#tabelHewan').DataTable();
            let currentLength = dt.page.len();
            dt.page.len(-1).draw();

            let table = document.getElementById("tabelHewan");
            let cloneTable = table.cloneNode(true);

            let rows = cloneTable.querySelectorAll('tr');
            rows.forEach(row => {
                let cells = row.querySelectorAll('th, td');
                if (cells.length > 0) {
                    row.removeChild(cells[cells.length - 1]);
                }
            });

            let uri = 'data:application/vnd.ms-excel;base64,';
            let template =
                '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><meta charset="UTF-8"></head><body><table border="1">{table}</table></body></html>';

            let base64 = function(s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            };
            let format = function(s, c) {
                return s.replace(/{(\w+)}/g, function(m, p) {
                    return c[p];
                })
            };
            let ctx = {
                worksheet: 'Data Hewan',
                table: cloneTable.innerHTML
            }

            let link = document.createElement("a");
            link.download = "Export_Data_Hewan_RPH.xls";
            link.href = uri + base64(format(template, ctx));
            link.click();

            dt.page.len(currentLength).draw();
        }

        function editData(button) {
            let hewan = JSON.parse(button.getAttribute('data-hewan'));
            let url = "{{ url('/hewan/update') }}/" + hewan.id_hewan;
            document.getElementById('formEdit').action = url;

            document.getElementById('edit_tanggal_masuk').value = hewan.tanggal_masuk.substring(0, 10);
            document.getElementById('edit_nama_pemilik').value = hewan.nama_pemilik;
            document.getElementById('edit_no_registrasi').value = hewan.no_registrasi;

            let asal = hewan.asal_hewan.split(', ');
            let desaValue = asal[0] || '';
            let kecValue = asal[1] || '';

            $('#edit_kecamatan').val(kecValue).trigger('change');
            loadDesa('edit', desaValue);

            document.getElementById('edit_jenis_hewan').value = hewan.jenis_hewan;
            document.getElementById('edit_jenis_kelamin').value = hewan.jenis_kelamin;
            document.getElementById('edit_berat').value = hewan.berat;
            document.getElementById('edit_umur').value = hewan.umur;

            toggleModal('modalEdit');
        }

        // ================= LOGIKA HAPUS DATA =================
        function konfirmasiHapus(url, noRegistrasi) {
            document.getElementById('formHapus').action = url;
            document.getElementById('text_hapus_no_registrasi').innerText = noRegistrasi;
            toggleModal('modalHapus');
        }

        // ================= LOGIKA JENIS HEWAN =================
        function bukaModalKelolaJenis() {
            renderJenisHewanList();
            toggleModal('modalJenisHewan');
        }

        function renderJenisHewanList() {
            let selectElem = document.getElementById("jenis_hewan_select");
            let listContainer = document.getElementById("list_jenis_hewan");
            listContainer.innerHTML = "";

            for (let i = 1; i < selectElem.options.length; i++) {
                let val = selectElem.options[i].value;
                let badge = document.createElement("div");
                badge.className =
                    "flex items-center justify-between px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-700 shadow-sm";
                badge.innerHTML = `
                    <span>${val}</span>
                    <button type="button" onclick="hapusJenisHewan('${val}')" class="ml-3 text-slate-300 hover:text-red-500 transition-colors" title="Hapus Jenis">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                `;
                listContainer.appendChild(badge);
            }
        }

        function simpanJenisHewanBaru() {
            let inputElem = document.getElementById("input_jenis_baru");
            let jenisBaru = inputElem.value.trim();

            if (jenisBaru !== "") {
                let selectElem = document.getElementById("jenis_hewan_select");
                let isExist = false;
                for (let i = 0; i < selectElem.options.length; i++) {
                    if (selectElem.options[i].value.toLowerCase() === jenisBaru.toLowerCase()) {
                        isExist = true;
                        break;
                    }
                }

                if (!isExist) {
                    let optionBaru = document.createElement("option");
                    optionBaru.value = jenisBaru;
                    optionBaru.text = jenisBaru;
                    selectElem.appendChild(optionBaru);
                } else {
                    alert("Jenis hewan '" + jenisBaru + "' sudah ada di dalam daftar!");
                    return;
                }

                inputElem.value = "";
                renderJenisHewanList();
            } else {
                alert("Mohon ketikkan nama jenis hewan terlebih dahulu!");
                inputElem.focus();
            }
        }

        function hapusJenisHewan(valToRemove) {
            if (confirm("Apakah Anda yakin ingin menghapus '" + valToRemove + "' dari daftar pilihan?")) {
                let selectElem = document.getElementById("jenis_hewan_select");
                for (let i = 0; i < selectElem.options.length; i++) {
                    if (selectElem.options[i].value === valToRemove) {
                        selectElem.remove(i);
                        break;
                    }
                }
                renderJenisHewanList();
            }
        }
    </script>
@endsection
