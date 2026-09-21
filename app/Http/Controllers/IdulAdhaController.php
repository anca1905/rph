<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PemotonganIdulAdha;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PemotonganIdulAdhaImport;

class IdulAdhaController extends Controller
{
    public function index()
    {
        $idul_adha = PemotonganIdulAdha::orderBy('tanggal', 'desc')->get();
        return view('idul_adha.index', compact('idul_adha'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama_lokasi' => 'required|string|max:255',
            'jenis_lokasi' => 'required|string',
            'alamat' => 'required|string',
            'propinsi' => 'required|string',
            'kabupaten' => 'required|string',
            'kecamatan' => 'required|string',
            'desa' => 'required|string',
            'jenis_hewan' => 'required|string',
            'jumlah' => 'required|integer|min:1',
            'upt' => 'required|string',
            'pelapor' => 'required|string',
        ]);

        PemotonganIdulAdha::create($request->all());

        return redirect()->back()->with('success', 'Data Pemotongan Idul Adha berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama_lokasi' => 'required|string|max:255',
            'jenis_lokasi' => 'required|string',
            'alamat' => 'required|string',
            'propinsi' => 'required|string',
            'kabupaten' => 'required|string',
            'kecamatan' => 'required|string',
            'desa' => 'required|string',
            'jenis_hewan' => 'required|string',
            'jumlah' => 'required|integer|min:1',
            'upt' => 'required|string',
            'pelapor' => 'required|string',
        ]);

        $data = PemotonganIdulAdha::findOrFail($id);
        $data->update($request->all());

        return redirect()->back()->with('success', 'Data Pemotongan Idul Adha berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $data = PemotonganIdulAdha::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('success', 'Data Pemotongan Idul Adha berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_import' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new PemotonganIdulAdhaImport, $request->file('file_import'));
            return redirect()->back()->with('success', 'Data berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        $dataLaporan = PemotonganIdulAdha::orderBy('tanggal', 'desc')->get();

        $namaTtd = session('nama_ttd', 'Dr. drh. KASMAWATI, MM');
        $nipTtd = session('nip_ttd', '19771202 200604 2 005');

        $qrText = "Naskah ini telah tertandatangan oleh:\n";
        $qrText .= "Nama: " . $namaTtd . "\n";
        $qrText .= "Jabatan: Kepala Bidang Peternakan\n";
        $qrText .= "Unit Kerja: Dinas Perkebunan dan Peternakan\n";
        $qrText .= "Instansi: Pemerintah Kabupaten Kolaka\n";
        $qrText .= "Ditandatangani pada: " . \Carbon\Carbon::now()->translatedFormat('d F Y H:i:s');
        
        try {
            $qrCode = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->margin(1)->size(400)->errorCorrection('H')->generate($qrText));
            $qrDataUri = 'data:image/png;base64,' . $qrCode;
        } catch (\Exception $e) {
            $qrCode = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->margin(1)->size(400)->errorCorrection('H')->generate($qrText));
            $qrDataUri = 'data:image/svg+xml;base64,' . $qrCode;
        }

        $pangkatTtd = session('pangkat_ttd', 'Pembina TK.I Gol. IV/b');
        $orientasiCetak = session('orientasi_cetak', 'landscape');

        $data = [
            'dataLaporan'    => $dataLaporan,
            'nama_ttd'       => $namaTtd,
            'nip_ttd'        => $nipTtd,
            'pangkat_ttd'    => $pangkatTtd,
            'jabatan_ttd'    => 'Kepala Bidang Peternakan',
            'orientasi'      => $orientasiCetak,
            'gambar_ttd'     => session('gambar_ttd'),
            'gambar_stempel' => session('gambar_stempel'),
            'qrCode'         => $qrDataUri,
        ];

        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '2048M');
        $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('idul_adha.cetak_pdf', $data)
                    ->setPaper('a4')
                    ->setOrientation($orientasiCetak);
        return $pdf->download('Laporan_Pemotongan_Idul_Adha.pdf');
    }
}