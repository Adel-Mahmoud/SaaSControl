<?php

namespace App\Filament\Navigation;

use Filament\Navigation\NavigationGroup;

class Groups
{
    public static function all(): array
    {
        return [
            NavigationGroup::make()->label('الحجوزات')->icon('heroicon-o-calendar'),
            NavigationGroup::make()->label('الأطباء')->icon('heroicon-o-user-group'),
            NavigationGroup::make()->label('الخدمات')->icon('heroicon-o-briefcase'),
            NavigationGroup::make()->label('المرضى')->icon('heroicon-o-users'),
            NavigationGroup::make()->label('تقارير')->icon('heroicon-o-chart-bar'), 
            NavigationGroup::make()->label('المستخدمين')->icon('heroicon-o-users'), 
        ];
    }
}