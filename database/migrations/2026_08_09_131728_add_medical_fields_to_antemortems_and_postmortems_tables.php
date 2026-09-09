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
        Schema::table('antemortems', function (Blueprint $table) {
            $table->string('kondisi_fisik')->default('Normal')->after('tanggal_periksa');
            $table->string('tanda_penyakit')->default('Tidak Ada')->after('kondisi_fisik');
        });

        Schema::table('postmortems', function (Blueprint $table) {
            $table->string('limpa')->default('Normal')->after('waktu_periksa');
            $table->string('hati')->default('Normal')->after('limpa');
            $table->string('daging')->default('Normal')->after('hati');
            $table->string('paru_paru')->default('Normal')->after('daging');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('antemortems', function (Blueprint $table) {
            $table->dropColumn(['kondisi_fisik', 'tanda_penyakit']);
        });

        Schema::table('postmortems', function (Blueprint $table) {
            $table->dropColumn(['limpa', 'hati', 'daging', 'paru_paru']);
        });
    }
};
