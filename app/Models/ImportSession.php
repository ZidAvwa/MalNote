<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportSession extends Model
{
    protected $fillable = ['filename', 'current_index'];

    public function entries(): HasMany
    {
        return $this->hasMany(ImportEntry::class)->orderBy('position');
    }
}