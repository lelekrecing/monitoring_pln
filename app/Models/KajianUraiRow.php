<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KajianUraiRow extends Model
{
    protected $table = 'kajian_urai_rows';

    protected $fillable = [
        'kajian_proyek_id',
        'row_number',
        'row_data',
        'updated_by',
    ];

    protected $casts = [
        'row_number' => 'integer',
        'row_data' => 'array',
    ];

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(KajianProyek::class, 'kajian_proyek_id');
    }
}
