<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemotonganIdulAdha extends Model
{
    use HasFactory;

    protected $table = 'pemotongan_idul_adhas';
    protected $primaryKey = 'id_pemotongan_ia';

    protected $fillable = [
        'tanggal',
        'nama_lokasi',
        'jenis_lokasi',
        'jenis_lokasi_lainnya',
        'alamat',
        'propinsi',
        'kabupaten',
        'kecamatan',
        'desa',
        'jenis_hewan',
        'jumlah',
        'upt',
        'pelapor'
    ];
}
