<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hewans', function (Blueprint $table) {
            $table->id('id_hewan');
            $table->string('no_registrasi')->unique();
            $table->date('tanggal_masuk'); // Dipindah ke atas
            $table->string('nama_pemilik');
            $table->string('jenis_hewan');
            $table->enum('jenis_kelamin', ['Jantan', 'Betina']);
            $table->string('umur')->nullable();
            $table->decimal('berat', 8, 2); 
            $table->string('asal_hewan');
            $table->enum('kategori', ['Hewan Harian', 'Hewan Idul Adha'])->default('Hewan Harian');
            $table->string('status')->default('Menunggu Antemortem');

            // Relasi ke tabel Users
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            // Relasi ke tabel Laporan
            $table->unsignedBigInteger('id_laporan')->nullable();
            $table->foreign('id_laporan')->references('id_laporan')->on('laporans')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hewans');
    }
};