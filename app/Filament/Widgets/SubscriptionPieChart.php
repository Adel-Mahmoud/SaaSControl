<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Subscription;

class SubscriptionPieChart extends ChartWidget
{
    protected static ?string $heading = 'نسبة الاشتراكات';

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $active = Subscription::where('status', 'active')->count();
        $inactive = Subscription::where('status', 'inactive')->count();

        return [
            'labels' => ['نشط', 'غير نشط'],
            'datasets' => [
                [
                    'label' => 'Subscriptions',
                    'data' => [$active, $inactive],
                    'backgroundColor' => ['#16a34a', '#be5151'], 
                ],
            ],
        ];
    }
}
