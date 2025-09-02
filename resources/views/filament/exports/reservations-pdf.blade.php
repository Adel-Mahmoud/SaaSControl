<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $reportTitle }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .summary { margin-top: 30px; }
        .summary-item { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $reportTitle }}</h1>
        <p>الفترة من {{ $startDate }} إلى {{ $endDate }}</p>
    </div>

    <div class="info">
        <h3>إحصائيات التقرير</h3>
        <p>إجمالي الحجوزات: {{ $totalReservations }}</p>
        <p>الحجوزات المكتملة: {{ $completedReservations }}</p>
        <p>الحجوزات قيد الانتظار: {{ $pendingReservations }}</p>
        <p>الحجوزات الملغاة: {{ $cancelledReservations }}</p>
        <p>إجمالي الإيرادات: {{ number_format($totalRevenue, 2) }} ر.س</p>
    </div>

    <h3>تفاصيل الحجوزات</h3>
    <table>
        <thead>
            <tr>
                <th>رقم الحجز</th>
                <th>اسم المريض</th>
                <th>الهاتف</th>
                <th>الطبيب</th>
                <th>الخدمة</th>
                <th>السعر</th>
                <th>التاريخ</th>
                <th>رقم الحجز</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservations as $reservation)
            <tr>
                <td>{{ $reservation->id }}</td>
                <td>{{ $reservation->patient->user->name }}</td>
                <td>{{ $reservation->patient->user->phone }}</td>
                <td>{{ $reservation->doctor->user->name }}</td>
                <td>{{ $reservation->service->name }}</td>
                <td>{{ number_format($reservation->service_price, 2) }} ر.س</td>
                <td>{{ $reservation->reservation_date }}</td>
                <td>{{ $reservation->reservation_number }}</td>
                <td>
                    @if($reservation->status == 'completed')
                    مكتمل
                    @elseif($reservation->status == 'pending')
                    قيد الانتظار
                    @else
                    ملغي
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>ملخص التقرير</h3>
        <p>تم إنشاء التقرير في: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>