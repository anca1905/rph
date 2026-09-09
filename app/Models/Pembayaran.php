<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayarans';
    protected $primaryKey = 'id_pembayaran';

    protected $fillable = [
        'id_hewan',
        'total_pembayaran',
        'status_pembayaran',
    ];

    // Fungsi untuk menarik data dari tabel hewans
    public function hewan()
    {
        return $this->belongsTo(Hewan::class, 'id_hewan', 'id_hewan');
    }
}