<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Antemortem;
use App\Models\Pemotongan;
use App\Models\Postmortem;
use App\Models\Hewan;

class AntemortemController extends Controller
{
    public function index()
    {
        // Ambil SEMUA data hewan harian beserta relasi antemortemnya
        $hewans = Hewan::where('kategori', 'Hewan Harian')
                       ->with('antemortem')
                       ->latest()
                       ->get();
        
        return view('antemortem.index', compact('hewans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_hewan'          => 'required|exists:hewans,id_hewan|unique:antemortems,id_hewan',
            'tanggal_periksa'   => 'required|date',
            'status_antemortem' => 'required|in:Lolos,Ditolak,Menunggu,Karantina',
            'kondisi_fisik'     => 'required|string',
            'tanda_penyakit'    => 'nullable|string',
            'catatan'           => 'nullable|string',
        ], [
            'id_hewan.unique' => 'Gagal! Hewan ini sudah memiliki data pemeriksaan antemortem sebelumnya.'
        ]);

        Antemortem::create([
            'id_hewan'          => $request->id_hewan,
            'tanggal_periksa'   => $request->tanggal_periksa,
            'kondisi_fisik'     => $request->kondisi_fisik,
            'tanda_penyakit'    => $request->tanda_penyakit ?? 'Tidak Ada',
            'status_antemortem' => $request->status_antemortem,
            'catatan'           => $request->catatan,
        ]);

        $hewan = Hewan::find($request->id_hewan);
        if($request->status_antemortem == 'Lolos') {
            $hewan->update(['status' => 'Lolos Antemortem (Siap Potong)']);
        } elseif ($request->status_antemortem == 'Karantina') {
            $hewan->update(['status' => 'Karantina Sementara']);
        } elseif ($request->status_antemortem == 'Ditolak') {
            $hewan->update(['status' => 'Ditolak (Sakit/Tidak Layak)']);
        } else {
            $hewan->update(['status' => 'Menunggu Antemortem']);
        }

        return redirect()->back()->with('success', 'Pemeriksaan antemortem dan status berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_periksa'   => 'required|date',
            'status_antemortem' => 'required|in:Lolos,Ditolak,Menunggu,Karantina',
            'kondisi_fisik'     => 'required|string',
            'tanda_penyakit'    => 'nullable|string',
            'catatan'           => 'nullable|string',
        ]);

        $antemortem = Antemortem::findOrFail($id);

        if ($request->status_antemortem == 'Ditolak' && \App\Models\Pemotongan::where('id_hewan', $antemortem->id_hewan)->exists()) {
            return redirect()->back()->withErrors(['Error' => 'Hewan sudah diproses pemotongan, status tidak bisa diubah menjadi Ditolak.']);
        }

        $antemortem->update([
            'tanggal_periksa'   => $request->tanggal_periksa,
            'kondisi_fisik'     => $request->kondisi_fisik,
            'tanda_penyakit'    => $request->tanda_penyakit ?? 'Tidak Ada',
            'status_antemortem' => $request->status_antemortem,
            'catatan'           => $request->catatan,
        ]);

        if($antemortem->hewan) {
            if($request->status_antemortem == 'Lolos') {
                $antemortem->hewan->update(['status' => 'Lolos Antemortem (Siap Potong)']);
            } elseif ($request->status_antemortem == 'Karantina') {
                $antemortem->hewan->update(['status' => 'Karantina Sementara']);
            } elseif ($request->status_antemortem == 'Ditolak') {
                $antemortem->hewan->update(['status' => 'Ditolak (Sakit/Tidak Layak)']);
            } else {
                $antemortem->hewan->update(['status' => 'Menunggu Antemortem']);
            }
        }

        return redirect()->back()->with('success', 'Data antemortem dan status berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $antemortem = Antemortem::findOrFail($id);
        $id_hewan = $antemortem->id_hewan;
        
        if (\App\Models\Pemotongan::where('id_hewan', $id_hewan)->exists()) {
            return redirect()->back()->withErrors(['Error' => 'Tidak dapat menghapus data Antemortem karena hewan sudah diproses Pemotongan. Hapus data Pemotongan terlebih dahulu!']);
        }

        // Kembalikan status hewan menjadi menunggu ke tahap paling awal
        if($antemortem->hewan) {
            $antemortem->hewan->update(['status' => 'Menunggu Antemortem']);
        }
        
        $antemortem->delete();

        return redirect()->back()->with('success', 'Data pemeriksaan antemortem dan seluruh riwayat proses RPH terkait berhasil dihapus!');
    }
}