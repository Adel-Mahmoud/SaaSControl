<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'الأدوار';
    protected static ?string $pluralModelLabel = 'الأدوار';
    protected static ?string $modelLabel = 'دور';
    protected static ?string $navigationGroup = 'الادوار والصلاحيات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('اسم الدور')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\CheckboxList::make('permissions')
                    ->label('الصلاحيات')
                    ->relationship('permissions', 'name')
                    ->columns(3) 
                    ->gridDirection('row')
                    ->bulkToggleable()
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#'),
                Tables\Columns\TextColumn::make('name')->label('اسم الدور'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('view_permissions')
                    ->label('عرض الصلاحيات')
                    ->icon('heroicon-o-eye')
                    ->button()
                    ->color('primary')
                    ->modalHeading(fn($record) => "صلاحيات الدور: {$record->name}")
                    ->modalContent(function ($record) {
                        $badges = $record->permissions->map(fn($p) =>
                            "<div class='bg-blue-100 text-blue-800 px-3 py-1 rounded-lg text-sm font-medium text-center'>{$p->name}</div>"
                        )->join('');

                        return new \Illuminate\Support\HtmlString("
                            <div class='grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2'>
                                {$badges}
                            </div>
                        ");
                    })
                    ->modalWidth('md'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
