<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'service_id',
        'service_price',
        'status',
        'reservation_date',
        'reservation_number',
    ];
    
    protected $casts = [
        'reservation_date' => 'datetime',
    ];
    
    protected static function booted()
    {
        static::creating(function ($reservation) {
            $date = \Carbon\Carbon::parse($reservation->reservation_date)->toDateString();
    
            $lastNumber = self::whereDate('reservation_date', $date)->max('reservation_number');
    
            $reservation->reservation_number = $lastNumber ? $lastNumber + 1 : 1;
        });
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompeted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}
