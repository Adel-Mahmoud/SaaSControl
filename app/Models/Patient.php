<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
    ];

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }
}