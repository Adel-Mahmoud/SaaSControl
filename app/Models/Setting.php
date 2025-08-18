<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'project_name',
        'logo_path',
        'consult_price',
        'followup_price',
        'followup_days',
    ];
}
