<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
    {
        Schema::create('pemotongans', function (Blueprint $table) {
            $table->id('id_pemotongan');
            $table->unsignedBigInteger('id_hewan');
            $table->foreign('id_hewan')->references('id_hewan')->on('hewans')->onDelete('cascade');
            
            $table->datetime('waktu_potong'); // Kolom waktu
            $table->enum('status_pemotongan', ['Menunggu Dipotong', 'Selesai Dipotong'])->default('Menunggu Dipotong');
            
            $table->decimal('berat_karkas', 8, 2);
            $table->decimal('berat_jeroan', 8, 2)->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemotongans');
    }
};