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
        Schema::table('users', function (Blueprint $table) {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'petugas', 'pimpinan', 'pekerja_idul_adha') DEFAULT 'petugas'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kita kembalikan ke semula tanpa pekerja_idul_adha
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'petugas', 'pimpinan') DEFAULT 'petugas'");
        });
    }
};
