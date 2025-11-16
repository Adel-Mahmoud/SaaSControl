<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class SubscriptionChart extends ChartWidget
{
    protected static ?string $heading = 'معدل الاشتراكات الشهري';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        Carbon::setLocale('ar'); 
        $months = collect(range(1, 12))->map(function ($month) {
            return Carbon::createFromDate(null, $month, 1)->translatedFormat('F'); 
        });

        $data = $months->map(function ($monthName, $index) {
            // عدد الاشتراكات في الشهر
            return Subscription::whereMonth('created_at', $index + 1)->count();
        });

        return [
            'labels' => $months->toArray(),
            'datasets' => [
                [
                    'label' => 'عدد الاشتراكات الجديدة',
                    'data' => $data->toArray(),
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                ],
            ],
        ];
    }
}
