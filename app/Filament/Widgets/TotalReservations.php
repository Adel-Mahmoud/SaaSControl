<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Reservation;
use Carbon\Carbon;

class TotalReservations extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        $today = Carbon::today();
        
        $todayReservations = Reservation::whereDate('reservation_date', $today)->count();
        
        $todayPendingReservations = Reservation::whereDate('reservation_date', $today)
            ->where('status', 'pending')
            ->count();
        
        $todayRevenue = Reservation::whereDate('reservation_date', $today)
            ->where('status', '!=', 'cancelled')
            ->sum('service_price');
        
        $todayConfirmedReservations = Reservation::whereDate('reservation_date', $today)
            ->where('status', 'completad')
            ->count();

        return [
            Stat::make('حجوزات اليوم', $todayReservations)
                ->description('إجمالي الحجوزات لليوم')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
                
            Stat::make('المعلقة اليوم', $todayPendingReservations)
                ->description('حجوزات معلقة لليوم')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('المؤكدة اليوم', $todayConfirmedReservations)
                ->description('حجوزات مؤكدة لليوم')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
                
            Stat::make('إيرادات اليوم', number_format($todayRevenue) . ' ج.م')
                ->description('إجمالي الإيرادات لليوم')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),
        ];
    }
}