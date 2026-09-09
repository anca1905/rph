<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
    {
        Schema::create('antemortems', function (Blueprint $table) {
            $table->id('id_antemortem');
            $table->unsignedBigInteger('id_hewan');
            $table->foreign('id_hewan')->references('id_hewan')->on('hewans')->onDelete('cascade');
            
            $table->date('tanggal_periksa');
            $table->text('catatan')->nullable(); 
            // TAMBAHKAN 'Karantina' PADA BARIS INI:
            $table->enum('status_antemortem', ['Menunggu', 'Lolos', 'Karantina', 'Ditolak'])->default('Menunggu');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('antemortems');
    }
};