<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antemortem extends Model
{
    use HasFactory;

    protected $table = 'antemortems';
    protected $primaryKey = 'id_antemortem';

    protected $fillable = [
        'id_hewan',
        'tanggal_periksa',
        'kondisi_fisik',
        'tanda_penyakit',
        'catatan',
        'status_antemortem',
    ];

    public function hewan()
    {
        return $this->belongsTo(Hewan::class, 'id_hewan', 'id_hewan');
    }
}