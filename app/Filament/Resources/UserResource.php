<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'المستخدمين';
    protected static ?string $slug = 'users';
    protected static ?string $navigationLabel = 'المستخدمين';
    protected static ?string $pluralModelLabel = 'المستخدمين';
    protected static ?string $modelLabel = 'مستخدم';
    protected static ?int $navigationSort = 6;

    use \App\Traits\HasDefaultNavigation;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('تفاصيل المستخدم')
                ->description('قم بتعبئة التفاصيل الخاصة بالمستخدم')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('اسم المستخدم')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan('full'),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->label('البريد الإلكتروني')
                        ->unique(ignoreRecord: true)
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->label('رقم الهاتف')
                        ->unique(ignoreRecord: true)
                        ->required()
                        ->maxLength(20),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->label('كلمة المرور')
                        ->dehydrateStateUsing(fn($state) => !empty($state) ? bcrypt($state) : null)
                        ->required(fn(string $context) => $context === 'create')
                        ->maxLength(255),
                    Forms\Components\Select::make('roles')
                        ->label('Role')
                        ->multiple(false)
                        ->relationship('roles', 'name')
                        ->required(),
                ])
                ->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                ->label('#')
                ->rowIndex(),
                // Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم المستخدم')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('roles.name')->label('Role'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => $record->id !== auth()->id()),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->before(function ($records) {
                        return $records->reject(fn($r) => $r->id === auth()->id());
                    }),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
