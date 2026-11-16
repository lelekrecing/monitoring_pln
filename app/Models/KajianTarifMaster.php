<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KajianTarifMaster extends Model
{
    protected $table = 'kajian_tarif_masters';

    protected $fillable = [
        'kode_tarif',
        'kategori',
        'tarif_ap2t',
        'default_daya_kva',
        'jam_nyala_bulan',
        'bp_satuan_rp',
        'ket_bp',
        'ujl_satuan_rp',
        'ket_ujl',
        'tarif_kwh_rp',
        'ket_tarif',
        'sort_order',
    ];

    protected $casts = [
        'default_daya_kva' => 'float',
        'jam_nyala_bulan'  => 'float',
        'bp_satuan_rp'     => 'float',
        'ujl_satuan_rp'    => 'float',
        'tarif_kwh_rp'     => 'float',
        'sort_order'       => 'integer',
    ];
}
