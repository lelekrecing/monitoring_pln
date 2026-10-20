<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Panggil UserSeeder untuk 8 akun (ULP & Admin) yang baru kita buat
        $this->call(UserSeeder::class);

        // Akun admin
        $this->call(AdminSeeder::class);

        // Data awal Standar Konstruksi (katalog gambar SUTM)
        $this->call(KonstruksiStandarSeeder::class);
    }
}