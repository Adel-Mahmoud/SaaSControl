<?php

namespace App\Exports;

use App\Models\Reservation;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReservationsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;
    protected $doctorId;
    protected $status;

    public function __construct($startDate, $endDate, $doctorId = null, $status = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->doctorId = $doctorId;
        $this->status = $status;
    }

    public function collection()
    {
        $query = Reservation::with(['patient.user', 'doctor.user', 'service'])
            ->whereBetween('reservation_date', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);

        if ($this->doctorId) {
            $query->where('doctor_id', $this->doctorId);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'رقم الحجز',
            'اسم المريض',
            'الهاتف',
            'الطبيب',
            'الخدمة',
            'سعر الخدمة',
            'تاريخ الحجز',
            'رقم الحجز في اليوم',
            'الحالة'
        ];
    }

    public function map($reservation): array
    {
        return [
            $reservation->id,
            $reservation->patient->user->name,
            $reservation->patient->user->phone,
            $reservation->doctor->user->name,
            $reservation->service->name,
            $reservation->service_price,
            $reservation->reservation_date,
            $reservation->reservation_number,
            $this->getStatusText($reservation->status)
        ];
    }

    private function getStatusText($status)
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي'
        ];

        return $statuses[$status] ?? $status;
    }
}