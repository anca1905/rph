<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postmortems', function (Blueprint $table) {
            $table->id('id_postmortem');
            
            // Relasi ke tabel hewan
            $table->unsignedBigInteger('id_hewan');
            $table->foreign('id_hewan')->references('id_hewan')->on('hewans')->onDelete('cascade');
            
            $table->datetime('waktu_periksa');
            $table->string('kondisi_karkas')->nullable();
            $table->string('kondisi_jeroan')->nullable();
            $table->text('catatan_medis')->nullable(); // Menggantikan temuan & keputusan
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postmortems');
    }
};