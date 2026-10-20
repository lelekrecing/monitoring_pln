<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $users = [
            // 1-7 (Role: user)
            [
                'name' => 'Perencanaan UP3 Jambi',
                'username' => 'perencanaan_up3jambi',
                'password' => Hash::make('perencanaan234'),
                'role' => 'user',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'ULP Kuala Tungkal',
                'username' => 'ulp_kualatungkal',
                'password' => Hash::make('ulpkualatungkal234'),
                'role' => 'user',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'ULP Kota Baru',
                'username' => 'ulp_kotabaru',
                'password' => Hash::make('ulpkotabaru234'),
                'role' => 'user',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'ULP Muara Bulian',
                'username' => 'ulp_muarabulian',
                'password' => Hash::make('ulpmuarabulian234'),
                'role' => 'user',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'ULP Telanaipura',
                'username' => 'ulp_telanaipura',
                'password' => Hash::make('ulptelanaipura234'),
                'role' => 'user',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'ULP Muara Sabak',
                'username' => 'ulp_muarasabak',
                'password' => Hash::make('ulpmuarasabak234'),
                'role' => 'user',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'ULP Seberang Kota',
                'username' => 'ulp_seberangkota',
                'password' => Hash::make('ulpseberangkota234'),
                'role' => 'user',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // 8 (Role: admin)
            [
                'name' => 'Admin Perencanaan',
                'username' => 'adminren_up3jambi',
                'password' => Hash::make('perencanaanup3jambi789'),
                'role' => 'admin',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('users')->insert($users);
    }
}