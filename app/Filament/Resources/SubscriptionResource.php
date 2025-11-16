<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;
    protected static ?string $navigationGroup = 'الاشتراكات';
    protected static ?string $slug = 'subscriptions';
    protected static ?string $navigationLabel = 'الاشتراكات';
    protected static ?string $pluralModelLabel = 'الاشتراكات';
    protected static ?string $modelLabel = 'اشتراك';
    protected static ?int $navigationSort = 3;

    use \App\Traits\HasDefaultNavigation;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('بيانات الاشتراك')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('اسم الاشتراك')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan('full'),

                    Forms\Components\TextInput::make('domain')
                        ->label('الدومين')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\TextInput::make('db_name')
                        ->label('اسم قاعدة البيانات')
                        ->maxLength(255),

                    Forms\Components\Select::make('status')
                        ->label('الحالة')
                        ->options([
                            'active' => 'نشط',
                            'inactive' => 'غير نشط'
                        ])
                        ->required(),

                    Forms\Components\DatePicker::make('starts_at')
                        ->label('تاريخ البداية')
                        ->nullable(),

                    Forms\Components\DatePicker::make('ends_at')
                        ->label('تاريخ النهاية')
                        ->nullable(),
                ])
                ->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('index')
                ->label('#')
                ->rowIndex(),

            Tables\Columns\TextColumn::make('name')
                ->label('اسم الاشتراك')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('domain')
                ->label('الدومين')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('db_name')
                ->label('قاعدة البيانات'),

            Tables\Columns\TextColumn::make('status')
                ->label('الحالة')
                ->badge()
                ->color(fn($state) => $state === 'active' ? 'success' : 'danger'),

            Tables\Columns\TextColumn::make('starts_at')
                ->label('تاريخ البداية')
                ->date()
                ->sortable(),

            Tables\Columns\TextColumn::make('ends_at')
                ->label('تاريخ النهاية')
                ->date()
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('تاريخ الإنشاء')
                ->dateTime()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ])
        ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
