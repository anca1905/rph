@extends('layouts.app')

@section('title', 'Data Pembayaran Retribusi')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #modalResi {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                background: transparent !important;
                display: block !important;
                visibility: visible !important;
            }

            .print-overflow-visible {
                overflow: visible !important;
                max-height: none !important;
            }

            #area-print-resi,
            #area-print-resi * {
                visibility: visible !important;
            }

            #area-print-resi {
                position: relative !important;
                margin: 20px auto !important;
                width: 380px !important;
                padding: 30px !important;
                background-color: white !important;
                border: 1px solid #cbd5e1 !important;
                border-radius: 12px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .struk-ticket {
                border: none !important;
                box-shadow: none !important;
                background-image: none !important;
            }

            .no-print {
                display: none !important;
            }
        }

        .struk-ticket {
            background-image: radial-gradient(circle at 10px 10px, transparent 10px, white 10px);
            background-size: 20px 20px;
            background-position: -10px -10px;
            border-top: 1px solid #e2e8f0;
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
                class="text-emerald-400 hover:text-emerald-600 transition-colors focus:outline-none"><i
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
                class="text-red-400 hover:text-red-600 transition-colors focus:outline-none"><i
                    class="fas fa-times text-lg"></i></button>
        </div>
    @endif

    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 border-b border-slate-100 pb-4 gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Riwayat Transaksi Pembayaran</h2>
                <p class="text-xs text-slate-500 mt-1">Kelola data retribusi pemotongan berdasarkan data registrasi hewan
                <p class="text-xs text-slate-500 mt-1">Kelola data retribusi pemotongan berdasarkan data registrasi hewan
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button onclick="exportExcelPembayaran()"
                    class="px-4 py-2 bg-emerald-50 text-emerald-700 border border-emerald-200 text-sm font-semibold rounded-xl hover:bg-emerald-100 transition-colors shadow-sm flex items-center gap-2">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            </div>
        </div>

        <div class="w-full">
            <table id="tabelPembayaran" class="w-full text-left text-sm whitespace-nowrap">
                <thead
                    class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-4 border-b border-slate-100 text-center">No</th>
                        <th class="px-4 py-4 border-b border-slate-100">Status Pembayaran</th>
                        <th class="px-4 py-4 border-b border-slate-100">No Registrasi</th>
                        <th class="px-4 py-4 border-b border-slate-100">Tanggal Masuk</th>
                        <th class="px-4 py-4 border-b border-slate-100">Nama Pemilik</th>
                        <th class="px-4 py-4 border-b border-slate-100">Asal Hewan</th>
                        <th class="px-4 py-4 border-b border-slate-100">Jenis Hewan</th>
                        <th class="px-4 py-4 border-b border-slate-100">Umur</th>
                        <th class="px-4 py-4 border-b border-slate-100">Berat</th>
                        <th class="px-4 py-4 border-b border-slate-100">Kategori</th>
                        <th class="px-4 py-4 border-b border-slate-100">Status Hewan</th>
                        <th
                            class="px-4 py-4 border-b border-slate-100 text-center sticky right-0 bg-slate-100 z-20 shadow-[-4px_0_6px_-4px_rgba(0,0,0,0.1)]">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-slate-600 divide-y divide-slate-50">
                    @if (isset($hewans) && $hewans->count() > 0)
                        @foreach ($hewans as $hewan)
                            @php
                                $bayar = $hewan->pembayaran;
                            @endphp
                            <tr class="group hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 text-center">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    @if ($bayar && $bayar->status_pembayaran == 'Lunas')
                                        <span
                                            class="px-2.5 py-1 bg-green-50 text-green-600 border border-green-200 rounded-lg text-[10px] font-bold"><i
                                                class="fas fa-check mr-1"></i> LUNAS</span>
                                    @elseif ($bayar && $bayar->status_pembayaran == 'Belum Lunas')
                                        <span
                                            class="px-2.5 py-1 bg-amber-50 text-amber-600 border border-amber-200 rounded-lg text-[10px] font-bold"><i
                                                class="fas fa-clock mr-1"></i> BELUM LUNAS</span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 bg-red-50 text-red-600 border border-red-200 rounded-lg text-[10px] font-bold"><i
                                                class="fas fa-times-circle mr-1"></i> BELUM DIBAYAR</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-800">{{ $hewan->no_registrasi ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ \Carbon\Carbon::parse($hewan->tanggal_masuk)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-700">{{ $hewan->nama_pemilik ?? '-' }}
                                </td>
                                <td class="px-4 py-3">{{ $hewan->asal_hewan ?? '-' }}</td>
                                <td class="px-4 py-3 font-semibold">{{ $hewan->jenis_hewan ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $hewan->umur ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $hewan->berat ?? '-' }} Kg</td>
                                <td class="px-4 py-3 text-blue-600 font-medium">{{ $hewan->kategori ?? '-' }}</td>
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
                                <td
                                    class="px-4 py-3 text-center sticky right-0 bg-white group-hover:bg-slate-50 transition-colors z-10 shadow-[-4px_0_6px_-4px_rgba(0,0,0,0.1)]">
                                    <div class="flex items-center justify-center gap-2">
                                        @if ($bayar)
                                            <button onclick="bukaResi(this)" data-bayar="{{ json_encode($bayar) }}"
                                                data-hewan="{{ json_encode($hewan) }}"
                                                class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors"
                                                title="Cetak/Download Resi">
                                                <i class="fas fa-receipt text-xs"></i>
                                            </button>
                                            @if(auth()->user()->role != 'pimpinan')

                                            {{-- <button onclick="editData(this)" data-bayar="{{ json_encode($bayar) }}"
                                                class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors"
                                                title="Edit Transaksi">
                                                <i class="fas fa-pen text-xs"></i>
                                            </button> --}}
                                            <button
                                                onclick="konfirmasiHapus('{{ route('pembayaran.destroy', $bayar->id_pembayaran) }}')"
                                                class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                                title="Hapus Transaksi">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                            @endif
                                        @else
                                            @if(auth()->user()->role != 'pimpinan')
                                                <button onclick="tambahData({{ $hewan->id_hewan }}, '{{ $hewan->no_registrasi }} - {{ $hewan->nama_pemilik }}')"
                                                    class="px-3 py-1.5 rounded-lg bg-blue-600 text-white font-semibold text-xs hover:bg-blue-700 transition-colors whitespace-nowrap shadow-sm">
                                                    <i class="fas fa-file-invoice-dollar mr-1"></i> Proses Bayar
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

    <div id="modalTambah" class="fixed inset-0 z-[100] hidden no-print">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            onclick="toggleModal('modalTambah')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
            <div
                class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md w-full">
                <div class="bg-white px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800"><i
                            class="fas fa-file-invoice-dollar text-green-600 mr-2"></i> Buat Tagihan Baru</h3>
                    <button onclick="toggleModal('modalTambah')" class="text-slate-400 hover:text-red-500"><i
                            class="fas fa-times text-xl"></i></button>
                </div>
                <form action="{{ route('pembayaran.store') }}" method="POST">
                    @csrf
                    <div class="px-6 py-5 bg-slate-50/50">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Data Registrasi Hewan
                                    (Terkunci)</label>
                                <input type="hidden" name="id_hewan" id="tambah_id_hewan" required>
                                <input type="text" id="tambah_hewan_label" readonly
                                    class="w-full px-3 py-2 text-sm bg-slate-100 text-slate-500 border border-slate-200 rounded-lg pointer-events-none cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Total Biaya (Rp) <span
                                        class="text-red-500">*</span></label>
                                <input type="number" name="total_pembayaran" required placeholder="Contoh: 75000"
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Status Pembayaran <span
                                        class="text-red-500">*</span></label>
                                <select name="status_pembayaran" required
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-green-500">
                                    <option value="Belum Lunas">Belum Lunas</option>
                                    <option value="Lunas">Lunas</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" onclick="toggleModal('modalTambah')"
                            class="px-5 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">Batal</button>
                        <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white bg-green-600 rounded-xl hover:bg-green-700">Simpan
                            Tagihan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="modalEdit" class="fixed inset-0 z-[100] hidden no-print">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            onclick="toggleModal('modalEdit')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
            <div
                class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md w-full">
                <div class="bg-white px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800"><i class="fas fa-pen text-emerald-600 mr-2"></i> Edit
                        Tagihan</h3>
                    <button onclick="toggleModal('modalEdit')" class="text-slate-400 hover:text-red-500"><i
                            class="fas fa-times text-xl"></i></button>
                </div>
                <form id="formEdit" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="px-6 py-5 bg-slate-50/50">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">No Registrasi Hewan
                                    (Terkunci)</label>
                                <input type="hidden" name="id_hewan" id="edit_id_hewan_hidden" required>
                                <input type="text" id="edit_hewan_label" readonly
                                    class="w-full px-3 py-2 text-sm bg-slate-100 text-slate-500 border border-slate-200 rounded-lg pointer-events-none cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Total Biaya (Rp) <span
                                        class="text-red-500">*</span></label>
                                <input type="number" id="edit_total" name="total_pembayaran" required
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Status Pembayaran <span
                                        class="text-red-500">*</span></label>
                                <select id="edit_status" name="status_pembayaran" required
                                    class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                    <option value="Belum Lunas">Belum Lunas</option>
                                    <option value="Lunas">Lunas</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" onclick="toggleModal('modalEdit')"
                            class="px-5 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">Batal</button>
                        <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="modalHapus" class="fixed inset-0 z-[120] hidden no-print">
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
                    <p class="text-sm text-slate-600">Apakah Anda yakin ingin menghapus data tagihan ini?</p>
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

    <div id="modalResi" class="fixed inset-0 z-[130] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity no-print"
            onclick="toggleModal('modalResi')"></div>
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <div
                class="relative bg-white text-left shadow-2xl transform transition-all sm:my-8 sm:max-w-sm w-full struk-ticket rounded-2xl flex flex-col max-h-[90vh]">

                <div class="overflow-y-auto w-full print-overflow-visible flex-1 p-6">
                    <div id="area-print-resi" class="relative bg-white">

                        <div class="text-center mb-5 relative z-10">
                            <h2 class="text-xl font-black text-slate-800 tracking-tight">RPH KOLAKA</h2>
                            <p class="text-[10px] text-slate-500 font-medium mt-1">Jl. Operasional RPH No. 1, Kolaka</p>
                            <div class="w-full border-b-2 border-dashed border-slate-300 mt-4"></div>
                        </div>

                        <div class="space-y-2 text-sm text-slate-600 font-medium relative z-10">
                            <div class="flex justify-between items-center border-b border-slate-50 pb-1.5">
                                <span>No. Registrasi</span>
                                <span id="resi_reg" class="font-bold text-slate-800"></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-50 pb-1.5">
                                <span>Nama Pemilik</span>
                                <span id="resi_nama" class="font-bold text-slate-800 uppercase"></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-50 pb-1.5">
                                <span>Asal Hewan</span>
                                <span id="resi_asal" class="text-slate-800"></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-50 pb-1.5">
                                <span>Umur</span>
                                <span id="resi_umur" class="text-slate-800"></span>
                            </div>
                            <div class="flex justify-between items-center pb-1.5">
                                <span>Berat</span>
                                <span id="resi_berat" class="text-slate-800"></span>
                            </div>
                        </div>

                        <div class="w-full border-b border-dashed border-slate-300 my-4"></div>

                        <div
                            class="flex justify-between items-center mb-4 bg-slate-50 p-3 rounded-lg border border-slate-100 relative z-10">
                            <span class="font-bold text-slate-600">TOTAL</span>
                            <span id="resi_total" class="text-lg font-black text-emerald-600 tracking-tight"></span>
                        </div>

                        <div class="flex justify-center mt-6 mb-4 relative z-10">
                            <div id="resi_status_badge" class="inline-block shadow-sm select-none">
                            </div>
                        </div>

                        <div class="text-center mt-6">
                            <p class="text-[9px] text-slate-400 font-medium">Terima kasih atas kepercayaan Anda.</p>
                        </div>
                    </div>
                </div>

                <div
                    class="px-5 py-3 bg-slate-50 border-t border-slate-200 flex justify-between items-center gap-2 no-print shrink-0 rounded-b-2xl">
                    <button type="button" onclick="toggleModal('modalResi')"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors shadow-sm">
                        Tutup
                    </button>

                    <div class="flex gap-2">
                        <button type="button" onclick="downloadStrukImage()"
                            class="px-4 py-2 text-xs font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 shadow-sm flex items-center gap-1.5 transition-colors">
                            <i class="fas fa-image"></i> Gambar
                        </button>
                        <button type="button" onclick="window.print()"
                            class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm flex items-center gap-1.5 transition-colors">
                            <i class="fas fa-print"></i> PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#tabelPembayaran').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json",
                    "emptyTable": "Belum ada riwayat transaksi pembayaran.",
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

        function tambahData(idHewan, labelHewan) {
            document.getElementById('tambah_id_hewan').value = idHewan;
            document.getElementById('tambah_hewan_label').value = labelHewan;
            toggleModal('modalTambah');
        }

        function editData(button) {
            let dataBayar = JSON.parse(button.getAttribute('data-bayar'));
            let url = "{{ url('/pembayaran/update') }}/" + dataBayar.id_pembayaran;
            document.getElementById('formEdit').action = url;

            document.getElementById('edit_id_hewan_hidden').value = dataBayar.id_hewan;
            document.getElementById('edit_hewan_label').value = dataBayar.hewan.no_registrasi + ' - ' + dataBayar.hewan.nama_pemilik;
            document.getElementById('edit_total').value = dataBayar.total_pembayaran;
            document.getElementById('edit_status').value = dataBayar.status_pembayaran;

            toggleModal('modalEdit');
        }

        function konfirmasiHapus(url) {
            document.getElementById('formHapus').action = url;
            toggleModal('modalHapus');
        }

        // ================= LOGIKA EXPORT EXCEL PEMBAYARAN =================
        function exportExcelPembayaran() {
            let dt = $('#tabelPembayaran').DataTable();
            let currentLength = dt.page.len();
            dt.page.len(-1).draw();

            let table = document.getElementById("tabelPembayaran");
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
                worksheet: 'Riwayat Pembayaran',
                table: cloneTable.innerHTML
            }

            let link = document.createElement("a");
            link.download = "Export_Data_Pembayaran_RPH.xls";
            link.href = uri + base64(format(template, ctx));
            link.click();

            dt.page.len(currentLength).draw();
        }

        function bukaResi(button) {
            let dataBayar = JSON.parse(button.getAttribute('data-bayar'));
            let dataHewan = JSON.parse(button.getAttribute('data-hewan'));

            let rpFormat = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(dataBayar.total_pembayaran);

            document.getElementById('resi_reg').innerText = dataHewan ? dataHewan.no_registrasi : '-';
            document.getElementById('resi_nama').innerText = dataHewan ? dataHewan.nama_pemilik : '-';
            document.getElementById('resi_asal').innerText = dataHewan ? dataHewan.asal_hewan : '-';
            document.getElementById('resi_umur').innerText = dataHewan ? dataHewan.umur : '-';
            document.getElementById('resi_berat').innerText = dataHewan ? dataHewan.berat + ' Kg' : '-';

            document.getElementById('resi_total').innerText = rpFormat;

            let badge = document.getElementById('resi_status_badge');
            badge.innerText = dataBayar.status_pembayaran;
            if (dataBayar.status_pembayaran === 'Lunas') {
                badge.className =
                    "inline-block px-6 py-1.5 border-4 border-dashed border-emerald-500 text-emerald-600 text-xl font-black tracking-widest uppercase rounded-xl transform -rotate-6 bg-emerald-50/80 shadow-sm opacity-90 select-none";
            } else {
                badge.className =
                    "inline-block px-6 py-1.5 border-4 border-dashed border-red-500 text-red-600 text-xl font-black tracking-widest uppercase rounded-xl transform -rotate-6 bg-red-50/80 shadow-sm opacity-90 select-none";
            }

            toggleModal('modalResi');
        }

        function downloadStrukImage() {
            const resiElement = document.getElementById('area-print-resi');
            const noReg = document.getElementById('resi_reg').innerText;

            html2canvas(resiElement, {
                scale: 2,
                backgroundColor: "#ffffff"
            }).then(canvas => {
                let link = document.createElement('a');
                link.download = 'Struk_RPH_' + noReg + '.png';
                link.href = canvas.toDataURL("image/png");
                link.click();
            });
        }
    </script>
@endsection
