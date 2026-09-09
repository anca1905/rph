<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Hewan;

class PembayaranController extends Controller
{
    public function index()
    {
        // Ambil SEMUA data hewan harian beserta relasi pembayarannya
        $hewans = Hewan::where('kategori', 'Hewan Harian')
                       ->with('pembayaran')
                       ->latest()
                       ->get();
        
        return view('pembayaran.index', compact('hewans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_hewan'          => 'required|exists:hewans,id_hewan',
            'total_pembayaran'  => 'required|numeric',
            'status_pembayaran' => 'required|in:Lunas,Belum Lunas',
        ]);

        Pembayaran::create($request->all());
        return redirect()->back()->with('success', 'Tagihan pembayaran berhasil dibuat!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_hewan'          => 'required|exists:hewans,id_hewan',
            'total_pembayaran'  => 'required|numeric',
            'status_pembayaran' => 'required|in:Lunas,Belum Lunas',
        ]);

        $pembayaran = Pembayaran::findOrFail($id);

        if ($request->status_pembayaran == 'Belum Lunas' && \App\Models\Pemotongan::where('id_hewan', $pembayaran->id_hewan)->exists()) {
            return redirect()->back()->withErrors(['Error' => 'Hewan sudah diproses pemotongan, status pembayaran tidak bisa diubah menjadi Belum Lunas.']);
        }

        $pembayaran->update($request->all());

        return redirect()->back()->with('success', 'Data transaksi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        
        if (\App\Models\Pemotongan::where('id_hewan', $pembayaran->id_hewan)->exists()) {
            return redirect()->back()->withErrors(['Error' => 'Tidak dapat menghapus data Pembayaran karena hewan sudah diproses Pemotongan.']);
        }

        $pembayaran->delete();

        return redirect()->back()->with('success', 'Data transaksi berhasil dihapus!');
    }
}