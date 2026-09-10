<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemotongan;
use App\Models\Antemortem;
use App\Models\Postmortem;
use App\Models\Hewan;
use App\Models\Pembayaran;
use Carbon\Carbon;

class PemotonganController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->input('periode', 'harian');
        
        $queryDipotong = Pemotongan::where('status_pemotongan', 'Selesai Dipotong');
        $queryLolos = Antemortem::where('status_antemortem', 'Lolos');
        $queryKarantina = Antemortem::where('status_antemortem', 'Karantina');

        $teksPeriode = 'Hari Ini';
        $today = Carbon::today();

        if ($periode == 'harian') {
            $teksPeriode = 'Hari Ini';
            $queryDipotong->whereDate('waktu_potong', $today);
            $queryLolos->whereDate('tanggal_periksa', $today);
            $queryKarantina->whereDate('tanggal_periksa', $today);
        } elseif ($periode == 'bulanan') {
            $teksPeriode = 'Bulan Ini';
            $queryDipotong->whereMonth('waktu_potong', $today->month)->whereYear('waktu_potong', $today->year);
            $queryLolos->whereMonth('tanggal_periksa', $today->month)->whereYear('tanggal_periksa', $today->year);
            $queryKarantina->whereMonth('tanggal_periksa', $today->month)->whereYear('tanggal_periksa', $today->year);
        } elseif ($periode == 'tahunan') {
            $teksPeriode = 'Tahun Ini';
            $queryDipotong->whereYear('waktu_potong', $today->year);
            $queryLolos->whereYear('tanggal_periksa', $today->year);
            $queryKarantina->whereYear('tanggal_periksa', $today->year);
        } else {
            $teksPeriode = 'Semua Waktu';
        }

        $jumlahDipotong = $queryDipotong->count();
        $jumlahLolos = $queryLolos->count();
        $jumlahKarantina = $queryKarantina->count();

        // Ambil SEMUA data hewan harian beserta relasi pemotongannya
        // Jika butuh filter tabel, bisa ditambah di sini. Untuk sekarang ikuti yang ada:
        $hewans = Hewan::where('kategori', 'Hewan Harian')
                       ->with(['pemotongan', 'pembayaran', 'antemortem'])
                       ->latest()
                       ->get();
        
        return view('pemotongan.index', compact(
            'hewans', 
            'periode', 
            'teksPeriode', 
            'jumlahDipotong', 
            'jumlahLolos', 
            'jumlahKarantina'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_hewan'          => 'required|exists:hewans,id_hewan',
            'tanggal_potong'    => 'required|date',
            'status_pemotongan' => 'required|in:Menunggu Dipotong,Selesai Dipotong',
            'berat_karkas'      => 'required|numeric',
            'berat_jeroan'      => 'nullable|numeric',
        ]);

        $cekLunas = Pembayaran::where('id_hewan', $request->id_hewan)
                              ->where('status_pembayaran', 'Lunas')
                              ->exists();

        $cekAM = Antemortem::where('id_hewan', $request->id_hewan)
                           ->where('status_antemortem', 'Lolos')
                           ->exists();

        if (!$cekLunas || !$cekAM) {
            return redirect()->back()->withErrors(['Persyaratan' => 'Tidak dapat menyimpan data! Hewan ini belum LUNAS atau belum LOLOS ANTEMORTEM. Selesaikan administrasinya terlebih dahulu.']);
        }

        $jam = $request->jam_potong ? $request->jam_potong : '00:00';
        $waktuGabungan = $request->tanggal_potong . ' ' . $jam;

        Pemotongan::create([
            'id_hewan'          => $request->id_hewan,
            'waktu_potong'      => $waktuGabungan,
            'status_pemotongan' => $request->status_pemotongan,
            'berat_karkas'      => $request->berat_karkas,
            'berat_jeroan'      => $request->berat_jeroan,
        ]);

        $hewan = Hewan::find($request->id_hewan);
        if ($request->status_pemotongan == 'Selesai Dipotong') {
            $hewan->update(['status' => 'Selesai Dipotong']);
        } else {
            $hewan->update(['status' => 'Menunggu Dipotong']);
        }

        return redirect()->back()->with('success', 'Data antrean potong berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_potong'    => 'required|date',
            'status_pemotongan' => 'required|in:Menunggu Dipotong,Selesai Dipotong',
            'berat_karkas'      => 'required|numeric',
            'berat_jeroan'      => 'nullable|numeric',
        ]);

        $jam = $request->jam_potong ? $request->jam_potong : '00:00';
        $waktuGabungan = $request->tanggal_potong . ' ' . $jam;

        $pemotongan = Pemotongan::findOrFail($id);
        $pemotongan->update([
            'waktu_potong'      => $waktuGabungan,
            'status_pemotongan' => $request->status_pemotongan,
            'berat_karkas'      => $request->berat_karkas,
            'berat_jeroan'      => $request->berat_jeroan,
        ]);

        if($pemotongan->hewan) {
            if ($request->status_pemotongan == 'Selesai Dipotong') {
                $pemotongan->hewan->update(['status' => 'Selesai Dipotong']);
            } else {
                $pemotongan->hewan->update(['status' => 'Menunggu Dipotong']);
            }
        }

        return redirect()->back()->with('success', 'Data hasil potong berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pemotongan = Pemotongan::findOrFail($id);
        $id_hewan = $pemotongan->id_hewan;
        
        // Hapus data beruntun di proses lain
        Postmortem::where('id_hewan', $id_hewan)->delete();

        // Kembalikan status hewan ke status antemortem
        if($pemotongan->hewan) {
            $am = Antemortem::where('id_hewan', $id_hewan)->first();
            $status = $am ? $am->status_antemortem : 'Menunggu Antemortem';
            $pemotongan->hewan->update(['status' => $status]);
        }
        
        $pemotongan->delete();

        return redirect()->back()->with('success', 'Data hasil pemotongan dan seluruh riwayat proses RPH terkait berhasil dihapus!');
    }
}