@extends('layouts.app')

@section('title', 'Data Pemotongan Idul Adha')

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
                <h2 class="text-lg font-bold text-slate-800">Laporan Pemotongan Idul Adha Luar RPH</h2>
                <p class="text-xs text-slate-500 mt-1">Kelola data pemotongan hewan qurban di luar RPH (Masjid, Lapangan, dll)</p>
            </div>

            <div class="flex flex-wrap gap-2">
                @if(auth()->user()->role != 'pimpinan')
                    <button onclick="toggleModal('modalTambah')"
                        class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold rounded-xl hover:from-emerald-600 hover:to-teal-600 transition-all shadow-md shadow-emerald-500/30 flex items-center gap-2 transform hover:-translate-y-0.5">
                        <i class="fas fa-plus-circle"></i> Input Laporan
                    </button>
                    <button onclick="toggleModal('modalImport')"
                        class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-600 transition-all shadow-md shadow-blue-500/30 flex items-center gap-2 transform hover:-translate-y-0.5">
                        <i class="fas fa-file-import"></i> Import Excel
                    </button>
                @endif
                <button onclick="exportExcelIdulAdha()"
                    class="px-4 py-2 bg-slate-50 text-slate-700 border border-slate-200 text-sm font-semibold rounded-xl hover:bg-slate-100 transition-colors shadow-sm flex items-center gap-2">
                    <i class="fas fa-file-excel text-emerald-600"></i> Export Excel
                </button>
                <a href="{{ route('idul_adha.export_pdf') }}" target="_blank"
                    class="px-4 py-2 bg-red-50 text-red-600 border border-red-200 text-sm font-semibold rounded-xl hover:bg-red-100 transition-colors shadow-sm flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i> Cetak PDF
                </a>
            </div>
        </div>

        <div class="w-full">
            <table id="tabelIdulAdha" class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-4 border-b border-slate-100 text-center">No</th>
                        <th class="px-4 py-4 border-b border-slate-100">Tanggal</th>
                        <th class="px-4 py-4 border-b border-slate-100">Lokasi / Instansi</th>
                        <th class="px-4 py-4 border-b border-slate-100">Jenis Lokasi</th>
                        <th class="px-4 py-4 border-b border-slate-100">Alamat Lengkap</th>
                        <th class="px-4 py-4 border-b border-slate-100">Kec. / Desa</th>
                        <th class="px-4 py-4 border-b border-slate-100">Jenis Hewan</th>
                        <th class="px-4 py-4 border-b border-slate-100 text-center">Jumlah</th>
                        <th class="px-4 py-4 border-b border-slate-100">Pelapor (UPT)</th>
                        @if(auth()->user()->role != 'pimpinan')
                            <th class="px-4 py-4 border-b border-slate-100 text-center sticky right-0 bg-slate-100 z-20 shadow-[-4px_0_6px_-4px_rgba(0,0,0,0.1)]">
                                Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="text-slate-600 divide-y divide-slate-50">
                    @if (isset($idul_adha) && $idul_adha->count() > 0)
                        @foreach ($idul_adha as $item)
                            <tr class="group hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 text-center">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 font-bold text-emerald-600">{{ $item->nama_lokasi }}</td>
                                <td class="px-4 py-3">
                                    {{ $item->jenis_lokasi }} 
                                    @if($item->jenis_lokasi == 'Lainnya' && $item->jenis_lokasi_lainnya)
                                        <span class="text-xs text-slate-400">({{ $item->jenis_lokasi_lainnya }})</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs whitespace-normal min-w-[200px]">{{ $item->alamat }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold">{{ $item->kecamatan }}</div>
                                    <div class="text-xs text-slate-500">{{ $item->desa }}</div>
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-700">{{ $item->jenis_hewan }}</td>
                                <td class="px-4 py-3 text-center font-bold text-lg text-slate-800">{{ $item->jumlah }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold">{{ $item->pelapor }}</div>
                                    <div class="text-xs text-slate-500">{{ $item->upt }}</div>
                                </td>
                                @if(auth()->user()->role != 'pimpinan')
                                    <td class="px-4 py-3 text-center sticky right-0 bg-white group-hover:bg-slate-50 transition-colors z-10 shadow-[-4px_0_6px_-4px_rgba(0,0,0,0.1)]">
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick="editData(this)" data-laporan="{{ json_encode($item) }}"
                                                class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors"
                                                title="Edit Data">
                                                <i class="fas fa-pen text-xs"></i>
                                            </button>
                                            <button
                                                onclick="konfirmasiHapus('{{ route('idul_adha.destroy', $item->id_pemotongan_ia) }}', '{{ $item->nama_lokasi }}')"
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

    <!-- MODAL TAMBAH -->
    <div id="modalTambah" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            onclick="toggleModal('modalTambah')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:max-w-3xl w-full flex flex-col max-h-[90vh] overflow-hidden border border-slate-100">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4 flex justify-between items-center shrink-0">
                    <h3 class="text-lg font-bold text-white"><i class="fas fa-mosque mr-2 opacity-80"></i>
                        Input Data Pemotongan Idul Adha</h3>
                    <button type="button" onclick="toggleModal('modalTambah')" class="text-emerald-100 hover:text-white transition-colors focus:outline-none"><i
                            class="fas fa-times text-xl"></i></button>
                </div>
                <form action="{{ route('idul_adha.store') }}" method="POST" class="flex-1 overflow-y-auto flex flex-col">
                    @csrf
                    <div class="px-6 py-5 bg-slate-50/50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal Pemotongan <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}"
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Lokasi / Instansi <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_lokasi" required placeholder="Contoh: Masjid Al-Ikhlas / Pemda"
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jenis Lokasi <span class="text-red-500">*</span></label>
                                <select name="jenis_lokasi" id="tambah_jenis_lokasi" required onchange="toggleLainnya('tambah')"
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Masjid">Masjid</option>
                                    <option value="Lapangan">Lapangan</option>
                                    <option value="Instansi Pemerintah">Instansi Pemerintah</option>
                                    <option value="Sekolah / Yayasan">Sekolah / Yayasan</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div id="tambah_jenis_lainnya_container" class="hidden">
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Sebutkan Jenis Lokasi Lainnya <span class="text-red-500">*</span></label>
                                <input type="text" name="jenis_lokasi_lainnya" id="tambah_jenis_lainnya_input" placeholder="Isi di sini..."
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
                                <textarea name="alamat" required rows="2" placeholder="Jl. Raya No. 123..."
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500"></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kecamatan <span class="text-red-500">*</span></label>
                                <select name="kecamatan" id="tambah_kecamatan" required onchange="loadDesa('tambah')"
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kelurahan / Desa <span class="text-red-500">*</span></label>
                                <select name="desa" id="tambah_desa" required
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                    <option value="">-- Pilih Desa/Kelurahan --</option>
                                </select>
                            </div>

                            <!-- Hidden inputs for Propinsi and Kabupaten based on usual defaults, can be made visible if needed -->
                            <input type="hidden" name="propinsi" value="Sulawesi Tenggara">
                            <input type="hidden" name="kabupaten" value="Kolaka">

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jenis Hewan <span class="text-red-500">*</span></label>
                                <select name="jenis_hewan" required
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Sapi">Sapi</option>
                                    <option value="Kambing">Kambing</option>
                                    <option value="Domba">Domba</option>
                                    <option value="Kerbau">Kerbau</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jumlah Ekor <span class="text-red-500">*</span></label>
                                <input type="number" name="jumlah" required min="1" placeholder="Misal: 5"
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Asal Laporan (UPT / Wilayah) <span class="text-red-500">*</span></label>
                                <input type="text" name="upt" required placeholder="Nama UPT..."
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Pelapor <span class="text-red-500">*</span></label>
                                <input type="text" name="pelapor" required placeholder="Nama Lengkap Pelapor"
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end gap-3 shrink-0 mt-auto">
                        <button type="button" onclick="toggleModal('modalTambah')"
                            class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-200">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl hover:from-emerald-600 hover:to-teal-600 shadow-md shadow-emerald-500/30 transform hover:-translate-y-0.5 transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1"><i class="fas fa-save mr-1.5"></i> Simpan
                            Laporan</button>
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
                <form action="{{ route('idul_adha.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col">
                    @csrf
                    <div class="px-6 py-5 bg-slate-50/50">
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Upload File Excel (.xls, .xlsx, .csv)</label>
                            <input type="file" name="file_import" required accept=".xls,.xlsx,.csv"
                                class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-slate-500 mt-2">Pastikan format kolom sesuai dengan data yang ada di tabel, urutan kolom: Tanggal, Nama Lokasi, Jenis Lokasi, Jenis Lokasi Lainnya, Alamat, Propinsi, Kabupaten, Kecamatan, Desa, Jenis Hewan, Jumlah, UPT, Pelapor.</p>
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

    <!-- MODAL EDIT -->
    <div id="modalEdit" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            onclick="toggleModal('modalEdit')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:max-w-3xl w-full flex flex-col max-h-[90vh] overflow-hidden border border-slate-100">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4 flex justify-between items-center shrink-0">
                    <h3 class="text-lg font-bold text-white"><i class="fas fa-pen mr-2 opacity-80"></i>
                        Edit Data Pemotongan Idul Adha</h3>
                    <button type="button" onclick="toggleModal('modalEdit')" class="text-emerald-100 hover:text-white transition-colors focus:outline-none"><i
                            class="fas fa-times text-xl"></i></button>
                </div>
                <form id="formEdit" action="" method="POST" class="flex-1 overflow-y-auto flex flex-col">
                    @csrf
                    @method('PUT')
                    <div class="px-6 py-5 bg-slate-50/50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal Pemotongan <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal" id="edit_tanggal" required 
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Lokasi / Instansi <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_lokasi" id="edit_nama_lokasi" required 
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jenis Lokasi <span class="text-red-500">*</span></label>
                                <select name="jenis_lokasi" id="edit_jenis_lokasi" required onchange="toggleLainnya('edit')"
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Masjid">Masjid</option>
                                    <option value="Lapangan">Lapangan</option>
                                    <option value="Instansi Pemerintah">Instansi Pemerintah</option>
                                    <option value="Sekolah / Yayasan">Sekolah / Yayasan</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div id="edit_jenis_lainnya_container" class="hidden">
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Sebutkan Jenis Lokasi Lainnya <span class="text-red-500">*</span></label>
                                <input type="text" name="jenis_lokasi_lainnya" id="edit_jenis_lainnya_input" 
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
                                <textarea name="alamat" id="edit_alamat" required rows="2" 
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500"></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kecamatan <span class="text-red-500">*</span></label>
                                <select name="kecamatan" id="edit_kecamatan" required onchange="loadDesa('edit')"
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kelurahan / Desa <span class="text-red-500">*</span></label>
                                <select name="desa" id="edit_desa" required
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                    <option value="">-- Pilih Desa/Kelurahan --</option>
                                </select>
                            </div>

                            <input type="hidden" name="propinsi" id="edit_propinsi" value="Sulawesi Tenggara">
                            <input type="hidden" name="kabupaten" id="edit_kabupaten" value="Kolaka">

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jenis Hewan <span class="text-red-500">*</span></label>
                                <select name="jenis_hewan" id="edit_jenis_hewan" required
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                    <option value="Sapi">Sapi</option>
                                    <option value="Kambing">Kambing</option>
                                    <option value="Domba">Domba</option>
                                    <option value="Kerbau">Kerbau</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jumlah Ekor <span class="text-red-500">*</span></label>
                                <input type="number" name="jumlah" id="edit_jumlah" required min="1" 
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Asal Laporan (UPT / Wilayah) <span class="text-red-500">*</span></label>
                                <input type="text" name="upt" id="edit_upt" required 
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Pelapor <span class="text-red-500">*</span></label>
                                <input type="text" name="pelapor" id="edit_pelapor" required 
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end gap-3 shrink-0 mt-auto">
                        <button type="button" onclick="toggleModal('modalEdit')"
                            class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-200">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl hover:from-emerald-600 hover:to-teal-600 shadow-md shadow-emerald-500/30 transform hover:-translate-y-0.5 transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1"><i class="fas fa-check-circle mr-1.5"></i> Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL HAPUS -->
    <div id="modalHapus" class="fixed inset-0 z-[120] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            onclick="toggleModal('modalHapus')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
            <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md w-full">
                <div class="bg-white px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-base font-bold text-slate-800"><i class="fas fa-exclamation-triangle text-red-500 mr-2"></i> Konfirmasi Hapus Data</h3>
                    <button type="button" onclick="toggleModal('modalHapus')" class="text-slate-400 hover:text-red-500 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <div class="px-6 py-5 bg-slate-50/50 text-center">
                    <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-trash-alt text-2xl"></i>
                    </div>
                    <p class="text-sm text-slate-600 mb-1">Apakah Anda yakin ingin menghapus data laporan di lokasi:</p>
                    <p id="text_hapus_nama_lokasi" class="text-lg font-bold text-slate-800 mb-3"></p>
                    <p class="text-xs text-red-500 font-medium bg-red-50 py-2 px-3 rounded-lg"><i class="fas fa-info-circle mr-1"></i> Data yang dihapus tidak dapat dikembalikan lagi.</p>
                </div>
                <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end gap-3">
                    <form id="formHapus" action="" method="POST" class="m-0 flex gap-3">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="toggleModal('modalHapus')" class="px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm">Ya, Hapus Data</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>

    <script>
        // Data Kecamatan dan Desa di Kabupaten Kolaka (Contoh Data Umum)
        const dataWilayahKolaka = {
            "Kolaka": ["Lamokato", "Lalombaa", "Watuliandu", "Sabilambo", "Laloeha", "Tahoa", "Balandete"],
            "Latambaga": ["Latambaga", "Sea", "Mangolo", "Induha", "Kolakaasi", "Ulunggolaka", "Sakuli"],
            "Wundulako": ["Wundulako", "Lamekongga", "Ngapa", "Kowioha", "Towire", "Silea", "Tikonu"],
            "Pomalaa": ["Pomalaa", "Dawi-dawi", "Tonggoni", "Kumoro", "Huko-huko", "Totobo", "Oko-oko", "Pelambua"],
            "Baula": ["Baula", "Puubunga", "Puulemo", "PuuLawulo", "Watalara"],
            "Wolo": ["Wolo", "Ulu Wolo", "Iwoimendaa", "Lasiroku", "Tolowe Ponrewu"],
            "Samaturu": ["Tosiba", "Konaweha", "Sani-sani", "Tamboli", "Ulu Konaweha"],
            "Tanggetada": ["Tanggetada", "Tinggo", "Anaiwoi", "Palewai", "PuuBunga"],
            "Watubangga": ["Watubangga", "Mataosu", "Kastura", "Polinggona", "Lamundre"],
            "Toari": ["Toari", "Wonua Raya", "Rano Jaya"],
            "Polinggona": ["Polinggona", "Lamondre", "Watu-watu"],
            "Iwoimendaa": ["Iwoimendaa", "Tamborasi", "Lasiroku"]
        };

        function initDropdownKecamatan() {
            const selectTambahKec = document.getElementById('tambah_kecamatan');
            const selectEditKec = document.getElementById('edit_kecamatan');
            
            let options = '<option value="">-- Pilih Kecamatan --</option>';
            for (let kec in dataWilayahKolaka) {
                options += `<option value="${kec}">${kec}</option>`;
            }
            
            selectTambahKec.innerHTML = options;
            selectEditKec.innerHTML = options;
        }

        function loadDesa(tipe, selectedDesa = null) {
            let kecId = tipe === 'tambah' ? 'tambah_kecamatan' : 'edit_kecamatan';
            let desaId = tipe === 'tambah' ? 'tambah_desa' : 'edit_desa';
            
            let kecamatan = document.getElementById(kecId).value;
            let selectDesa = document.getElementById(desaId);
            
            let options = '<option value="">-- Pilih Desa/Kelurahan --</option>';
            
            if (kecamatan && dataWilayahKolaka[kecamatan]) {
                dataWilayahKolaka[kecamatan].forEach(desa => {
                    let selected = (selectedDesa === desa) ? 'selected' : '';
                    options += `<option value="${desa}" ${selected}>${desa}</option>`;
                });
            } else if (kecamatan) {
                // Jika kecamatan tidak ada di list tapi diketik manual (jika bisa), atau tambah opsi lainnya
                options += `<option value="Lainnya">Lainnya</option>`;
            }
            
            selectDesa.innerHTML = options;
        }

        $(document).ready(function() {
            initDropdownKecamatan();
            
            $('#tabelIdulAdha').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json",
                    "emptyTable": "Belum ada laporan pemotongan Idul Adha.",
                },
                "classes": {
                    "sWrapper": "dataTables_wrapper dt-tailwindcss w-full",
                    "sFilterInput": "px-3 py-1.5 ml-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500 text-sm",
                    "sLengthSelect": "px-3 py-1.5 mx-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500 text-sm"
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

        function toggleLainnya(prefix) {
            const select = document.getElementById(prefix + '_jenis_lokasi');
            const container = document.getElementById(prefix + '_jenis_lainnya_container');
            const input = document.getElementById(prefix + '_jenis_lainnya_input');

            if (select.value === 'Lainnya') {
                container.classList.remove('hidden');
                input.setAttribute('required', 'required');
            } else {
                container.classList.add('hidden');
                input.removeAttribute('required');
                input.value = '';
            }
        }

        // ================= LOGIKA EDIT DATA =================
        function editData(button) {
            let data = JSON.parse(button.getAttribute('data-laporan'));
            let url = "{{ url('/idul_adha/update') }}/" + data.id_pemotongan_ia;
            document.getElementById('formEdit').action = url;

            document.getElementById('edit_tanggal').value = data.tanggal;
            document.getElementById('edit_nama_lokasi').value = data.nama_lokasi;
            document.getElementById('edit_jenis_lokasi').value = data.jenis_lokasi;

            let editJenisLokasi = document.getElementById('edit_jenis_lokasi');
            let editJenisLainnyaCont = document.getElementById('edit_jenis_lainnya_container');
            let editJenisLainnyaInput = document.getElementById('edit_jenis_lainnya_input');

            // Cek apakah jenis lokasi ada di opsi dropdown
            let isOpsiStandard = Array.from(editJenisLokasi.options).some(opt => opt.value === data.jenis_lokasi && opt.value !== "Lainnya");
            
            if(isOpsiStandard) {
                editJenisLokasi.value = data.jenis_lokasi;
                editJenisLainnyaCont.classList.add('hidden');
                editJenisLainnyaInput.removeAttribute('required');
                editJenisLainnyaInput.value = '';
            } else {
                editJenisLokasi.value = 'Lainnya';
                editJenisLainnyaCont.classList.remove('hidden');
                editJenisLainnyaInput.setAttribute('required', 'required');
                editJenisLainnyaInput.value = data.jenis_lokasi_lainnya || data.jenis_lokasi;
            }

            document.getElementById('edit_alamat').value = data.alamat;
            
            let selectEditKec = document.getElementById('edit_kecamatan');
            // Cek jika kecamatan dari data ada di option, jika tidak, tambahkan opsi sementara
            if(!Array.from(selectEditKec.options).some(opt => opt.value === data.kecamatan)) {
                let opt = document.createElement('option');
                opt.value = data.kecamatan;
                opt.text = data.kecamatan;
                selectEditKec.add(opt);
            }
            selectEditKec.value = data.kecamatan;
            loadDesa('edit', data.desa); // Load desa based on kecamatan and set selected

            document.getElementById('edit_jenis_hewan').value = data.jenis_hewan;
            document.getElementById('edit_jumlah').value = data.jumlah;
            document.getElementById('edit_upt').value = data.upt;
            document.getElementById('edit_pelapor').value = data.pelapor;

            toggleModal('modalEdit');
        }

        // ================= LOGIKA HAPUS DATA =================
        function konfirmasiHapus(url, namaLokasi) {
            document.getElementById('formHapus').action = url;
            document.getElementById('text_hapus_nama_lokasi').innerText = namaLokasi;
            toggleModal('modalHapus');
        }

        // ================= LOGIKA EXPORT EXCEL IDUL ADHA =================
        function exportExcelIdulAdha() {
            let dt = $('#tabelIdulAdha').DataTable();

            let currentLength = dt.page.len();
            dt.page.len(-1).draw();

            let table = document.getElementById("tabelIdulAdha");
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

            let base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) };
            let format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) };
            let ctx = { worksheet: 'Pemotongan Idul Adha', table: cloneTable.innerHTML }

            let link = document.createElement("a");
            link.download = "Laporan_Pemotongan_Idul_Adha.xls";
            link.href = uri + base64(format(template, ctx));
            link.click();

            dt.page.len(currentLength).draw();
        }
    </script>
@endsection
