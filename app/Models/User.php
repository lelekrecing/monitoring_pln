<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * =========================================================
     * ARSITEKTUR KEAMANAN (MASS ASSIGNMENT) STANDAR LARAVEL
     * =========================================================
     */
    protected $fillable = [
        'name',
        'username', // Telah ditambahkan
        'password',
        'role',     // Telah ditambahkan
    ];

    /**
     * Menyembunyikan data sensitif saat data diubah ke array/JSON
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Konversi tipe data secara otomatis
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}