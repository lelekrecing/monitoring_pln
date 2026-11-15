<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KajianProyek extends Model
{
    protected $table = 'kajian_proyeks';

    protected $fillable = [
        'kode_kajian',
        'mode',
        'ulp',
        'nama_pekerjaan',
        'jarak_gudang_km',
        'tahun',
        'total_pelanggan',
        'total_daya_kva',
        'jumlah_trafo',
        'kapasitas_trafo_kva',
        'estimasi_sutm_km',
        'estimasi_sutr_km',
        'jumlah_tiang',
        'estimasi_nilai_rab',
        'estimasi_pendapatan_tahun',
        'payback_tahun',
        'layak_otomatis',
        'hasil_ringkas',
        'status',
        'catatan_ulp',
        'catatan_perencanaan',
        'kebutuhan_jaringan',
        'vendor_kontrak',
        'no_kontrak',
        'nilai_kontrak_pakai',
        'terkontrak_at',
        'user_id',
        'created_by_name',
        'created_by_role',
        'updated_by_name',
    ];

    protected $casts = [
        'jarak_gudang_km' => 'float',
        'total_pelanggan' => 'integer',
        'total_daya_kva' => 'float',
        'jumlah_trafo' => 'integer',
        'kapasitas_trafo_kva' => 'float',
        'estimasi_sutm_km' => 'float',
        'estimasi_sutr_km' => 'float',
        'jumlah_tiang' => 'integer',
        'estimasi_nilai_rab' => 'float',
        'estimasi_pendapatan_tahun' => 'float',
        'payback_tahun' => 'float',
        'layak_otomatis' => 'boolean',
        'kebutuhan_jaringan' => 'array',
        'nilai_kontrak_pakai' => 'float',
        'terkontrak_at' => 'datetime',
    ];

    public function pelanggans(): HasMany
    {
        return $this->hasMany(KajianPelanggan::class, 'kajian_proyek_id')->orderBy('slot');
    }

    public function uraiRows(): HasMany
    {
        return $this->hasMany(KajianUraiRow::class, 'kajian_proyek_id')->orderBy('row_number');
    }
}
