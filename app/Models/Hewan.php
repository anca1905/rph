<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hewan extends Model
{
    use HasFactory;

    protected $table = 'hewans';
    protected $primaryKey = 'id_hewan'; 

    protected $fillable = [
        'no_registrasi',
        'tanggal_masuk',
        'nama_pemilik',
        'asal_hewan',
        'kategori',
        'jenis_hewan',
        'jenis_kelamin',
        'umur',
        'berat',
        'status',
        'user_id',
        'id_laporan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function antemortem()
    {
        return $this->hasOne(Antemortem::class, 'id_hewan', 'id_hewan');
    }

    public function postmortem()
    {
        return $this->hasOne(Postmortem::class, 'id_hewan', 'id_hewan');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_hewan', 'id_hewan');
    }

    public function pemotongan()
    {
        return $this->hasOne(Pemotongan::class, 'id_hewan', 'id_hewan');
    }
}