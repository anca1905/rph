<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PekerjaIdulAdhaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pekerja = User::where('role', 'pekerja_idul_adha')->get();
        return view('pekerja_idul_adha.index', compact('pekerja'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'no_hp' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput()
                        ->with('error', 'Gagal menambahkan pekerja. Periksa kembali isian Anda.');
        }

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pekerja_idul_adha',
            'no_hp' => $request->no_hp,
        ]);

        return redirect()->route('pekerja.index')->with('success', 'Akun pekerja Idul Adha berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|string|max:20',
        ];

        // Jika password diisi, berarti ingin ganti password
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput()
                        ->with('error', 'Gagal mengubah data pekerja.');
        }

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->no_hp = $request->no_hp;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('pekerja.index')->with('success', 'Data pekerja Idul Adha berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        
        // Pastikan hanya bisa menghapus role pekerja_idul_adha
        if ($user->role == 'pekerja_idul_adha') {
            $user->delete();
            return redirect()->route('pekerja.index')->with('success', 'Akun pekerja berhasil dihapus!');
        }

        return redirect()->route('pekerja.index')->with('error', 'Akses ditolak. Tidak dapat menghapus admin/petugas lain.');
    }
}
