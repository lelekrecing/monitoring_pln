<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KajianPelanggan extends Model
{
    protected $table = 'kajian_pelanggans';

    protected $fillable = [
        'kajian_proyek_id',
        'slot',
        'jenis_permohonan',
        'kategori_tarif',
        'kelompok_tarif',
        'jumlah_pelanggan',
        'daya_sebelum_kva',
        'daya_sesudah_kva',
        'penambahan_daya_kva',
    ];

    protected $casts = [
        'slot' => 'integer',
        'jumlah_pelanggan' => 'integer',
        'daya_sebelum_kva' => 'float',
        'daya_sesudah_kva' => 'float',
        'penambahan_daya_kva' => 'float',
    ];

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(KajianProyek::class, 'kajian_proyek_id');
    }
}
