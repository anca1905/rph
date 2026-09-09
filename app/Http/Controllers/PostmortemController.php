<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Postmortem;
use App\Models\Antemortem;
use App\Models\Pemotongan;
use App\Models\Hewan;

class PostmortemController extends Controller
{
    public function index()
    {
        // Ambil SEMUA data hewan harian beserta relasi postmortemnya
        $hewans = Hewan::where('kategori', 'Hewan Harian')
                       ->with(['postmortem', 'pemotongan'])
                       ->latest()
                       ->get();
        
        return view('postmortem.index', compact('hewans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_hewan'       => 'required|exists:hewans,id_hewan',
            'tanggal_periksa'=> 'required|date',
            'kondisi_karkas' => 'required|in:ASUH (Aman Sehat Utuh Halal),Bersyarat,Ditolak',
            'kondisi_jeroan' => 'required|in:ASUH (Aman Sehat Utuh Halal),Bersyarat,Ditolak',
            'limpa'          => 'required|string',
            'hati'           => 'required|string',
            'daging'         => 'required|string',
            'paru_paru'      => 'required|string',
            'catatan_medis'  => 'nullable|string',
        ]);

        $cekPotong = Pemotongan::where('id_hewan', $request->id_hewan)
                               ->where('status_pemotongan', 'Selesai Dipotong')
                               ->exists();

        if (!$cekPotong) {
            return redirect()->back()->withErrors(['Persyaratan' => 'Tidak dapat menyimpan data! Hewan ini belum diproses atau belum berstatus SELESAI DIPOTONG.']);
        }

        $jam = $request->jam_periksa ? $request->jam_periksa : '00:00';
        $waktuGabungan = $request->tanggal_periksa . ' ' . $jam;

        Postmortem::create([
            'id_hewan'       => $request->id_hewan,
            'waktu_periksa'  => $waktuGabungan,
            'kondisi_karkas' => $request->kondisi_karkas,
            'kondisi_jeroan' => $request->kondisi_jeroan,
            'limpa'          => $request->limpa,
            'hati'           => $request->hati,
            'daging'         => $request->daging,
            'paru_paru'      => $request->paru_paru,
            'catatan_medis'  => $request->catatan_medis,
        ]);

        $k = $request->kondisi_karkas;
        $j = $request->kondisi_jeroan;
        $statusGlobal = 'Selesai Postmortem';

        if ($k == 'Ditolak' && $j == 'Ditolak') {
            $statusGlobal = 'Ditolak Seluruhnya';
        } elseif ($k == 'Bersyarat' && $j == 'Bersyarat') {
            $statusGlobal = 'Selesai Postmortem (Bersyarat Seluruhnya)';
        } elseif ($k == 'Ditolak' && $j == 'Bersyarat') {
            $statusGlobal = 'Selesai Postmortem (Karkas Ditolak, Jeroan Bersyarat)';
        } elseif ($k == 'Bersyarat' && $j == 'Ditolak') {
            $statusGlobal = 'Selesai Postmortem (Karkas Bersyarat, Jeroan Ditolak)';
        } elseif ($k == 'Ditolak') {
            $statusGlobal = 'Selesai Postmortem (Karkas Ditolak)';
        } elseif ($j == 'Ditolak') {
            $statusGlobal = 'Selesai Postmortem (Jeroan Ditolak)';
        } elseif ($k == 'Bersyarat') {
            $statusGlobal = 'Selesai Postmortem (Karkas Bersyarat)';
        } elseif ($j == 'Bersyarat') {
            $statusGlobal = 'Selesai Postmortem (Jeroan Bersyarat)';
        } else {
            $statusGlobal = 'Selesai Postmortem';
        }

        $hewan = Hewan::find($request->id_hewan);
        $hewan->update(['status' => $statusGlobal]);

        return redirect()->back()->with('success', 'Data hasil postmortem berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_periksa'=> 'required|date',
            'kondisi_karkas' => 'required|in:ASUH (Aman Sehat Utuh Halal),Bersyarat,Ditolak',
            'kondisi_jeroan' => 'required|in:ASUH (Aman Sehat Utuh Halal),Bersyarat,Ditolak',
            'limpa'          => 'required|string',
            'hati'           => 'required|string',
            'daging'         => 'required|string',
            'paru_paru'      => 'required|string',
            'catatan_medis'  => 'nullable|string',
        ]);

        $jam = $request->jam_periksa ? $request->jam_periksa : '00:00';
        $waktuGabungan = $request->tanggal_periksa . ' ' . $jam;

        $postmortem = Postmortem::findOrFail($id);
        $postmortem->update([
            'waktu_periksa'  => $waktuGabungan,
            'kondisi_karkas' => $request->kondisi_karkas,
            'kondisi_jeroan' => $request->kondisi_jeroan,
            'limpa'          => $request->limpa,
            'hati'           => $request->hati,
            'daging'         => $request->daging,
            'paru_paru'      => $request->paru_paru,
            'catatan_medis'  => $request->catatan_medis,
        ]);

        if($postmortem->hewan) {
            $k = $request->kondisi_karkas;
            $j = $request->kondisi_jeroan;
            $statusGlobal = 'Selesai Postmortem';

            if ($k == 'Ditolak' && $j == 'Ditolak') {
                $statusGlobal = 'Ditolak Seluruhnya';
            } elseif ($k == 'Bersyarat' && $j == 'Bersyarat') {
                $statusGlobal = 'Selesai Postmortem (Bersyarat Seluruhnya)';
            } elseif ($k == 'Ditolak' && $j == 'Bersyarat') {
                $statusGlobal = 'Selesai Postmortem (Karkas Ditolak, Jeroan Bersyarat)';
            } elseif ($k == 'Bersyarat' && $j == 'Ditolak') {
                $statusGlobal = 'Selesai Postmortem (Karkas Bersyarat, Jeroan Ditolak)';
            } elseif ($k == 'Ditolak') {
                $statusGlobal = 'Selesai Postmortem (Karkas Ditolak)';
            } elseif ($j == 'Ditolak') {
                $statusGlobal = 'Selesai Postmortem (Jeroan Ditolak)';
            } elseif ($k == 'Bersyarat') {
                $statusGlobal = 'Selesai Postmortem (Karkas Bersyarat)';
            } elseif ($j == 'Bersyarat') {
                $statusGlobal = 'Selesai Postmortem (Jeroan Bersyarat)';
            } else {
                $statusGlobal = 'Selesai Postmortem';
            }
            
            $postmortem->hewan->update(['status' => $statusGlobal]);
        }

        return redirect()->back()->with('success', 'Data hasil postmortem berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $postmortem = Postmortem::findOrFail($id);
        $id_hewan = $postmortem->id_hewan;
        
        // Tidak perlu hapus antemortem atau pemotongan, karena postmortem ada di akhir rantai

        // Kembalikan status hewan menjadi selesai dipotong
        if($postmortem->hewan) {
            $postmortem->hewan->update(['status' => 'Selesai Dipotong']);
        }
        
        $postmortem->delete();

        return redirect()->back()->with('success', 'Data pemeriksaan postmortem dan seluruh riwayat proses RPH terkait berhasil dihapus!');
    }
}