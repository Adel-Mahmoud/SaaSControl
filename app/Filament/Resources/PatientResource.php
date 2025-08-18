<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Models\Patient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'المرضى';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'patients';
    protected static ?string $navigationLabel = 'المرضى';
    protected static ?string $pluralModelLabel = 'المرضى';
    protected static ?string $modelLabel = 'مريض';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('patient_id')
                    ->label('ابحث عن مريض')
                    ->options(function ($get, $set) {
                        return Patient::query()
                            ->limit(10)
                            ->get()
                            ->mapWithKeys(function ($patient) {
                                return [
                                    $patient->id => "{$patient->name} - {$patient->phone}"
                                ];
                            });
                    })
                    ->getSearchResultsUsing(function (string $search) {
                        return Patient::query()
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->limit(10)
                            ->get()
                            ->mapWithKeys(function ($patient) {
                                return [
                                    $patient->id => "{$patient->name} - {$patient->phone}"
                                ];
                            });
                    })
                    ->getOptionLabelUsing(function ($value) {
                        $patient = Patient::find($value);
                        return $patient ? "{$patient->name} - {$patient->phone}" : '';
                    })
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $patient = Patient::find($state);
                            if ($patient) {
                                $set('name', $patient->name);
                                $set('phone', $patient->phone);
                                $set('address', $patient->address);
                            }
                        }
                    })
                    ->columnSpanFull(),

                Forms\Components\Section::make('تفاصيل المريض')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم المريض')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->required()
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('address')
                            ->label('عنوان المريض')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('الزيارات')
                    ->schema([
                        Forms\Components\HasManyRepeater::make('visits')
                            ->label('الزيارات')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('visit_type')
                                    ->label('نوع الزيارة')
                                    ->options([
                                        'new' => 'كشف جديد',
                                        'followup' => 'متابعة',
                                    ])
                                    ->required(),

                                Forms\Components\DateTimePicker::make('visit_date')
                                    ->label('تاريخ الزيارة')
                                    ->required(),

                                Forms\Components\TextInput::make('amount_due')
                                    ->label('المبلغ المستحق')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->prefix('$'),

                                Forms\Components\TextInput::make('amount_paid')
                                    ->label('المبلغ المدفوع')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->prefix('$'),

                                Forms\Components\Select::make('payment_status')
                                    ->label('حالة الدفع')
                                    ->options([
                                        'paid' => 'مدفوع',
                                        'unpaid' => 'غير مدفوع',
                                        'partial' => 'جزئي',
                                    ])
                                    ->required(),

                                Forms\Components\Toggle::make('is_exception')
                                    ->label('استثناء')
                                    ->live(),

                                Forms\Components\Textarea::make('exception_reason')
                                    ->label('سبب الاستثناء')
                                    ->rows(3)
                                    ->hidden(fn(Forms\Get $get) => !$get('is_exception')),
                            ])
                            ->defaultItems(1)
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم المريض')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable(),

                Tables\Columns\TextColumn::make('visits_count')
                    ->label('عدد الزيارات')
                    ->counts('visits')
                    ->sortable(),

                Tables\Columns\TextColumn::make('address')
                    ->label('عنوان المريض')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
