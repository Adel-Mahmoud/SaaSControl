<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Filament\Resources\SettingResource\Pages;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?string $navigationLabel = 'الإعدادات';
    protected static ?string $modelLabel = 'إعداد';
    protected static ?int $navigationSort = 999;
    protected static bool $isCollapsed = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('group')
                    ->label('المجموعة')
                    ->options([
                        'general' => 'عام',
                        'appearance' => 'المظهر',
                        'social' => 'التواصل الاجتماعي',
                        'contact' => 'معلومات الاتصال',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('key')
                    ->label('مفتاح الإعداد')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('project_name, site_logo, etc.'),

                Forms\Components\Select::make('type')
                    ->label('نوع البيانات')
                    ->options([
                        'text' => 'نص',
                        'image' => 'صورة',
                        'boolean' => 'نعم/لا',
                        'json' => 'JSON',
                        'color' => 'لون',
                    ])
                    ->reactive()
                    ->required()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $set('value', null);
                    }),

                // Text, JSON, Color
                Forms\Components\TextInput::make('value')
                    ->label('القيمة')
                    ->nullable()
                    ->visible(fn($get) => in_array($get('type'), ['text', 'json', 'color'])),

                // Image 
                Forms\Components\FileUpload::make('value')
                    ->label('الصورة')
                    ->image()
                    ->directory('settings')
                    ->maxSize(2048)
                    ->nullable()
                    ->multiple(false)
                    ->getUploadedFileNameForStorageUsing(
                        fn(TemporaryUploadedFile $file): string =>
                        'setting_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension()
                    )
                    ->visible(fn($get) => $get('type') === 'image')
                    ->loadingIndicatorPosition('left')
                    ->panelLayout('integrated')
                    ->removeUploadedFileButtonPosition('right')
                    ->uploadButtonPosition('left')
                    ->uploadProgressIndicatorPosition('left'),

                // Boolean 
                Forms\Components\Toggle::make('value')
                    ->label('مفعل')
                    ->default(false)
                    ->visible(fn($get) => $get('type') === 'boolean')
                    ->dehydrateStateUsing(fn($state) => $state ? '1' : '0'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('المفتاح')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label('القيمة')
                    ->limit(50)
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->type === 'image' && $state) {
                            return '📷 صورة مرفوعة';
                        } elseif ($record->type === 'boolean') {
                            return $state === '1' ? 'نعم' : 'لا';
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'text' => 'gray',
                        'image' => 'success',
                        'boolean' => 'primary',
                        'json' => 'warning',
                        'color' => 'danger',
                        default => 'info',
                    }),

                Tables\Columns\TextColumn::make('group')
                    ->label('المجموعة')
                    ->badge()
                    ->color('success'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Setting $record) {
                        if ($record->type === 'image' && $record->value) {
                            Storage::delete($record->value);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
