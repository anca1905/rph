<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id('id_pembayaran');
            
            // Relasi ke tabel hewans
            $table->unsignedBigInteger('id_hewan');
            $table->foreign('id_hewan')->references('id_hewan')->on('hewans')->onDelete('cascade');
            
            // Atribut khusus pembayaran
            $table->decimal('total_pembayaran', 12, 2)->default(0);
            $table->enum('status_pembayaran', ['Lunas', 'Belum Lunas'])->default('Belum Lunas');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};