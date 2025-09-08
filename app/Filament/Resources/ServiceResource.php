<?php

namespace App\Filament\Resources;

use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Navigation\NavigationItem;
use App\Filament\Resources\ServiceResource\Pages;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static ?string $navigationGroup = 'الخدمات';
    protected static ?string $navigationLabel = 'الخدمات';
    protected static ?string $modelLabel = 'خدمة';
    protected static ?string $pluralModelLabel = 'الخدمات';
    protected static ?string $slug = 'services';
    protected static ?string $title = 'إدارة الخدمات';
    protected static ?int $navigationSort = 4;

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل الخدمة')
                    ->description('قم بتعبئة البيانات الخاصة بالخدمة')
                    ->schema([
                        Forms\Components\Select::make('name')
                            ->label('اسم الخدمة')
                            ->options([
                                'كشف عادي' => 'كشف عادي',
                                'كشف مستعجل' => 'كشف مستعجل',
                                'كشف شامل' => 'كشف شامل',                                'استشارة طبية' => 'استشارة طبية',
                                'فحص طبي' => 'فحص طبي',
                                'تحليل دم' => 'تحليل دم',
                                'تصوير أشعة' => 'تصوير أشعة',
                                'علاج طبيعي' => 'علاج طبيعي',
                                'تطعيم' => 'تطعيم',
                                'جراحة بسيطة' => 'جراحة بسيطة',
                                'تنظيف أسنان' => 'تنظيف أسنان',
                                'خدمة طوارئ' => 'خدمة طوارئ',
                                'خدمة منزلية' => 'خدمة منزلية',
                            ])
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('price')
                            ->label('السعر')
                            ->numeric()
                            ->prefix('جنيه')
                            ->required(),

                        Forms\Components\Select::make('doctor_id')
                            ->label('الطبيب المسؤول')
                            ->relationship('doctor', 'id')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->user->name)
                            ->searchable()
                            ->required()
                            ->preload(),


                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                    // ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الخدمة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('doctor.user.name')
                    ->label('الطبيب')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('price')
                    ->label('السعر')
                    // ->money('sar')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('doctor_id')
                    ->label('الطبيب')
                    ->relationship('doctor.user', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('عرض'),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
