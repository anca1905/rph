<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hewan;
use App\Models\Pemotongan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        if (auth()->check() && auth()->user()->role === 'pekerja_idul_adha') {
            $totalLaporan = \App\Models\PemotonganIdulAdha::count();
            $laporanHariIni = \App\Models\PemotonganIdulAdha::whereDate('tanggal', $now->today())->count();
            $totalHewanIdulAdha = \App\Models\PemotonganIdulAdha::sum('jumlah');
            
            // Komposisi Hewan Idul Adha
            $komposisiIdulAdha = \App\Models\PemotonganIdulAdha::selectRaw('jenis_hewan, SUM(jumlah) as total')
                ->groupBy('jenis_hewan')
                ->pluck('total', 'jenis_hewan');
            
            $labelKomposisiIA = $komposisiIdulAdha->keys()->toArray();
            $dataKomposisiIA = $komposisiIdulAdha->values()->toArray();

            $laporanTerbaru = \App\Models\PemotonganIdulAdha::latest('created_at')->take(5)->get();

            return view('dashboard.pekerja', compact('totalLaporan', 'laporanHariIni', 'totalHewanIdulAdha', 'labelKomposisiIA', 'dataKomposisiIA', 'laporanTerbaru'));
        }

        // Lanjut ke dashboard utama (Admin, Petugas, Pimpinan)

        // 1. KARTU: Total Hewan Masuk (Seluruh data registrasi hewan)
        $totalHewan = Hewan::count();

        // 2. KARTU: Data Hewan Ditolak (Mencari status yang mengandung kata 'Ditolak')
        $hewanDitolak = Hewan::where('status', 'like', '%Ditolak%')->count();

        // 3. KARTU: Data Pemotongan Harian (Hari ini saja)
        $pemotonganHarian = Pemotongan::whereDate('waktu_potong', $now->today())->count();

        // 4. GRAFIK: Tren Pemotongan Bulanan (Untuk Tahun Ini)
        // Kita siapkan array 12 bulan dengan nilai awal 0
        $trenBulanan = array_fill(1, 12, 0);
        $dataTren = Pemotongan::selectRaw('MONTH(waktu_potong) as bulan, COUNT(*) as total')
            ->whereYear('waktu_potong', $now->year)
            ->groupBy('bulan')
            ->pluck('total', 'bulan');
        
        foreach ($dataTren as $bulan => $total) {
            $trenBulanan[$bulan] = $total;
        }
        $trenBulananData = array_values($trenBulanan); // Hanya mengambil angkanya saja untuk grafik

        // 5. GRAFIK: Komposisi Hewan (Berdasarkan Jenis Hewan yang ada di database)
        $komposisiHewan = Hewan::selectRaw('jenis_hewan, COUNT(*) as total')
            ->groupBy('jenis_hewan')
            ->pluck('total', 'jenis_hewan');
        
        $labelKomposisi = $komposisiHewan->keys()->toArray(); // Array Nama Jenis Hewan (Sapi, Kambing, dll)
        $dataKomposisi = $komposisiHewan->values()->toArray(); // Array Jumlahnya

        // 6. TABEL: Aktivitas Pemotongan Terakhir (Khusus Hari Ini)
        $aktivitasHarian = Pemotongan::with('hewan')
            ->whereDate('waktu_potong', $now->today())
            ->latest('waktu_potong')
            ->get();

        return view('dashboard.dashboard', compact(
            'totalHewan', 
            'hewanDitolak', 
            'pemotonganHarian', 
            'trenBulananData', 
            'labelKomposisi', 
            'dataKomposisi', 
            'aktivitasHarian'
        ));
    }
}