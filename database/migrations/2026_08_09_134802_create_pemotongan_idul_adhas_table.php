<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pemotongan_idul_adhas', function (Blueprint $table) {
            $table->id('id_pemotongan_ia');
            $table->date('tanggal');
            $table->string('nama_lokasi');
            $table->string('jenis_lokasi');
            $table->string('jenis_lokasi_lainnya')->nullable();
            $table->text('alamat');
            $table->string('propinsi');
            $table->string('kabupaten');
            $table->string('kecamatan');
            $table->string('desa');
            $table->string('jenis_hewan');
            $table->integer('jumlah');
            $table->string('upt');
            $table->string('pelapor');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemotongan_idul_adhas');
    }
};
