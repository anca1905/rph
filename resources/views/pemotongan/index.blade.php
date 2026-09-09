@extends('layouts.app')

@section('title', 'Data Hasil Potong (Karkas)')

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
                class="text-emerald-400 hover:text-emerald-600 focus:outline-none"><i
                    class="fas fa-times text-lg"></i></button>
        </div>
    @endif
    @if ($errors->any())
        <div id="alert-error"
            class="mb-4 p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl shadow-sm flex justify-between items-start transition-all duration-300">
            <div>
                <div class="flex items-center gap-2 mb-2 font-bold"><i class="fas fa-exclamation-triangle"></i> Peringatan
                    Sistem:</div>
                <ul class="list-disc list-inside text-sm font-medium ml-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" onclick="document.getElementById('alert-error').remove()"
                class="text-red-400 hover:text-red-600 focus:outline-none"><i class="fas fa-times text-lg"></i></button>
        </div>
    @endif

    <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex flex-col justify-center">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Filter Periode
                Aktivitas</label>
            <form action="{{ route('pemotongan.index') }}" method="GET" class="m-0 relative">
                <select name="periode" onchange="this.form.submit()"
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 text-slate-700 font-bold text-xs rounded-xl focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200 cursor-pointer transition-all appearance-none">
                    <option value="harian" {{ isset($periode) && $periode == 'harian' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="bulanan" {{ isset($periode) && $periode == 'bulanan' ? 'selected' : '' }}>Bulan Ini
                    </option>
                    <option value="tahunan" {{ isset($periode) && $periode == 'tahunan' ? 'selected' : '' }}>Tahun Ini
                    </option>
                    <option value="semua" {{ isset($periode) && $periode == 'semua' ? 'selected' : '' }}>Semua Waktu
                    </option>
                </select>
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fas fa-calendar-alt text-xs"></i>
                </div>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fas fa-chevron-down text-[10px]"></i>
                </div>
            </form>
            <p class="text-[9px] text-slate-400 mt-2 font-medium"><i class="fas fa-info-circle mr-1"></i> Data: <span
                    class="font-bold text-green-600">{{ $teksPeriode ?? 'Hari Ini' }}</span></p>
        </div>

        <div
            class="bg-gradient-to-br from-green-500 to-emerald-700 rounded-2xl p-4 shadow-sm text-white flex items-center justify-between overflow-hidden relative">
            <div class="z-10">
                <p class="text-[10px] font-semibold text-green-100 mb-1 opacity-90 uppercase tracking-wider">Aktivitas
                    Potong</p>
                <h3 class="text-2xl font-black">{{ $jumlahDipotong ?? 0 }} <span
                        class="text-xs font-medium text-green-200 ml-1">Ekor</span></h3>
            </div>
            <div
                class="absolute -right-4 -bottom-4 w-20 h-20 bg-white/10 rounded-full flex items-center justify-center backdrop-blur-sm z-0">
                <i class="fas fa-drumstick-bite text-3xl text-white/50"></i>
            </div>
        </div>

        <div
            class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-4 shadow-sm text-white flex items-center justify-between overflow-hidden relative">
            <div class="z-10">
                <p class="text-[10px] font-semibold text-blue-100 mb-1 opacity-90 uppercase tracking-wider">Lolos Antemortem
                </p>
                <h3 class="text-2xl font-black">{{ $jumlahLolos ?? 0 }} <span
                        class="text-xs font-medium text-blue-200 ml-1">Ekor</span></h3>
            </div>
            <div
                class="absolute -right-4 -bottom-4 w-20 h-20 bg-white/10 rounded-full flex items-center justify-center backdrop-blur-sm z-0">
                <i class="fas fa-check-circle text-3xl text-white/50"></i>
            </div>
        </div>

        <div
            class="bg-gradient-to-br from-yellow-400 to-amber-500 rounded-2xl p-4 shadow-sm text-amber-950 flex items-center justify-between overflow-hidden relative">
            <div class="z-10">
                <p class="text-[10px] font-semibold text-amber-900 mb-1 opacity-90 uppercase tracking-wider">Karantina
                    (Istirahat)</p>
                <h3 class="text-2xl font-black">{{ $jumlahKarantina ?? 0 }} <span
                        class="text-xs font-medium text-amber-900 ml-1">Ekor</span></h3>
            </div>
            <div
                class="absolute -right-4 -bottom-4 w-20 h-20 bg-black/5 rounded-full flex items-center justify-center backdrop-blur-sm z-0">
                <i class="fas fa-bed text-3xl text-amber-900/40"></i>
            </div>
        </div>

    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 border-b border-slate-100 pb-4 gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Catatan Berat Karkas & Jeroan</h2>
                <p class="text-xs text-slate-500 mt-1">Data produksi hewan yang telah Lolos Antemortem & Pembayaran Lunas
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button onclick="exportExcelPemotongan()"
                    class="px-4 py-2 bg-emerald-50 text-emerald-700 border border-emerald-200 text-sm font-semibold rounded-xl hover:bg-emerald-100 transition-colors shadow-sm flex items-center gap-2">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            </div>
        </div>

        <div class="w-full">
            <table id="tabelPemotongan" class="w-full text-left text-sm whitespace-nowrap">
                <thead
                    class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-4 border-b border-slate-100 text-center">No</th>
                        <th class="px-4 py-4 border-b border-slate-100">No Registrasi</th>
                        <th class="px-4 py-4 border-b border-slate-100">Nama Pemilik</th>
                        <th class="px-4 py-4 border-b border-slate-100">Jenis Hewan</th>
                        <th class="px-4 py-4 border-b border-slate-100">Tanggal Potong</th>
                        <th class="px-4 py-4 border-b border-slate-100">Jam Potong</th>
                        <th class="px-4 py-4 border-b border-slate-100 text-right">Berat Karkas</th>
                        <th class="px-4 py-4 border-b border-slate-100 text-right">Berat Jeroan</th>
                        <th class="px-4 py-4 border-b border-slate-100 text-center">Status Pemotongan</th>
                        <th
                            class="px-4 py-4 border-b border-slate-100 text-center sticky right-0 bg-slate-100 z-20 shadow-[-4px_0_6px_-4px_rgba(0,0,0,0.1)]">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-slate-600 divide-y divide-slate-50">
                    @if (isset($hewans) && $hewans->count() > 0)
                        @foreach ($hewans as $hewan)
                            @php
                                $potong = $hewan->pemotongan;
                            @endphp
                            <tr class="group hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 text-center">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-bold text-slate-800">{{ $hewan->no_registrasi ?? '-' }}
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-700">{{ $hewan->nama_pemilik ?? '-' }}
                                </td>
                                <td class="px-4 py-3">{{ $hewan->jenis_hewan ?? '-' }}</td>

                                <td class="px-4 py-3">{{ $potong ? \Carbon\Carbon::parse($potong->waktu_potong)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-3">{{ $potong ? \Carbon\Carbon::parse($potong->waktu_potong)->format('H:i') : '-' }}
                                </td>

                                <td class="px-4 py-3 text-right font-bold text-emerald-600">{{ $potong ? $potong->berat_karkas . ' Kg' : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold">
                                    {{ ($potong && $potong->berat_jeroan) ? $potong->berat_jeroan . ' Kg' : '-' }}</td>

                                <td class="px-4 py-3 text-center">
                                    @php
                                        $statusGlobal = $hewan->status ?? 'Menunggu Dipotong';
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

                                <td
                                    class="px-4 py-3 text-center sticky right-0 bg-white group-hover:bg-slate-50 transition-colors z-10 shadow-[-4px_0_6px_-4px_rgba(0,0,0,0.1)]">
                                    <div class="flex items-center justify-center gap-2">
                                        @if ($potong)
                                            @if(auth()->user()->role != 'pimpinan')
                                                <button onclick="editData(this)" data-id="{{ $potong->id_pemotongan }}"
                                                    data-tanggal="{{ \Carbon\Carbon::parse($potong->waktu_potong)->format('Y-m-d') }}"
                                                    data-jam="{{ \Carbon\Carbon::parse($potong->waktu_potong)->format('H:i') }}"
                                                    data-karkas="{{ $potong->berat_karkas }}"
                                                    data-jeroan="{{ $potong->berat_jeroan }}"
                                                    data-status="{{ $potong->status_pemotongan }}"
                                                    data-reg="{{ $hewan->no_registrasi ?? '-' }}"
                                                    data-nama="{{ $hewan->nama_pemilik ?? '-' }}"
                                                    data-umur="{{ $hewan->umur ?? '-' }}"
                                                    data-berat="{{ $hewan->berat ?? '-' }}"
                                                class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors"
                                                title="Edit Data">
                                                <i class="fas fa-pen text-xs"></i>
                                            </button>
                                                <button
                                                    onclick="konfirmasiHapus('{{ route('pemotongan.destroy', $potong->id_pemotongan) }}')"
                                                    class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                                    title="Hapus Data">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            @endif
                                        @else
                                            @php
                                                $isLunas = $hewan->pembayaran && $hewan->pembayaran->status_pembayaran == 'Lunas';
                                                $isLolosAM = $hewan->antemortem && $hewan->antemortem->status_antemortem == 'Lolos';
                                            @endphp

                                            @if($isLunas && $isLolosAM)
                                                @if(auth()->user()->role != 'pimpinan')
                                                    <button onclick="tambahData({{ $hewan->id_hewan }}, '{{ $hewan->no_registrasi }}', '{{ $hewan->nama_pemilik }}', '{{ $hewan->jenis_hewan }}', '{{ $hewan->umur }}', '{{ $hewan->berat }}')"
                                                        class="px-3 py-1.5 rounded-lg bg-teal-600 text-white font-semibold text-xs hover:bg-teal-700 transition-colors whitespace-nowrap shadow-sm">
                                                        <i class="fas fa-balance-scale mr-1"></i> Proses Potong
                                                    </button>
                                                @endif
                                            @else
                                                @php
                                                    $pesan = [];
                                                    if (!$isLunas) $pesan[] = 'Belum Lunas';
                                                    if (!$isLolosAM) $pesan[] = 'Belum Lolos AM';
                                                    $textPesan = implode(' & ', $pesan);
                                                @endphp
                                                <button disabled
                                                    class="px-2 py-1.5 rounded-lg bg-red-50 text-red-500 border border-red-100 font-bold text-[10px] cursor-not-allowed whitespace-nowrap shadow-sm"
                                                    title="Belum memenuhi syarat">
                                                    <i class="fas fa-lock mr-1"></i> {{ $textPesan }}
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
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
                class="relative bg-white rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl w-full flex flex-col max-h-[90vh] overflow-hidden border border-slate-100">
                <div class="bg-gradient-to-r from-emerald-600 to-teal-500 px-6 py-4 flex justify-between items-center shrink-0">
                    <h3 class="text-lg font-bold text-white"><i class="fas fa-balance-scale mr-2 opacity-80"></i>
                        Input Hasil Potong</h3>
                    <button onclick="toggleModal('modalTambah')" class="text-emerald-100 hover:text-white transition-colors focus:outline-none"><i
                            class="fas fa-times text-xl"></i></button>
                </div>
                <form action="{{ route('pemotongan.store') }}" method="POST" class="flex-1 overflow-y-auto">
                    @csrf
                    <div class="px-6 py-5 bg-slate-50/50">
                        <div class="space-y-4">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Data Registrasi
                                        Hewan (Terkunci)</label>
                                    <input type="hidden" name="id_hewan" id="tambah_id_hewan" required>
                                    <input type="text" id="tambah_hewan_label" readonly
                                        class="w-full px-3 py-2 text-sm bg-slate-100 text-slate-500 border border-slate-200 rounded-lg pointer-events-none cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Status
                                        Pembayaran</label>
                                    <div id="status_lunas_container">
                                        <div id="status_lunas_display"
                                            class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-400 italic flex items-center gap-2 cursor-default shadow-inner">
                                            Memeriksa pembayaran...
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal Potong <span
                                            class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_potong" required value="{{ date('Y-m-d') }}"
                                        class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jam Potong
                                        (Opsional)</label>
                                    <input type="time" name="jam_potong" value="{{ date('H:i') }}"
                                        class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Status Pemotongan <span
                                        class="text-red-500">*</span></label>
                                <select name="status_pemotongan" required
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                                    <option value="Menunggu Dipotong">Menunggu Dipotong</option>
                                    <option value="Selesai Dipotong">Selesai Dipotong</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Berat Karkas (Kg)
                                        <span class="text-red-500">*</span></label>
                                    <input type="number" step="0.01" name="berat_karkas" required placeholder="0.00"
                                        class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Berat Jeroan
                                        (Kg)</label>
                                    <input type="number" step="0.01" name="berat_jeroan" placeholder="0.00"
                                        class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end gap-3 shrink-0">
                        <button type="button" onclick="toggleModal('modalTambah')"
                            class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-slate-800 transition-all focus:outline-none focus:ring-2 focus:ring-slate-200">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl hover:from-emerald-600 hover:to-teal-600 shadow-md shadow-emerald-500/30 transform hover:-translate-y-0.5 transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1"><i class="fas fa-save mr-1.5"></i> Simpan
                            Data</button>
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
                class="relative bg-white text-left shadow-2xl transform transition-all sm:my-8 sm:max-w-xl w-full flex flex-col max-h-[90vh] rounded-2xl overflow-hidden border border-slate-100">
                <div class="bg-gradient-to-r from-emerald-600 to-teal-500 px-6 py-4 flex justify-between items-center shrink-0">
                    <h3 class="text-lg font-bold text-white"><i class="fas fa-pen mr-2 opacity-80"></i> Edit
                        Hasil Potong</h3>
                    <button onclick="toggleModal('modalEdit')" class="text-emerald-100 hover:text-white transition-colors focus:outline-none"><i
                            class="fas fa-times text-xl"></i></button>
                </div>
                <form id="formEdit" action="" method="POST" class="flex-1 overflow-y-auto">
                    @csrf
                    @method('PUT')

                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 shrink-0">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Informasi Hewan</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                            <div class="bg-white p-2 rounded-lg border border-slate-200">
                                <span class="block text-slate-400 text-[9px] uppercase mb-0.5">No Reg</span>
                                <span id="edit_info_reg" class="font-bold text-slate-800"></span>
                            </div>
                            <div class="bg-white p-2 rounded-lg border border-slate-200">
                                <span class="block text-slate-400 text-[9px] uppercase mb-0.5">Pemilik</span>
                                <span id="edit_info_nama" class="font-bold text-slate-800"></span>
                            </div>
                            <div class="bg-white p-2 rounded-lg border border-slate-200">
                                <span class="block text-slate-400 text-[9px] uppercase mb-0.5">Umur</span>
                                <span id="edit_info_umur" class="font-bold text-slate-800"></span>
                            </div>
                            <div class="bg-white p-2 rounded-lg border border-slate-200">
                                <span class="block text-slate-400 text-[9px] uppercase mb-0.5">Berat Hewan</span>
                                <span id="edit_info_berat" class="font-bold text-slate-800"></span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-5 bg-white">
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal Potong <span
                                            class="text-red-500">*</span></label>
                                    <input type="date" id="edit_tanggal" name="tanggal_potong" required
                                        class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jam Potong</label>
                                    <input type="time" id="edit_jam" name="jam_potong"
                                        class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Status Pemotongan <span
                                        class="text-red-500">*</span></label>
                                <select id="edit_status" name="status_pemotongan" required
                                    class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                    <option value="Menunggu Dipotong">Menunggu Dipotong</option>
                                    <option value="Selesai Dipotong">Selesai Dipotong</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Berat Karkas (Kg)
                                        <span class="text-red-500">*</span></label>
                                    <input type="number" step="0.01" id="edit_karkas" name="berat_karkas" required
                                        class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Berat Jeroan
                                        (Kg)</label>
                                    <input type="number" step="0.01" id="edit_jeroan" name="berat_jeroan"
                                        class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 shrink-0">
                        <button type="button" onclick="toggleModal('modalEdit')"
                            class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-slate-800 transition-all focus:outline-none focus:ring-2 focus:ring-slate-200">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl hover:from-emerald-600 hover:to-teal-600 shadow-md shadow-emerald-500/30 transform hover:-translate-y-0.5 transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1"><i class="fas fa-check-circle mr-1.5"></i> Simpan
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
                            class="fas fa-exclamation-triangle text-red-500 mr-2"></i> Konfirmasi Hapus</h3>
                    <button onclick="toggleModal('modalHapus')" class="text-slate-400 hover:text-red-500"><i
                            class="fas fa-times text-lg"></i></button>
                </div>
                <div class="px-6 py-5 bg-slate-50/50 text-center">
                    <div
                        class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-trash-alt text-2xl"></i></div>
                    <p class="text-sm text-slate-600">Apakah Anda yakin ingin menghapus data pemotongan ini?</p>
                </div>
                <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end gap-3">
                    <form id="formHapus" action="" method="POST" class="m-0 flex gap-3">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="toggleModal('modalHapus')"
                            class="px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700">Ya,
                            Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#tabelPemotongan').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json",
                    "emptyTable": "Belum ada data pemotongan di periode ini.",
                    "search": "Cari Data:",
                },
                "classes": {
                    "sWrapper": "dataTables_wrapper dt-tailwindcss w-full",
                    "sFilterInput": "px-3 py-1.5 ml-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-green-500 text-sm",
                    "sLengthSelect": "px-3 py-1.5 mx-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-green-500 text-sm"
                },
                "ordering": false,
                "initComplete": function(settings, json) {
                    $(this).wrap(
                        '<div class="overflow-x-auto relative w-full border-b border-slate-200"></div>'
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

        function tambahData(idHewan, noreg, nama, jenis, umur, berat) {
            document.getElementById('tambah_id_hewan').value = idHewan;
            document.getElementById('tambah_hewan_label').value = noreg + ' - ' + nama + ' (' + jenis + ')';
            
            // Assume it's already paid since they reached here, or we can check PHP side, but usually in table we just assume or check.
            // Actually, we don't need status lunas display since we simplified it.
            let container = document.getElementById('status_lunas_display');
            if (container) {
                container.innerHTML = `<i class="fas fa-check-circle"></i> Memeriksa Pembayaran...`;
                container.className = "w-full px-3 py-2 text-sm bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-700 font-bold flex items-center gap-2 cursor-default shadow-inner";
            }
            
            toggleModal('modalTambah');
        }

        function editData(button) {
            let id = button.getAttribute('data-id');
            let url = "{{ url('/pemotongan/update') }}/" + id;
            document.getElementById('formEdit').action = url;

            document.getElementById('edit_info_reg').innerText = button.getAttribute('data-reg');
            document.getElementById('edit_info_nama').innerText = button.getAttribute('data-nama');
            document.getElementById('edit_info_umur').innerText = button.getAttribute('data-umur');
            document.getElementById('edit_info_berat').innerText = button.getAttribute('data-berat') + ' Kg';

            document.getElementById('edit_tanggal').value = button.getAttribute('data-tanggal');
            document.getElementById('edit_jam').value = button.getAttribute('data-jam');
            document.getElementById('edit_status').value = button.getAttribute('data-status');
            document.getElementById('edit_karkas').value = button.getAttribute('data-karkas');
            document.getElementById('edit_jeroan').value = button.getAttribute('data-jeroan');

            toggleModal('modalEdit');
        }

        function konfirmasiHapus(url) {
            document.getElementById('formHapus').action = url;
            toggleModal('modalHapus');
        }

        function exportExcelPemotongan() {
            let dt = $('#tabelPemotongan').DataTable();
            let currentLength = dt.page.len();
            dt.page.len(-1).draw();

            let table = document.getElementById("tabelPemotongan");
            let cloneTable = table.cloneNode(true);

            let rows = cloneTable.querySelectorAll('tr');
            rows.forEach(row => {
                let cells = row.querySelectorAll('th, td');
                if (cells.length > 0) row.removeChild(cells[cells.length - 1]);
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
                worksheet: 'Hasil Potong',
                table: cloneTable.innerHTML
            }

            let link = document.createElement("a");
            link.download = "Export_Data_Pemotongan_RPH.xls";
            link.href = uri + base64(format(template, ctx));
            link.click();

            dt.page.len(currentLength).draw();
        }
    </script>
@endsection
