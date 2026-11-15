<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontrakKuota extends Model
{
    protected $table = 'kontrak_kuotas';

    protected $fillable = [
        'vendor',
        'no_kontrak',
        'jenis_pekerjaan',
        'tahun',
        'kuota_awal_rp',
        'terpakai_manual_rp',
        'kuota_pekerjaan_maks',
        'status_aktif',
        'catatan',
    ];

    protected $casts = [
        'kuota_awal_rp' => 'float',
        'terpakai_manual_rp' => 'float',
        'kuota_pekerjaan_maks' => 'integer',
        'status_aktif' => 'boolean',
    ];
}
