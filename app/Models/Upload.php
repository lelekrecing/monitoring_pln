<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $table = 'uploads';

    public $timestamps = false;

    protected $fillable = [
        'nama_files',
        'file_path',
        'versi',
        'jumlah_sheet',
        'upload_by',
        'created_at',
        'status',
        'jenis_monitoring',
        'sheet_selesai',
        'sheet_sedang_diproses',
        'pesan_error',
    ];
}