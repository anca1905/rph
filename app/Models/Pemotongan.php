<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemotongan extends Model
{
    use HasFactory;

    protected $table = 'pemotongans';
    protected $primaryKey = 'id_pemotongan';

    protected $fillable = [
        'id_hewan',
        'waktu_potong',
        'status_pemotongan',
        'berat_karkas',
        'berat_jeroan',
    ];

    public function hewan()
    {
        return $this->belongsTo(Hewan::class, 'id_hewan', 'id_hewan');
    }
}