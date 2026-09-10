<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat akun Admin
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Buat akun Petugas
        User::create([
            'name' => 'Petugas RPH',
            'username' => 'petugas',
            'email' => 'petugas@admin.com',
            'password' => Hash::make('password'),
            'role' => 'petugas',
        ]);

        // Buat akun Pimpinan
        User::create([
            'name' => 'Pimpinan',
            'username' => 'pimpinan',
            'email' => 'pimpinan@admin.com',
            'password' => Hash::make('password'),
            'role' => 'pimpinan',
        ]);
    }
}
