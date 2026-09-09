@extends('layouts.app')

@section('title', 'Pemeriksaan Postmortem')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

    <style>
        .note-modal-backdrop {
            display: none !important;
        }

        .note-editor.note-frame {
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            background: white;
        }

        .content-summernote ul {
            list-style-type: disc !important;
            padding-left: 1.5rem !important;
            margin-bottom: 0.5rem;
        }

        .content-summernote ol {
            list-style-type: decimal !important;
            padding-left: 1.5rem !important;
            margin-bottom: 0.5rem;
        }

        .content-summernote p {
            margin-bottom: 0.5rem;
        }

        .content-summernote b,
        .content-summernote strong {
            font-weight: 700 !important;
        }

        .content-summernote i,
        .content-summernote em {
            font-style: italic !important;
        }

        .content-summernote u {
            text-decoration: underline !important;
        }
    </style>

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
                <div class="flex items-center gap-2 mb-2 font-bold"><i class="fas fa-exclamation-triangle"></i> Terjadi
                    Kesalahan Input:</div>
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

    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">

        <div class="flex flex-col md:flex-row justify-between items-center mb-6 border-b border-slate-100 pb-4 gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Catatan Pemeriksaan Postmortem</h2>
                <p class="text-xs text-slate-500 mt-1">Pemeriksaan karkas dan organ dalam (hati, paru, limpa, dll) pasca
                    pemotongan</p>
            </div>

            <div class="flex gap-2">
                <button onclick="exportExcelPostmortem()"
                    class="px-4 py-2 bg-emerald-50 text-emerald-700 border border-emerald-200 text-sm font-semibold rounded-xl hover:bg-emerald-100 transition-colors shadow-sm flex items-center gap-2">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            </div>
        </div>

        <div class="w-full">
            <table id="tabelPostmortem" class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-4 border-b border-slate-100 text-center">No</th>
                        <th class="px-4 py-4 border-b border-slate-100">No Registrasi</th>
                        <th class="px-4 py-4 border-b border-slate-100">Nama Pemilik</th>
                        <th class="px-4 py-4 border-b border-slate-100">Jenis Hewan</th>

                        <th class="px-4 py-4 border-b border-slate-100">Tanggal Periksa</th>
                        <th class="px-4 py-4 border-b border-slate-100">Jam Periksa</th>
                        <th class="px-4 py-4 border-b border-slate-100 text-center">Kondisi Karkas</th>
                        <th class="px-4 py-4 border-b border-slate-100 text-center">Kondisi Jeroan</th>
                        <th class="px-4 py-4 border-b border-slate-100 text-center">Catatan Medis</th>
                        <th class="px-4 py-4 border-b border-slate-100 text-center">Status Proses RPH</th>
                        <th
                            class="px-4 py-4 border-b border-slate-100 text-center sticky right-0 bg-slate-100 z-20 shadow-[-4px_0_6px_-4px_rgba(0,0,0,0.1)]">
                            Aksi</th>
                    </tr>
                </thead>

                <tbody class="text-slate-600 divide-y divide-slate-50">
                    @if (isset($hewans) && $hewans->count() > 0)
                        @foreach ($hewans as $hewan)
                            @php
                                $pm = $hewan->postmortem;
                            @endphp
                            <tr class="group hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 text-center">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-bold text-slate-800">{{ $hewan->no_registrasi ?? '-' }}</td>

                                <td class="px-4 py-3 font-medium text-slate-700">{{ $hewan->nama_pemilik ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $hewan->jenis_hewan ?? '-' }}</td>

                                <td class="px-4 py-3">{{ $pm ? \Carbon\Carbon::parse($pm->waktu_periksa)->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-3">{{ $pm ? \Carbon\Carbon::parse($pm->waktu_periksa)->format('H:i') : '-' }}</td>

                                <td class="px-4 py-3 text-center">
                                    @if ($pm && $pm->kondisi_karkas == 'ASUH (Aman Sehat Utuh Halal)')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 text-green-600 border border-green-200 rounded-lg text-[10px] font-bold"><i
                                                class="fas fa-check"></i> ASUH</span>
                                    @elseif($pm && $pm->kondisi_karkas == 'Bersyarat')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-600 border border-amber-200 rounded-lg text-[10px] font-bold"><i
                                                class="fas fa-minus"></i> BERSYARAT</span>
                                    @elseif($pm)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 text-red-600 border border-red-200 rounded-lg text-[10px] font-bold"><i
                                                class="fas fa-times"></i> DITOLAK</span>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @if ($pm && $pm->kondisi_jeroan == 'ASUH (Aman Sehat Utuh Halal)')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 text-green-600 border border-green-200 rounded-lg text-[10px] font-bold"><i
                                                class="fas fa-check"></i> ASUH</span>
                                    @elseif($pm && $pm->kondisi_jeroan == 'Bersyarat')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-600 border border-amber-200 rounded-lg text-[10px] font-bold"><i
                                                class="fas fa-minus"></i> BERSYARAT</span>
                                    @elseif($pm)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 text-red-600 border border-red-200 rounded-lg text-[10px] font-bold"><i
                                                class="fas fa-times"></i> DITOLAK</span>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @if (!$pm || empty(trim(strip_tags($pm->catatan_medis))))
                                        <span class="text-xs text-slate-400 italic">Tidak ada</span>
                                    @else
                                        <button onclick="bukaCatatan(this)"
                                            data-noreg="{{ $hewan->no_registrasi ?? '-' }}"
                                            data-nama="{{ $hewan->nama_pemilik ?? '-' }}"
                                            data-jenis="{{ $hewan->jenis_hewan ?? '-' }}"
                                            data-catatan="{{ htmlentities($pm->catatan_medis) }}"
                                            class="px-3 py-1 bg-blue-50 text-blue-600 border border-blue-200 hover:bg-blue-100 rounded-lg text-xs font-bold transition-colors shadow-sm">
                                            <i class="fas fa-eye mr-1"></i> Lihat
                                        </button>
                                    @endif
                                </td>

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
                                        } elseif ($statusGlobal == 'Menunggu Dipotong') {
                                            $badgeStyle = 'bg-cyan-50 text-cyan-600 border-cyan-200';
                                            $icon = 'fa-clock';
                                        } elseif ($statusGlobal == 'Selesai Dipotong') {
                                            $badgeStyle = 'bg-teal-50 text-teal-600 border-teal-200';
                                            $icon = 'fa-cut';
                                        }

                                        // Logika 9 Kombinasi Cerdas (Prioritaskan Ditolak -> Bersyarat -> ASUH)
                                        elseif (str_contains($statusGlobal, 'Ditolak')) {
                                            $badgeStyle = 'bg-red-50 text-red-600 border-red-200';
                                            $icon = 'fa-times-circle';
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
                                        @if ($pm)
                                            @if(auth()->user()->role != 'pimpinan')
                                                <button onclick="editData(this)" data-id="{{ $pm->id_postmortem }}"
                                                    data-tanggal="{{ \Carbon\Carbon::parse($pm->waktu_periksa)->format('Y-m-d') }}"
                                                    data-jam="{{ \Carbon\Carbon::parse($pm->waktu_periksa)->format('H:i') }}"
                                                    data-karkas="{{ $pm->kondisi_karkas }}"
                                                    data-jeroan="{{ $pm->kondisi_jeroan }}"
                                                    data-limpa="{{ $pm->limpa }}"
                                                    data-hati="{{ $pm->hati }}"
                                                    data-daging="{{ $pm->daging }}"
                                                    data-paru="{{ $pm->paru_paru }}"
                                                    data-catatan="{{ htmlentities($pm->catatan_medis) }}"
                                                    data-reg="{{ $hewan->no_registrasi ?? '-' }}"
                                                    data-nama="{{ $hewan->nama_pemilik ?? '-' }}"
                                                    data-umur="{{ $hewan->umur ?? '-' }}"
                                                    class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors"
                                                    title="Edit Data">
                                                    <i class="fas fa-pen text-xs"></i>
                                                </button>
                                                <button
                                                    onclick="konfirmasiHapus('{{ route('postmortem.destroy', $pm->id_postmortem) }}')"
                                                    class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                                    title="Hapus Data">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            @endif
                                        @else
                                            @php
                                                $isSelesaiDipotong = $hewan->pemotongan && $hewan->pemotongan->status_pemotongan == 'Selesai Dipotong';
                                            @endphp

                                            @if($isSelesaiDipotong)
                                                @if(auth()->user()->role != 'pimpinan')
                                                    <button onclick="tambahData({{ $hewan->id_hewan }}, '{{ $hewan->no_registrasi }}', '{{ $hewan->nama_pemilik }}', '{{ $hewan->jenis_hewan }}', '{{ $hewan->umur }}')"
                                                        class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white font-semibold text-xs hover:bg-indigo-700 transition-colors whitespace-nowrap shadow-sm">
                                                        <i class="fas fa-microscope mr-1"></i> Proses PM
                                                    </button>
                                                @endif
                                            @else
                                                <button disabled
                                                    class="px-3 py-1.5 rounded-lg bg-slate-200 text-slate-400 font-semibold text-xs cursor-not-allowed whitespace-nowrap"
                                                    title="Belum memenuhi syarat (Harus Selesai Dipotong terlebih dahulu)">
                                                    <i class="fas fa-ban mr-1"></i> Belum Siap
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

    <div id="modalCatatan" class="fixed inset-0 z-[130] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            onclick="toggleModal('modalCatatan')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 py-8 text-center sm:p-0">
            <div
                class="relative bg-white rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:max-w-xl w-full flex flex-col max-h-[85vh] overflow-hidden border border-slate-100">
                <div
                    class="bg-gradient-to-r from-teal-600 to-cyan-500 px-6 py-4 flex justify-between items-center shrink-0">
                    <h3 class="text-lg font-bold text-white"><i class="fas fa-file-medical-alt mr-2 opacity-80"></i>
                        Detail Catatan Medis</h3>
                    <button onclick="toggleModal('modalCatatan')"
                        class="text-teal-100 hover:text-white transition-colors focus:outline-none"><i
                            class="fas fa-times text-xl"></i></button>
                </div>
                <div class="px-6 pt-5 pb-2 bg-slate-50 border-b border-slate-200 shrink-0">
                    <div class="grid grid-cols-3 gap-4 text-sm bg-white p-3 rounded-xl border border-slate-100 shadow-sm">
                        <div>
                            <span class="text-slate-400 block text-[10px] font-bold uppercase mb-1">No. Registrasi</span>
                            <span id="detail_info_noreg" class="font-bold text-slate-800"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] font-bold uppercase mb-1">Nama Pemilik</span>
                            <span id="detail_info_nama" class="font-bold text-slate-800"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] font-bold uppercase mb-1">Jenis Hewan</span>
                            <span id="detail_info_jenis" class="font-bold text-slate-800"></span>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-4 mb-2">Isi Catatan Medis Pasca
                        Potong:</p>
                </div>
                <div class="px-6 py-4 bg-white overflow-y-auto flex-1">
                    <div id="isi_catatan" class="content-summernote text-sm text-slate-700 leading-relaxed"></div>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end shrink-0">
                    <button type="button" onclick="toggleModal('modalCatatan')"
                        class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-slate-800 transition-all focus:outline-none focus:ring-2 focus:ring-slate-200 shadow-sm">Tutup
                        Catatan</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modalTambah" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            onclick="toggleModal('modalTambah')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div
                class="relative bg-white rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl w-full flex flex-col max-h-[90vh] overflow-hidden border border-slate-100">
                <div class="bg-gradient-to-r from-teal-600 to-cyan-500 px-6 py-4 flex justify-between items-center shrink-0">
                    <h3 class="text-lg font-bold text-white"><i class="fas fa-microscope mr-2 opacity-80"></i>
                        Input Pemeriksaan Postmortem</h3>
                    <button onclick="toggleModal('modalTambah')" class="text-teal-100 hover:text-white transition-colors focus:outline-none"><i
                            class="fas fa-times text-xl"></i></button>
                </div>
                <form action="{{ route('postmortem.store') }}" method="POST" class="flex-1 overflow-y-auto">
                    @csrf
                    <div class="px-6 py-5 bg-slate-50/50">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Data Registrasi
                                    Hewan (Terkunci)</label>
                                <input type="hidden" name="id_hewan" id="tambah_id_hewan" required>
                                <input type="text" id="tambah_hewan_label" readonly
                                    class="w-full px-3 py-2 text-sm bg-slate-100 text-slate-500 border border-slate-200 rounded-lg pointer-events-none cursor-not-allowed">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal Pemeriksaan
                                        <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_periksa" required value="{{ date('Y-m-d') }}"
                                        class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jam
                                        Pemeriksaan</label>
                                    <input type="time" name="jam_periksa" value="{{ date('H:i') }}"
                                        class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kondisi Karkas
                                        (Daging) <span class="text-red-500">*</span></label>
                                    <select name="kondisi_karkas" required
                                        class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                                        <option value="">-- Pilih Keputusan --</option>
                                        <option value="ASUH (Aman Sehat Utuh Halal)">ASUH (Aman Sehat Utuh Halal)</option>
                                        <option value="Bersyarat">Bersyarat</option>
                                        <option value="Ditolak">Ditolak</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kondisi Jeroan
                                        (Hati/Paru) <span class="text-red-500">*</span></label>
                                    <select name="kondisi_jeroan" required
                                        class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                                        <option value="">-- Pilih Keputusan --</option>
                                        <option value="ASUH (Aman Sehat Utuh Halal)">ASUH (Aman Sehat Utuh Halal)</option>
                                        <option value="Bersyarat">Bersyarat</option>
                                        <option value="Ditolak">Ditolak</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-1.5 uppercase">Limpa <span class="text-red-500">*</span></label>
                                    <select name="limpa" required class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                                        <option value="Normal">Normal</option>
                                        <option value="Abnormal">Abnormal</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-1.5 uppercase">Hati <span class="text-red-500">*</span></label>
                                    <select name="hati" required class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                                        <option value="Normal">Normal</option>
                                        <option value="Abnormal">Abnormal</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-1.5 uppercase">Daging <span class="text-red-500">*</span></label>
                                    <select name="daging" required class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                                        <option value="Normal">Normal</option>
                                        <option value="Abnormal">Abnormal</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-1.5 uppercase">Paru-paru <span class="text-red-500">*</span></label>
                                    <select name="paru_paru" required class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                                        <option value="Normal">Normal</option>
                                        <option value="Abnormal">Abnormal</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Catatan Medis & Tindakan
                                    Lanjut</label>
                                <textarea name="catatan_medis" class="summernote"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end gap-3 shrink-0">
                        <button type="button" onclick="toggleModal('modalTambah')"
                            class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-slate-800 transition-all focus:outline-none focus:ring-2 focus:ring-slate-200">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-teal-500 to-cyan-500 rounded-xl hover:from-teal-600 hover:to-cyan-600 shadow-md shadow-teal-500/30 transform hover:-translate-y-0.5 transition-all focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-1"><i class="fas fa-save mr-1.5"></i> Simpan
                            Pemeriksaan</button>
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
                class="relative bg-white rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl w-full flex flex-col max-h-[90vh] overflow-hidden border border-slate-100">
                <div class="bg-gradient-to-r from-teal-600 to-cyan-500 px-6 py-4 flex justify-between items-center shrink-0">
                    <h3 class="text-lg font-bold text-white"><i class="fas fa-pen mr-2 opacity-80"></i> Edit
                        Data Postmortem</h3>
                    <button onclick="toggleModal('modalEdit')" class="text-teal-100 hover:text-white transition-colors focus:outline-none"><i
                            class="fas fa-times text-xl"></i></button>
                </div>
                <form id="formEdit" action="" method="POST" class="flex-1 overflow-y-auto">
                    @csrf
                    @method('PUT')

                    <div class="px-6 pt-5 pb-4 bg-slate-50 border-b border-slate-200">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Informasi Hewan
                            (Otomatis)</p>
                        <div
                            class="grid grid-cols-3 gap-4 text-sm bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                            <div>
                                <span class="text-slate-400 block text-[10px] font-bold uppercase mb-1">No. Reg</span>
                                <span id="edit_info_reg" class="font-bold text-slate-800"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px] font-bold uppercase mb-1">Nama Pemilik</span>
                                <span id="edit_info_nama" class="font-bold text-slate-800"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px] font-bold uppercase mb-1">Umur</span>
                                <span id="edit_info_umur" class="font-bold text-slate-800"></span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-5 bg-white">
                        <div class="space-y-4">

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal Pemeriksaan
                                        <span class="text-red-500">*</span></label>
                                    <input type="date" id="edit_tanggal" name="tanggal_periksa" required
                                        class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jam
                                        Pemeriksaan</label>
                                    <input type="time" id="edit_jam" name="jam_periksa"
                                        class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kondisi Karkas <span
                                            class="text-red-500">*</span></label>
                                    <select id="edit_karkas" name="kondisi_karkas" required
                                        class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                        <option value="ASUH (Aman Sehat Utuh Halal)">ASUH (Aman Sehat Utuh Halal)</option>
                                        <option value="Bersyarat">Bersyarat</option>
                                        <option value="Ditolak">Ditolak</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kondisi Jeroan <span
                                            class="text-red-500">*</span></label>
                                    <select id="edit_jeroan" name="kondisi_jeroan" required
                                        class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                        <option value="ASUH (Aman Sehat Utuh Halal)">ASUH (Aman Sehat Utuh Halal)</option>
                                        <option value="Bersyarat">Bersyarat</option>
                                        <option value="Ditolak">Ditolak</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-1.5 uppercase">Limpa <span class="text-red-500">*</span></label>
                                    <select name="limpa" id="edit_limpa" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                        <option value="Normal">Normal</option>
                                        <option value="Abnormal">Abnormal</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-1.5 uppercase">Hati <span class="text-red-500">*</span></label>
                                    <select name="hati" id="edit_hati" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                        <option value="Normal">Normal</option>
                                        <option value="Abnormal">Abnormal</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-1.5 uppercase">Daging <span class="text-red-500">*</span></label>
                                    <select name="daging" id="edit_daging" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                        <option value="Normal">Normal</option>
                                        <option value="Abnormal">Abnormal</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-1.5 uppercase">Paru-paru <span class="text-red-500">*</span></label>
                                    <select name="paru_paru" id="edit_paru_paru" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                        <option value="Normal">Normal</option>
                                        <option value="Abnormal">Abnormal</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Catatan Medis</label>
                                <textarea id="edit_catatan" name="catatan_medis" class="summernote"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 shrink-0">
                        <button type="button" onclick="toggleModal('modalEdit')"
                            class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-slate-800 transition-all focus:outline-none focus:ring-2 focus:ring-slate-200">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-teal-500 to-cyan-500 rounded-xl hover:from-teal-600 hover:to-cyan-600 shadow-md shadow-teal-500/30 transform hover:-translate-y-0.5 transition-all focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-1"><i class="fas fa-check-circle mr-1.5"></i> Simpan
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
                        <i class="fas fa-trash-alt text-2xl"></i>
                    </div>
                    <p class="text-sm text-slate-600">Apakah Anda yakin ingin menghapus data postmortem ini?</p>
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
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#tabelPostmortem').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json",
                    "emptyTable": "Belum ada catatan pemeriksaan postmortem.",
                    "search": "Cari Registrasi / Pemilik:",
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

            $('.summernote').summernote({
                placeholder: 'Ketik catatan medis atau tindak lanjut di sini...',
                tabsize: 2,
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                ]
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

        function tambahData(idHewan, noreg, nama, jenis, umur) {
            document.getElementById('tambah_id_hewan').value = idHewan;
            document.getElementById('tambah_hewan_label').value = noreg + ' - ' + nama + ' (' + jenis + ')';
            toggleModal('modalTambah');
        }

        // ================= LOGIKA BUKA CATATAN =================
        function bukaCatatan(button) {
            document.getElementById('detail_info_noreg').innerText = button.getAttribute('data-noreg');
            document.getElementById('detail_info_nama').innerText = button.getAttribute('data-nama');
            document.getElementById('detail_info_jenis').innerText = button.getAttribute('data-jenis');

            let rawCatatan = document.createElement("textarea");
            rawCatatan.innerHTML = button.getAttribute('data-catatan');
            document.getElementById('isi_catatan').innerHTML = rawCatatan.value;

            toggleModal('modalCatatan');
        }

        // ================= LOGIKA EDIT =================
        function editData(button) {
            let id = button.getAttribute('data-id');
            let url = "{{ url('/postmortem/update') }}/" + id;
            document.getElementById('formEdit').action = url;

            document.getElementById('edit_info_reg').innerText = button.getAttribute('data-reg');
            document.getElementById('edit_info_nama').innerText = button.getAttribute('data-nama');
            document.getElementById('edit_info_umur').innerText = button.getAttribute('data-umur');

            document.getElementById('edit_tanggal').value = button.getAttribute('data-tanggal');
            document.getElementById('edit_jam').value = button.getAttribute('data-jam');
            document.getElementById('edit_karkas').value = button.getAttribute('data-karkas');
            document.getElementById('edit_jeroan').value = button.getAttribute('data-jeroan');
            document.getElementById('edit_limpa').value = button.getAttribute('data-limpa');
            document.getElementById('edit_hati').value = button.getAttribute('data-hati');
            document.getElementById('edit_daging').value = button.getAttribute('data-daging');
            document.getElementById('edit_paru_paru').value = button.getAttribute('data-paru');

            let rawCatatan = document.createElement("textarea");
            rawCatatan.innerHTML = button.getAttribute('data-catatan');
            $('#edit_catatan').summernote('code', rawCatatan.value);

            toggleModal('modalEdit');
        }

        // ================= LOGIKA HAPUS =================
        function konfirmasiHapus(url) {
            document.getElementById('formHapus').action = url;
            toggleModal('modalHapus');
        }

        // ================= EXPORT EXCEL =================
        function exportExcelPostmortem() {
            let dt = $('#tabelPostmortem').DataTable();
            let currentLength = dt.page.len();
            dt.page.len(-1).draw();

            let table = document.getElementById("tabelPostmortem");
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
                worksheet: 'Postmortem',
                table: cloneTable.innerHTML
            }

            let link = document.createElement("a");
            link.download = "Export_Data_Postmortem_RPH.xls";
            link.href = uri + base64(format(template, ctx));
            link.click();

            dt.page.len(currentLength).draw();
        }
    </script>
@endsection
