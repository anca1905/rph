@extends('layouts.app')

@section('title', 'Laporan Operasional RPH')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">

    <div id="downloadToast"
        class="fixed bottom-8 right-8 z-[100] hidden bg-slate-800 text-white px-5 py-4 rounded-2xl shadow-2xl flex items-center gap-4 transform translate-y-10 transition-all duration-500 opacity-0 border border-slate-700">
        <i id="toastIcon" class="fas fa-spinner fa-spin text-2xl text-green-400"></i>
        <div>
            <h4 class="font-bold text-sm" id="toastTitle">Memproses Laporan</h4>
            <p class="text-xs text-slate-300" id="toastDesc">Mohon tunggu sebentar...</p>
        </div>
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 mb-6">
        <h3 class="text-sm font-bold text-slate-800 mb-4"><i class="fas fa-filter text-green-600 mr-2"></i> Filter & Tarik
            Laporan</h3>

        <form action="{{ route('laporan.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ $start_date ?? '' }}"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-700 font-medium text-sm rounded-xl focus:outline-none focus:border-green-500 transition-colors shadow-inner">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">Tanggal Selesai</label>
                    <input type="date" name="end_date" value="{{ $end_date ?? '' }}"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-700 font-medium text-sm rounded-xl focus:outline-none focus:border-green-500 transition-colors shadow-inner">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">Jenis Laporan</label>
                    <select name="jenis_laporan"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 text-slate-700 font-medium text-sm rounded-xl focus:outline-none focus:border-green-500 transition-colors shadow-inner">
                        <option value="">-- Pilih Jenis Data --</option>
                        <option value="hewan" {{ isset($jenis_laporan) && $jenis_laporan == 'hewan' ? 'selected' : '' }}>
                            Data Hewan (Reguler)</option>

                        <option value="pembayaran"
                            {{ isset($jenis_laporan) && $jenis_laporan == 'pembayaran' ? 'selected' : '' }}>Data Pembayaran
                            Retribusi</option>
                        <option value="antemortem"
                            {{ isset($jenis_laporan) && $jenis_laporan == 'antemortem' ? 'selected' : '' }}>Data Pemeriksaan
                            Antemortem</option>
                        <option value="pemotongan"
                            {{ isset($jenis_laporan) && $jenis_laporan == 'pemotongan' ? 'selected' : '' }}>Data Hasil
                            Pemotongan</option>
                        <option value="postmortem"
                            {{ isset($jenis_laporan) && $jenis_laporan == 'postmortem' ? 'selected' : '' }}>Data
                            Pemeriksaan Postmortem</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="w-full px-4 py-2.5 bg-green-600 text-white text-sm font-bold rounded-xl hover:bg-green-700 transition-colors shadow-sm flex justify-center items-center gap-2">
                        <i class="fas fa-search"></i> Tampilkan Laporan
                    </button>
                </div>
            </div>
        </form>

        @if (isset($jenis_laporan) && $jenis_laporan != '')
            <div class="flex flex-wrap items-center justify-between mt-6 pt-6 border-t border-slate-100">
                <div class="flex flex-wrap gap-3">
                    <button onclick="exportLaporan('excel')"
                        class="px-4 py-2 bg-emerald-50 text-emerald-700 border border-emerald-200 text-sm font-bold rounded-xl hover:bg-emerald-100 transition-colors shadow-sm flex items-center gap-2">
                        <i class="fas fa-file-excel"></i> Unduh Excel
                    </button>
                    <button onclick="exportLaporan('pdf')"
                        class="px-4 py-2 bg-red-50 text-red-600 border border-red-200 text-sm font-bold rounded-xl hover:bg-red-100 transition-colors shadow-sm flex items-center gap-2">
                        <i class="fas fa-file-pdf"></i> Unduh PDF
                    </button>
                </div>

                <div>
                    <button onclick="openModalCetak()" type="button"
                        class="px-4 py-2 bg-slate-50 text-slate-700 border border-slate-300 text-sm font-bold rounded-xl hover:bg-slate-200 transition-colors shadow-sm flex items-center gap-2">
                        <i class="fas fa-cog text-slate-500"></i> Atur Format Cetak
                    </button>
                </div>
            </div>
        @endif
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 mb-8">
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-sm font-bold text-slate-800">Pratinjau Tabel Data Laporan</h2>
                <p class="text-xs text-slate-500 mt-1">
                    @if (isset($jenis_laporan) && $jenis_laporan != '')
                        Menampilkan data lengkap untuk periode terpilih.
                    @else
                        Tampilan akan menyesuaikan dengan filter pencarian Anda.
                    @endif
                </p>
            </div>

            @if (in_array($jenis_laporan, ['antemortem', 'pemotongan', 'postmortem']))
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-2 rounded-xl">
                    <label class="text-xs font-bold text-slate-600"><i class="fas fa-filter text-green-600 mr-1"></i> Saring
                        Kategori:</label>
                    <select id="dtKategoriFilter"
                        class="bg-white border border-slate-200 text-xs font-semibold text-slate-700 rounded-lg px-2 py-1.5 focus:outline-none focus:border-green-500 cursor-pointer shadow-sm">
                        <option value="">Semua Kategori (Harian & Qurban)</option>
                        <option value="Hewan Harian">Hewan Harian (Reguler)</option>
                        <option value="Hewan Idul Adha">Hewan Idul Adha (Qurban)</option>
                    </select>
                </div>
            @endif
        </div>

        @if (!isset($jenis_laporan) || $jenis_laporan == '')
            <div
                class="flex flex-col items-center justify-center py-16 px-4 text-center border-2 border-dashed border-slate-200 rounded-xl bg-slate-50">
                <div
                    class="w-20 h-20 mb-4 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-100 text-slate-300">
                    <i class="fas fa-search text-3xl"></i>
                </div>
                <h3 class="text-base font-bold text-slate-700 mb-1">Belum Ada Data yang Ditampilkan</h3>
                <p class="text-sm text-slate-500 max-w-md mx-auto">
                    Silakan pilih <b>Periode Waktu</b> dan <b>Jenis Laporan</b> pada panel filter di atas, kemudian klik
                    tombol <span class="font-semibold text-green-600">Tampilkan Laporan</span>.
                </p>
            </div>
        @else
            <div class="w-full">
                <table id="tabelLaporan" class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-4 border-b border-slate-100 text-center">No</th>
                            @if ($jenis_laporan == 'hewan')
                                <th class="px-4 py-4 border-b border-slate-100">No Registrasi</th>
                                <th class="px-4 py-4 border-b border-slate-100">Tgl Masuk</th>
                                <th class="px-4 py-4 border-b border-slate-100">Nama Pemilik</th>
                                <th class="px-4 py-4 border-b border-slate-100">Asal Hewan</th>
                                <th class="px-4 py-4 border-b border-slate-100">Kategori</th>
                                <th class="px-4 py-4 border-b border-slate-100">Jenis Hewan</th>
                                <th class="px-4 py-4 border-b border-slate-100">Kelamin</th>
                                <th class="px-4 py-4 border-b border-slate-100">Umur</th>
                                <th class="px-4 py-4 border-b border-slate-100">Berat</th>
                            @elseif($jenis_laporan == 'pembayaran')
                                <th class="px-4 py-4 border-b border-slate-100">No Registrasi</th>
                                <th class="px-4 py-4 border-b border-slate-100">Nama Pemilik</th>
                                <th class="px-4 py-4 border-b border-slate-100">Asal Hewan</th>
                                <th class="px-4 py-4 border-b border-slate-100">Jenis Hewan</th>
                                <th class="px-4 py-4 border-b border-slate-100">Kategori</th>
                                <th class="px-4 py-4 border-b border-slate-100">Tgl Transaksi</th>
                                <th class="px-4 py-4 border-b border-slate-100">Total Biaya</th>
                                <th class="px-4 py-4 border-b border-slate-100">Status Bayar</th>
                            @elseif($jenis_laporan == 'antemortem')
                                <th class="px-4 py-4 border-b border-slate-100">No Registrasi</th>
                                <th class="px-4 py-4 border-b border-slate-100">Nama Pemilik</th>
                                <th class="px-4 py-4 border-b border-slate-100">Asal Hewan</th>
                                <th class="px-4 py-4 border-b border-slate-100">Jenis Hewan</th>
                                <th class="px-4 py-4 border-b border-slate-100">Kategori</th>
                                <th class="px-4 py-4 border-b border-slate-100">Umur</th>
                                <th class="px-4 py-4 border-b border-slate-100">Berat</th>
                                <th class="px-4 py-4 border-b border-slate-100">Tgl Periksa</th>
                                <th class="px-4 py-4 border-b border-slate-100">Status AM</th>
                            @elseif($jenis_laporan == 'pemotongan')
                                <th class="px-4 py-4 border-b border-slate-100">No Registrasi</th>
                                <th class="px-4 py-4 border-b border-slate-100">Nama Pemilik</th>
                                <th class="px-4 py-4 border-b border-slate-100">Asal Hewan</th>
                                <th class="px-4 py-4 border-b border-slate-100">Jenis Hewan</th>
                                <th class="px-4 py-4 border-b border-slate-100">Kategori</th>
                                <th class="px-4 py-4 border-b border-slate-100">Waktu Potong</th>
                                <th class="px-4 py-4 border-b border-slate-100">Karkas (Kg)</th>
                                <th class="px-4 py-4 border-b border-slate-100">Jeroan (Kg)</th>
                                <th class="px-4 py-4 border-b border-slate-100">Status Potong</th>
                            @elseif($jenis_laporan == 'postmortem')
                                <th class="px-4 py-4 border-b border-slate-100">No Registrasi</th>
                                <th class="px-4 py-4 border-b border-slate-100">Nama Pemilik</th>
                                <th class="px-4 py-4 border-b border-slate-100">Asal Hewan</th>
                                <th class="px-4 py-4 border-b border-slate-100">Jenis Hewan</th>
                                <th class="px-4 py-4 border-b border-slate-100">Kategori</th>
                                <th class="px-4 py-4 border-b border-slate-100">Waktu Periksa</th>
                                <th class="px-4 py-4 border-b border-slate-100">Kondisi Karkas</th>
                                <th class="px-4 py-4 border-b border-slate-100">Kondisi Jeroan</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody class="text-slate-600 divide-y divide-slate-50">
                        @foreach ($dataLaporan as $row)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 text-center">{{ $loop->iteration }}</td>

                                @if ($jenis_laporan == 'hewan')
                                    <td class="px-4 py-3 font-bold">{{ $row->no_registrasi }}</td>
                                    <td class="px-4 py-3">
                                        {{ \Carbon\Carbon::parse($row->tanggal_masuk)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $row->nama_pemilik }}</td>
                                    <td class="px-4 py-3">{{ $row->asal_hewan }}</td>
                                    <td class="px-4 py-3">{{ $row->kategori }}</td>
                                    <td class="px-4 py-3">{{ $row->jenis_hewan }}</td>
                                    <td class="px-4 py-3">{{ $row->jenis_kelamin }}</td>
                                    <td class="px-4 py-3">{{ $row->umur ?? '-' }}</td>
                                    <td class="px-4 py-3 font-semibold">{{ $row->berat ?? '-' }} Kg</td>
                                @elseif($jenis_laporan == 'pembayaran')
                                    <td class="px-4 py-3 font-bold">{{ $row->hewan->no_registrasi ?? '-' }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $row->hewan->nama_pemilik ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->hewan->asal_hewan ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->hewan->jenis_hewan ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->hewan->kategori ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-emerald-600">Rp
                                        {{ number_format($row->total_pembayaran, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="{{ $row->status_pembayaran == 'Lunas' ? 'text-green-600' : 'text-red-600' }} font-bold text-xs">{{ $row->status_pembayaran }}</span>
                                    </td>
                                @elseif($jenis_laporan == 'antemortem')
                                    <td class="px-4 py-3 font-bold">{{ $row->hewan->no_registrasi ?? '-' }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $row->hewan->nama_pemilik ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->hewan->asal_hewan ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->hewan->jenis_hewan ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->hewan->kategori ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->hewan->umur ?? '-' }}</td>
                                    <td class="px-4 py-3 font-semibold">{{ $row->hewan->berat ?? '-' }} Kg</td>
                                    <td class="px-4 py-3">
                                        {{ \Carbon\Carbon::parse($row->tanggal_periksa)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 font-bold">{{ $row->status_antemortem }}</td>
                                @elseif($jenis_laporan == 'pemotongan')
                                    <td class="px-4 py-3 font-bold">{{ $row->hewan->no_registrasi ?? '-' }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $row->hewan->nama_pemilik ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->hewan->asal_hewan ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->hewan->jenis_hewan ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->hewan->kategori ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        {{ \Carbon\Carbon::parse($row->waktu_potong)->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 font-bold text-emerald-600">{{ $row->berat_karkas }} Kg</td>
                                    <td class="px-4 py-3 font-bold">{{ $row->berat_jeroan ?? '-' }} Kg</td>
                                    <td class="px-4 py-3 font-bold">{{ $row->status_pemotongan }}</td>
                                @elseif($jenis_laporan == 'postmortem')
                                    <td class="px-4 py-3 font-bold">{{ $row->hewan->no_registrasi ?? '-' }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $row->hewan->nama_pemilik ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->hewan->asal_hewan ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->hewan->jenis_hewan ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->hewan->kategori ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        {{ \Carbon\Carbon::parse($row->waktu_periksa)->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 font-bold">{{ $row->kondisi_karkas }}</td>
                                    <td class="px-4 py-3 font-bold">{{ $row->kondisi_jeroan }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div id="modalPengaturanCetak"
        class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-xl relative mx-4">
            <button onclick="closeModalCetak()"
                class="absolute top-4 right-4 text-slate-400 hover:text-red-500 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>

            <h3 class="text-lg font-bold text-slate-800 mb-1"><i class="fas fa-pen-nib text-green-600 mr-2"></i>
                Pengaturan Format Cetak</h3>
            <p class="text-xs text-slate-500 mb-5">Atur Nama dan NIP Kepala Dinas yang akan disematkan ke dalam QR Code pengesahan dokumen cetak PDF.</p>

            <form action="{{ route('laporan.simpan_ttd') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Kepala Dinas</label>
                        <input type="text" name="nama_ttd" value="{{ session('nama_ttd', '') }}"
                            placeholder="Contoh: Drh. Ahmad Fauzi"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">NIP Kepala Dinas</label>
                        <input type="text" name="nip_ttd" value="{{ session('nip_ttd', '') }}"
                            placeholder="Contoh: 19800101 200501 1 001"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Pangkat dan Golongan</label>
                        <input type="text" name="pangkat_ttd" value="{{ session('pangkat_ttd', '') }}"
                            placeholder="Contoh: Pembina Utama Muda, Gol. IV/c"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Orientasi Kertas Cetak PDF</label>
                        <select name="orientasi_cetak" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-green-500">
                            <option value="landscape" {{ session('orientasi_cetak') == 'landscape' ? 'selected' : '' }}>Landscape (Mendatar)</option>
                            <option value="portrait" {{ session('orientasi_cetak') == 'portrait' ? 'selected' : '' }}>Portrait (Tegak)</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" onclick="closeModalCetak()"
                        class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-sm font-bold hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-bold hover:bg-green-700 transition-colors shadow-sm">Simpan
                        Pengaturan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>

    <script>
        // Fungsi Modal Cetak
        function openModalCetak() {
            document.getElementById('modalPengaturanCetak').classList.remove('hidden');
        }

        function closeModalCetak() {
            document.getElementById('modalPengaturanCetak').classList.add('hidden');
        }

        // Fungsi Menampilkan Notifikasi Unduhan Custom
        function showDownloadToast(title, desc, iconClass, colorClass) {
            let toast = document.getElementById('downloadToast');
            let icon = document.getElementById('toastIcon');

            document.getElementById('toastTitle').innerText = title;
            document.getElementById('toastDesc').innerText = desc;

            // Reset ikon dan warna
            icon.className = `fas fa-spin text-2xl ${iconClass} ${colorClass}`;

            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.remove('translate-y-10', 'opacity-0');
            }, 10);

            // Sembunyikan otomatis setelah 4 detik
            setTimeout(() => {
                toast.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => toast.classList.add('hidden'), 500);
            }, 4000);
        }

        $(document).ready(function() {
            if ($('#tabelLaporan').length > 0) {
                let dtTable = $('#tabelLaporan').DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json",
                        "emptyTable": "Tidak ada data laporan untuk periode waktu dan parameter ini.",
                        "search": "Cari Data Pratinjau:",
                    },
                    "classes": {
                        "sWrapper": "dataTables_wrapper dt-tailwindcss w-full mt-2",
                        "sFilterInput": "px-3 py-1.5 ml-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-green-500 text-sm",
                        "sLengthSelect": "px-3 py-1.5 mx-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-green-500 text-sm"
                    },
                    "ordering": false,
                    "initComplete": function(settings, json) {
                        $(this).wrap(
                            '<div class="overflow-x-auto relative w-full border-b border-slate-200 rounded-b-lg mt-4 mb-4"></div>'
                        );
                    }
                });

                $('#dtKategoriFilter').on('change', function() {
                    dtTable.column(5).search(this.value).draw();
                });
            }
        });

        function exportLaporan(tipe) {
            let jns = "{{ $jenis_laporan ?? '' }}";
            let startDate = "{{ $start_date ?? '' }}";
            let endDate = "{{ $end_date ?? '' }}";

            let katElem = document.getElementById('dtKategoriFilter');
            let kat = katElem ? katElem.value : "";

            if (!jns) {
                showDownloadToast("Peringatan", "Silakan tampilkan pratinjau laporan terlebih dahulu.",
                    "fa-exclamation-triangle", "text-yellow-400");
                return;
            }

            if (tipe === 'excel') {
                showDownloadToast("Laporan Excel", "File sedang diproses dan diunduh ke perangkat Anda.", "fa-spinner",
                    "text-emerald-400");

                let dt = $('#tabelLaporan').DataTable();
                let currentLength = dt.page.len();
                dt.page.len(-1).draw();

                let table = document.getElementById("tabelLaporan");
                let cloneTable = table.cloneNode(true);

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
                    worksheet: 'Laporan RPH',
                    table: cloneTable.innerHTML
                };

                let link = document.createElement("a");
                link.download = "Laporan_RPH_" + jns + ".xls";
                link.href = uri + base64(format(template, ctx));
                link.click();

                dt.page.len(currentLength).draw();

            } else {
                if (tipe === 'pdf') {
                    showDownloadToast("Laporan PDF", "Menyiapkan dokumen dan membuka di tab baru...", "fa-spinner",
                        "text-red-400");
                }

                let url = "{{ route('laporan.export') }}?jenis_laporan=" + jns + "&start_date=" + startDate + "&end_date=" + endDate + "&kategori=" + kat +
                    "&format=" + tipe;
                window.location.href = url;
            }
        }
    </script>
@endsection
