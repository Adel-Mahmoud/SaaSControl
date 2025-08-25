<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Doctor;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use App\Filament\Resources\DoctorResource\Pages;

class DoctorResource extends Resource
{
    protected static ?string $model = Doctor::class;
    protected static ?string $navigationGroup = 'الأطباء';
    protected static ?string $navigationLabel = 'الأطباء';
    protected static ?string $pluralModelLabel = 'الأطباء';
    protected static ?string $modelLabel = 'طبيب';
    protected static ?string $slug = 'doctors';
    // protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static bool $isCollapsed = true;

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
            Forms\Components\Section::make('بيانات الطبيب')
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

                    Forms\Components\TextInput::make('specialization')
                        ->label('التخصص')
                        ->required(),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.phone')
                    ->label('الهاتف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('specialization')->label('التخصص')
            ])
            ->filters([])
            ->actions([
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
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'edit' => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }
}
