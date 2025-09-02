<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Patient;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Navigation\NavigationItem;
use App\Filament\Resources\PatientResource\Pages;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;
    protected static ?string $navigationGroup = 'المرضى';
    protected static ?string $slug = 'patients';
    protected static ?string $navigationLabel = 'المرضى';
    protected static ?string $pluralModelLabel = 'المرضى';
    protected static ?string $modelLabel = 'مريض';

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
        return $form->schema([
            Forms\Components\Section::make('بيانات المريض')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('المستخدم')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->createOptionForm([ 
                            Forms\Components\TextInput::make('name')
                                ->label('الاسم')
                                ->required(),
                            Forms\Components\TextInput::make('email')
                                ->label('البريد الإلكتروني')
                                ->email()
                                ->unique(User::class, 'email')
                                ->required(),
                            Forms\Components\TextInput::make('phone')
                                ->label('رقم الهاتف')
                                ->unique(User::class, 'phone')
                                ->required(),
                            Forms\Components\TextInput::make('password')
                                ->label('كلمة المرور')
                                ->password()
                                ->required()
                                ->dehydrateStateUsing(fn($state) => bcrypt($state)),
                        ]),

                    Forms\Components\TextInput::make('address')
                        ->label('عنوان المريض')
                        ->maxLength(500)
                        ->columnSpanFull(),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('اسم المريض')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.phone')
                    ->label('رقم الهاتف')
                    ->searchable(),
                    
                    Tables\Columns\TextColumn::make('address')
                    ->label('العنوان')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
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
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}

