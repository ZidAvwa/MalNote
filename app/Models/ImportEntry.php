<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportEntry extends Model
{
    protected $fillable = [
        'import_session_id', 'position', 'original_title', 'mal_id',
        'final_title', 'score', 'status', 'skipped',
    ];

    protected $casts = ['skipped' => 'boolean'];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ImportSession::class, 'import_session_id');
    }
}