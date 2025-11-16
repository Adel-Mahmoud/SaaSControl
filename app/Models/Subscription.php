<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions'; 

    protected $fillable = [
        'name',
        'domain',
        'db_name',
        'status',
        'starts_at',
        'ends_at',
    ];
}
