<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hewan;
use App\Models\Pembayaran;
use App\Models\Antemortem;
use App\Models\Pemotongan;
use App\Models\Postmortem;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

class LaporanController extends Controller
{
    private function getLaporanData($jenis_laporan, $start_date, $end_date, $kategori)
    {
        $dataLaporan = [];

        if (!empty($jenis_laporan)) {
            $dateField = 'created_at';
            
            switch ($jenis_laporan) {
                case 'hewan':
                    $query = Hewan::where('kategori', 'Hewan Harian');
                    $dateField = 'tanggal_masuk';
                    break;
                case 'pengawasan':
                    $query = Hewan::with(['antemortem', 'postmortem']);
                    $dateField = 'tanggal_masuk';
                    break;
                case 'idul_adha':
                    $query = \App\Models\PemotonganIdulAdha::query();
                    $dateField = 'tanggal';
                    break;
                case 'pembayaran':
                    $query = Pembayaran::with('hewan')->where('status_pembayaran', 'Lunas');
                    $dateField = 'created_at';
                    break;
                case 'antemortem':
                    $query = Antemortem::with('hewan');
                    $dateField = 'tanggal_periksa';
                    break;
                case 'pemotongan':
                    $query = Pemotongan::with('hewan');
                    $dateField = 'waktu_potong';
                    break;
                case 'postmortem':
                    $query = Postmortem::with('hewan');
                    $dateField = 'waktu_periksa';
                    break;
                default:
                    $query = collect([]);
            }

            if ($query instanceof \Illuminate\Database\Eloquent\Builder) {
                if (!empty($start_date) && !empty($end_date)) {
                    $query->whereBetween($dateField, [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
                } elseif (!empty($start_date)) {
                    $query->where($dateField, '>=', $start_date . ' 00:00:00');
                } elseif (!empty($end_date)) {
                    $query->where($dateField, '<=', $end_date . ' 23:59:59');
                }
                
                $dataLaporan = $query->latest($dateField)->get();
            }
        }

        return $dataLaporan;
    }

    public function index(Request $request)
    {
        $jenis_laporan = $request->input('jenis_laporan');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $kategori = $request->input('kategori', '');
        
        $dataLaporan = $this->getLaporanData($jenis_laporan, $start_date, $end_date, $kategori);

        return view('laporan.index', compact('jenis_laporan', 'start_date', 'end_date', 'kategori', 'dataLaporan'));
    }

    // FUNGSI BARU: Menyimpan Pengaturan Cetak ke Session & Server
    public function simpanPengaturanTtd(Request $request)
    {
        $request->validate([
            'nama_ttd'       => 'nullable|string|max:255',
            'nip_ttd'        => 'nullable|string|max:255',
            'pangkat_ttd'    => 'nullable|string|max:255',
            'orientasi_cetak'=> 'nullable|string|in:landscape,portrait',
            'gambar_ttd'     => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'gambar_stempel' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        if ($request->filled('nama_ttd')) session(['nama_ttd' => $request->nama_ttd]);
        if ($request->filled('nip_ttd')) session(['nip_ttd' => $request->nip_ttd]);
        if ($request->filled('pangkat_ttd')) session(['pangkat_ttd' => $request->pangkat_ttd]);
        if ($request->filled('orientasi_cetak')) session(['orientasi_cetak' => $request->orientasi_cetak]);

        // Buat folder uploads jika belum ada
        if (!File::exists(public_path('uploads'))) {
            File::makeDirectory(public_path('uploads'), 0755, true);
        }

        if ($request->hasFile('gambar_ttd')) {
            $ttdName = 'ttd_laporan.' . $request->gambar_ttd->extension();
            $request->gambar_ttd->move(public_path('uploads'), $ttdName);
            session(['gambar_ttd' => $ttdName]);
        }

        if ($request->hasFile('gambar_stempel')) {
            $stempelName = 'stempel_laporan.' . $request->gambar_stempel->extension();
            $request->gambar_stempel->move(public_path('uploads'), $stempelName);
            session(['gambar_stempel' => $stempelName]);
        }

        return redirect()->back()->with('success', 'Pengaturan cetak laporan berhasil disimpan!');
    }

    public function export(Request $request)
    {
        $jenis_laporan = $request->input('jenis_laporan');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $kategori = $request->input('kategori', '');
        $format = $request->input('format');

        $dataLaporan = $this->getLaporanData($jenis_laporan, $start_date, $end_date, $kategori);

        if (!empty($kategori) && in_array($jenis_laporan, ['antemortem', 'pemotongan', 'postmortem'])) {
            $dataLaporan = $dataLaporan->filter(function($item) use ($kategori) {
                return $item->hewan && $item->hewan->kategori === $kategori;
            });
        }

        $isDokter = in_array($jenis_laporan, ['antemortem', 'postmortem']);
        $jabatanTtd = $isDokter ? 'Dokter Hewan' : 'Kepala Bidang Peternakan dan Kesehatan Hewan';

        $namaTtd = session('nama_ttd', $isDokter ? 'drh. Nama Dokter' : 'Hasbir Jaya Razak, SP');
        $nipTtd = session('nip_ttd', $isDokter ? 'NIP Dokter' : '19690914 199803 2 005');
        $pangkatTtd = session('pangkat_ttd', $isDokter ? 'Dokter Hewan' : 'Pembina Utama Muda, Gol. IV/c');
        $orientasiCetak = session('orientasi_cetak', 'landscape');

        $qrText = "Naskah ini telah tertandatangan oleh:\n";
        $qrText .= "Nama: " . $namaTtd . "\n";
        $qrText .= "Jabatan: " . $jabatanTtd . "\n";
        $qrText .= "Unit Kerja: Dinas Perkebunan dan Peternakan\n";
        $qrText .= "Instansi: Pemerintah Kabupaten Kolaka\n";
        $qrText .= "Ditandatangani pada: " . \Carbon\Carbon::now()->translatedFormat('d F Y H:i:s');
        
        $qrCode = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->margin(1)->size(100)->errorCorrection('H')->generate($qrText));

        $data = [
            'jenis_laporan'  => $jenis_laporan,
            'start_date'     => $start_date,
            'end_date'       => $end_date,
            'kategori'       => $kategori,
            'dataLaporan'    => $dataLaporan,
            'nama_ttd'       => $namaTtd,
            'nip_ttd'        => $nipTtd,
            'pangkat_ttd'    => $pangkatTtd,
            'jabatan_ttd'    => $jabatanTtd,
            'orientasi'      => $orientasiCetak,
            'gambar_ttd'     => session('gambar_ttd'),
            'gambar_stempel' => session('gambar_stempel'),
            'qrCode'         => 'data:image/svg+xml;base64,' . $qrCode,
        ];

        if ($format == 'pdf') {
            ini_set('max_execution_time', 300);
            ini_set('memory_limit', '2048M');
            $pdf = Pdf::loadView('laporan.cetak', $data)->setPaper('A4', $orientasiCetak);
            return $pdf->download('Laporan_RPH_'.$jenis_laporan.'.pdf');
        } elseif ($format == 'word') {
            $headers = [
                "Content-type" => "application/vnd.ms-word",
                "Content-Disposition" => "attachment;Filename=Laporan_RPH_".$jenis_laporan.".doc"
            ];
            return response()->make(view('laporan.cetak', $data), 200, $headers);
        }

        return redirect()->back();
    }
}