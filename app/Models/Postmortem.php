<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postmortem extends Model
{
    use HasFactory;

    protected $table = 'postmortems';
    protected $primaryKey = 'id_postmortem';

    protected $fillable = [
        'id_hewan',
        'waktu_periksa',
        'limpa',
        'hati',
        'daging',
        'paru_paru',
        'kondisi_karkas',
        'kondisi_jeroan',
        'catatan_medis',
    ];

    public function hewan()
    {
        return $this->belongsTo(Hewan::class, 'id_hewan', 'id_hewan');
    }
}