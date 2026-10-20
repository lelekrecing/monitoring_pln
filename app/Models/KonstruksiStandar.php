<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonstruksiStandar extends Model
{
    protected $table = 'standar_konstruksis';

    protected $fillable = [
        'urutan',
        'no',
        'nama_konstruksi',
        'link_gambar',
        'sub_bab_kode',
        'sub_bab_nama',
    ];
}