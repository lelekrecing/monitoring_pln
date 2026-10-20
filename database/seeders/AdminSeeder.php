<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::firstOrCreate(
            ['username' => 'siipooke@gmail.com'], // Ubah 'email' menjadi 'username'
            [
                'nama' => 'Administrator',
                'password' => Hash::make('admin123')
            ]
        );
    }
}