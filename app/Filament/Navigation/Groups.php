<?php

namespace App\Filament\Navigation;

use Filament\Navigation\NavigationGroup;

class Groups
{
    public static function all(): array
    {
        return [
            
            NavigationGroup::make()->label('المستخدمين')->icon('heroicon-o-user-group'), 
            NavigationGroup::make()->label('الادوار والصلاحيات')->icon('heroicon-o-lock-closed'),
            NavigationGroup::make()->label('الإعدادات')->icon('heroicon-o-cog'), 
        ];
    }
} 