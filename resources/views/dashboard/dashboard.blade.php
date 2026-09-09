@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

        <div
            class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-6 shadow-sm text-white relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-emerald-100 text-sm font-semibold mb-1">Total Hewan Masuk</p>
                <h3 class="text-4xl font-bold">{{ number_format($totalHewan, 0, ',', '.') }} <span
                        class="text-lg font-medium">Ekor</span></h3>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-20">
                <i class="fas fa-box-open text-8xl"></i>
            </div>
        </div>

        <div
            class="bg-gradient-to-br from-red-500 to-red-700 rounded-2xl p-6 shadow-sm text-white relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-red-100 text-sm font-semibold mb-1">Data Hewan Ditolak</p>
                <h3 class="text-4xl font-bold">{{ number_format($hewanDitolak, 0, ',', '.') }} <span
                        class="text-lg font-medium">Ekor</span></h3>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-20">
                <i class="fas fa-ban text-8xl"></i>
            </div>
        </div>

        <div
            class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl p-6 shadow-sm text-white relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-blue-100 text-sm font-semibold mb-1">Pemotongan Harian (Hari Ini)</p>
                <h3 class="text-4xl font-bold">{{ number_format($pemotonganHarian, 0, ',', '.') }} <span
                        class="text-lg font-medium">Ekor</span></h3>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-20">
                <i class="fas fa-cut text-8xl"></i>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <h3 class="text-sm font-bold text-slate-800 mb-4">Tren Pemotongan Hewan (Tahun {{ date('Y') }})</h3>
            <div id="chartTrenBulanan" class="w-full h-72"></div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <h3 class="text-sm font-bold text-slate-800 mb-4">Komposisi Jenis Hewan Masuk</h3>
            <div id="chartKomposisi" class="w-full flex justify-center items-center h-72"></div>
        </div>

    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 mb-8">
        <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-sm font-bold text-slate-800">Aktivitas Pemotongan Terakhir (Hari Ini)</h2>
                <p class="text-xs text-slate-500 mt-1">Data pemotongan harian tanggal:
                    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            </div>
        </div>

        <div class="w-full">
            <table id="tabelAktivitas" class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-4 border-b border-slate-100 text-center">No</th>
                        <th class="px-4 py-4 border-b border-slate-100">No Registrasi</th>
                        <th class="px-4 py-4 border-b border-slate-100">Nama Pemilik</th>
                        <th class="px-4 py-4 border-b border-slate-100">Asal Hewan</th>
                        <th class="px-4 py-4 border-b border-slate-100">Jenis Hewan</th>
                        <th class="px-4 py-4 border-b border-slate-100">Kategori</th>
                        <th class="px-4 py-4 border-b border-slate-100">Waktu Potong</th>
                        <th class="px-4 py-4 border-b border-slate-100">Karkas (Kg)</th>
                        <th class="px-4 py-4 border-b border-slate-100">Jeroan (Kg)</th>
                        <th class="px-4 py-4 border-b border-slate-100">Status</th>
                    </tr>
                </thead>
                <tbody class="text-slate-600 divide-y divide-slate-50">
                    @forelse ($aktivitasHarian as $row)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 text-center">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-bold">{{ $row->hewan->no_registrasi ?? '-' }}</td>
                            <td class="px-4 py-3 font-medium">{{ $row->hewan->nama_pemilik ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $row->hewan->asal_hewan ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $row->hewan->jenis_hewan ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $row->hewan->kategori ?? '-' }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($row->waktu_potong)->format('H:i') }} WITA</td>
                            <td class="px-4 py-3 font-bold text-emerald-600">{{ $row->berat_karkas }} Kg</td>
                            <td class="px-4 py-3 font-bold">{{ $row->berat_jeroan ?? '-' }} Kg</td>
                            <td class="px-4 py-3 font-bold">
                                <span
                                    class="bg-blue-50 text-blue-600 px-2 py-1 rounded text-[10px] uppercase tracking-wider">{{ $row->status_pemotongan }}</span>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        $(document).ready(function() {
            // 1. INISIALISASI DATATABLES
            if ($('#tabelAktivitas').length > 0) {
                $('#tabelAktivitas').DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json",
                        "emptyTable": "Belum ada aktivitas pemotongan untuk hari ini.",
                        "search": "Cari Aktivitas:",
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
            }

            // 2. INISIALISASI GRAFIK TREN BULANAN (BAR CHART)
            var optionsTren = {
                series: [{
                    name: "Total Potong",
                    data: @json($trenBulananData) // Menerima Array Data [Jan, Feb, ...] dari Controller
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Inter, sans-serif'
                },
                colors: ['#10b981'], // Warna Emerald-500
                dataLabels: {
                    enabled: true
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '50%',
                    }
                },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov',
                        'Des'
                    ],
                    labels: {
                        style: {
                            colors: '#64748b'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#64748b'
                        }
                    }
                }
            };
            var chartTren = new ApexCharts(document.querySelector("#chartTrenBulanan"), optionsTren);
            chartTren.render();

            // 3. INISIALISASI GRAFIK KOMPOSISI JENIS HEWAN (DONUT CHART)
            var labelKomposisi = @json($labelKomposisi);
            var dataKomposisi = @json($dataKomposisi);

            // Cegah error ApexCharts jika database benar-benar kosong
            if (dataKomposisi.length === 0) {
                labelKomposisi = ['Belum Ada Data'];
                dataKomposisi = [
                    1
                ]; // Nilai *dummy* agar lingkaran tetap tergambar (namun opsinya disembunyikan/dijadikan abu-abu jika diatur lebih lanjut)
            }

            var optionsKomposisi = {
                series: [{
                    name: "Total",
                    data: dataKomposisi
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '50%',
                        distributed: true
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val + " Ekor"
                    }
                },
                legend: {
                    show: false
                },
                xaxis: {
                    categories: labelKomposisi,
                    labels: {
                        style: { colors: '#64748b' }
                    }
                },
                yaxis: {
                    labels: {
                        style: { colors: '#64748b' }
                    }
                },
                // Variasi warna untuk setiap jenis hewan: Sapi, Kambing, Kerbau, dll
                colors: ['#0ea5e9', '#f59e0b', '#8b5cf6', '#10b981', '#f43f5e', '#64748b']
            };
            var chartKomposisi = new ApexCharts(document.querySelector("#chartKomposisi"), optionsKomposisi);
            chartKomposisi.render();

        });
    </script>
@endsection
