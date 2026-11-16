<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KajianParam extends Model
{
    protected $table = 'kajian_params';

    protected $fillable = [
        'kajian_proyek_id',
        'parameter',
        'value',
    ];

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(KajianProyek::class, 'kajian_proyek_id');
    }
}
