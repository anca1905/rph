@extends('layouts.app')

@section('title', 'Dashboard Pekerja Idul Adha')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-6 shadow-sm text-white relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-emerald-100 text-sm font-semibold mb-1">Total Laporan Idul Adha</p>
                <h3 class="text-4xl font-bold">{{ number_format($totalLaporan, 0, ',', '.') }} <span class="text-lg font-medium">Data</span></h3>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-20">
                <i class="fas fa-file-alt text-8xl"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-teal-500 to-teal-700 rounded-2xl p-6 shadow-sm text-white relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-teal-100 text-sm font-semibold mb-1">Laporan Hari Ini</p>
                <h3 class="text-4xl font-bold">{{ number_format($laporanHariIni, 0, ',', '.') }} <span class="text-lg font-medium">Data</span></h3>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-20">
                <i class="fas fa-calendar-day text-8xl"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl p-6 shadow-sm text-white relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-blue-100 text-sm font-semibold mb-1">Total Hewan Terpotong</p>
                <h3 class="text-4xl font-bold">{{ number_format($totalHewanIdulAdha, 0, ',', '.') }} <span class="text-lg font-medium">Ekor</span></h3>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-20">
                <i class="fas fa-cow text-8xl"></i>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        <div class="lg:col-span-1 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <h3 class="text-sm font-bold text-slate-800 mb-4">Komposisi Hewan Idul Adha</h3>
            <div id="chartKomposisi" class="w-full flex justify-center items-center h-72"></div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <h3 class="text-sm font-bold text-slate-800 mb-4">Laporan Terbaru</h3>
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 border-b border-slate-100 text-center">No</th>
                            <th class="px-4 py-3 border-b border-slate-100">Tanggal</th>
                            <th class="px-4 py-3 border-b border-slate-100">Lokasi / Instansi</th>
                            <th class="px-4 py-3 border-b border-slate-100">Kec. / Desa</th>
                            <th class="px-4 py-3 border-b border-slate-100">Jenis Hewan</th>
                            <th class="px-4 py-3 border-b border-slate-100 text-center">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-600 divide-y divide-slate-50">
                        @forelse ($laporanTerbaru as $item)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-2 text-center">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2 font-semibold text-slate-800">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                <td class="px-4 py-2 font-bold text-emerald-600">{{ $item->nama_lokasi }}</td>
                                <td class="px-4 py-2">
                                    <div class="font-semibold">{{ $item->kecamatan }}</div>
                                    <div class="text-xs text-slate-500">{{ $item->desa }}</div>
                                </td>
                                <td class="px-4 py-2">{{ $item->jenis_hewan }}</td>
                                <td class="px-4 py-2 text-center font-bold">{{ $item->jumlah }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-slate-400 font-medium">Belum ada laporan terbaru</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-right">
                <a href="{{ route('idul_adha.index') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">Lihat Semua Data <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var labelKomposisi = @json($labelKomposisiIA);
        var dataKomposisi = @json($dataKomposisiIA);

        if(dataKomposisi.length > 0) {
            var optionsKomposisi = {
                series: dataKomposisi,
                chart: {
                    type: 'donut',
                    height: 300,
                    fontFamily: 'Inter, sans-serif'
                },
                labels: labelKomposisi,
                colors: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                name: {
                                    fontSize: '12px',
                                    color: '#64748b'
                                },
                                value: {
                                    fontSize: '24px',
                                    fontWeight: 700,
                                    color: '#1e293b'
                                },
                                total: {
                                    show: true,
                                    showAlways: true,
                                    label: 'Total Hewan',
                                    fontSize: '12px',
                                    color: '#64748b',
                                    formatter: function(w) {
                                        return w.globals.seriesTotals.reduce((a, b) => {
                                            return a + b
                                        }, 0)
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    position: 'bottom',
                    fontSize: '12px',
                    markers: {
                        radius: 12
                    }
                },
                stroke: {
                    show: true,
                    colors: ['transparent']
                }
            };
            var chartKomposisi = new ApexCharts(document.querySelector("#chartKomposisi"), optionsKomposisi);
            chartKomposisi.render();
        } else {
            document.querySelector("#chartKomposisi").innerHTML = '<div class="text-sm text-slate-400 font-medium">Belum ada data hewan.</div>';
        }
    });
</script>
@endsection
