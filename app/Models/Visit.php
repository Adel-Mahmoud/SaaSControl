<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'visit_type',
        'visit_date',
        'amount_due',
        'amount_paid',
        'payment_status',
        'is_exception',
        'exception_reason',
    ];

    protected $casts = [
        'visit_date'    => 'datetime',
        'amount_due'    => 'decimal:2',
        'amount_paid'   => 'decimal:2',
        'is_exception'  => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
