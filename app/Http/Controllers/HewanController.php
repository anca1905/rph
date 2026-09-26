<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hewan;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\HewanImport;

class HewanController extends Controller
{
    public function index()
    {
        $hewans = Hewan::orderBy('tanggal_masuk', 'desc')->get();

        $lastPH = Hewan::where('no_registrasi', 'like', 'PH-%')->orderBy('id_hewan', 'desc')->first();
        $nextPH = $lastPH ? 'PH-' . str_pad(intval(substr($lastPH->no_registrasi, 3)) + 1, 3, '0', STR_PAD_LEFT) : 'PH-001';

        return view('hewan.index', compact('hewans', 'nextPH'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_masuk' => 'required|date',
            'nama_pemilik'  => 'required|string|max:255',
            'kecamatan'     => 'required|string|max:255',
            'desa'          => 'required|string|max:255',
            'jenis_hewan'   => 'required|string|max:255', 
            'jenis_kelamin' => 'required|in:Jantan,Betina',
            'berat'         => 'nullable|numeric',
            'umur'          => 'nullable|string|max:255',
        ]);

        $prefix = 'PH-';
        $lastHewan = Hewan::where('no_registrasi', 'like', $prefix . '%')
                          ->orderBy('id_hewan', 'desc')
                          ->first();
        
        if ($lastHewan) {
            $lastNumber = intval(substr($lastHewan->no_registrasi, 3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        $noRegistrasiOtomatis = $prefix . $newNumber;

        Hewan::create([
            'no_registrasi' => $noRegistrasiOtomatis,
            'tanggal_masuk' => $request->tanggal_masuk,
            'nama_pemilik'  => $request->nama_pemilik,
            'asal_hewan'    => $request->desa . ', ' . $request->kecamatan,
            'kategori'      => 'Hewan Harian',
            'jenis_hewan'   => $request->jenis_hewan,
            'jenis_kelamin' => $request->jenis_kelamin,
            'berat'         => $request->berat,
            'umur'          => $request->umur,
            'status'        => 'Menunggu Antemortem', 
        ]);

        return redirect()->back()->with('success', 'Data hewan berhasil didaftarkan dengan No. Reg: ' . $noRegistrasiOtomatis);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_masuk' => 'required|date',
            'nama_pemilik'  => 'required|string|max:255',
            'kecamatan'     => 'required|string|max:255',
            'desa'          => 'required|string|max:255',
            'jenis_hewan'   => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Jantan,Betina',
            'berat'         => 'nullable|numeric',
            'umur'          => 'nullable|string|max:255',
        ]);

        $hewan = Hewan::findOrFail($id);
        $hewan->update([
            'tanggal_masuk' => $request->tanggal_masuk,
            'nama_pemilik'  => $request->nama_pemilik,
            'asal_hewan'    => $request->desa . ', ' . $request->kecamatan,
            'kategori'      => 'Hewan Harian',
            'jenis_hewan'   => $request->jenis_hewan,
            'jenis_kelamin' => $request->jenis_kelamin,
            'berat'         => $request->berat,
            'umur'          => $request->umur,
        ]);

        return redirect()->back()->with('success', 'Data hewan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $hewan = Hewan::findOrFail($id);
        $hewan->delete();

        return redirect()->back()->with('success', 'Data hewan berhasil dihapus!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_import' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new HewanImport, $request->file('file_import'));
            return redirect()->back()->with('success', 'Data Hewan Harian berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }
}
