<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Subscription;
use Illuminate\Support\Carbon;

class SubscriptionSummary extends BaseWidget
{
    protected ?string $headin = 'إحصائيات الاشتراكات';

    protected function getCards(): array
    {
        return [
            Card::make('الاشتراكات النشطة', Subscription::where('status', 'active')->count()),
            Card::make('الاشتراكات غير النشطة', Subscription::where('status', 'inactive')->count()),
            Card::make('ستنتهي قريبًا', Subscription::where('ends_at', '<=', Carbon::now()->addDays(7))->count()),
        ];
    }
}
