<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // INJEKSI SUPER ADMIN (Tanpa kolom role karena tabel tidak memilikinya)
        User::updateOrCreate(
            ['username' => 'admin_pln'], 
            [
                'name'     => 'Super Administrator',
                'password' => Hash::make('AdminPln123!'), 
            ]
        );
    }
}