<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationLabel = 'لوحة التحكم';
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $slug = 'dashboard';

    protected static string $view = 'filament.pages.dashboard';

    public function getTitle(): string
    {
        return 'لوحة التحكم';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\SubscriptionSummary::class,
            \App\Filament\Widgets\SubscriptionPieChart::class,
            \App\Filament\Widgets\SubscriptionChart::class,
        ];
    }
}
