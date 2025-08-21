<?php

namespace App\Filament\Resources;

use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Resources\ServiceResource\Pages;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static ?string $navigationGroup = 'الخدمات';
    protected static ?string $navigationLabel = 'الخدمات';
    protected static ?string $pluralModelLabel = 'الخدمات';
    protected static ?string $modelLabel = 'خدمة';
    protected static ?string $slug = 'services';
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('اسم الخدمة')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('الوصف')
                    ->rows(4)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('price')
                    ->label('السعر')
                    ->numeric()
                    ->prefix('ريال')
                    ->required(),

                Forms\Components\Select::make('doctor_id')
                    ->label('الطبيب المسؤول')
                    ->relationship('doctor', 'name')
                    ->searchable()
                    ->required()
                    ->preload(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الخدمة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('الطبيب')
                    ->sortable()
                    ->badge()
                    ->color('primary'),  

                Tables\Columns\TextColumn::make('price')
                    ->label('السعر')
                    ->money('sar')
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
                    ->relationship('doctor', 'name'),
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
