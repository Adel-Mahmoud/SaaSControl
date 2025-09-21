<?php

namespace App\Traits;

use Filament\Navigation\NavigationItem;

trait HasDefaultNavigation
{
    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make()
                ->label('عرض الكل')
                ->url(static::getUrl('index'))
                ->group(static::getNavigationGroup())
                ->sort(0),

            NavigationItem::make()
                ->label('إنشاء جديد')
                ->url(static::getUrl('create'))
                ->group(static::getNavigationGroup())
                ->sort(1),
        ];
    }
}