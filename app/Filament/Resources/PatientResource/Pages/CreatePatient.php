<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use App\Models\Visit;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;

    protected array $visitData = [];

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->visitData = [
            'visit_type' => $data['visit_type'],
            'visit_date' => $data['visit_date'],
            'amount_due' => $data['amount_due'],
            'amount_paid' => $data['amount_paid'],
            'payment_status' => $data['payment_status'],
            'is_exception' => $data['is_exception'] ?? false,
            'exception_reason' => $data['exception_reason'] ?? null,
        ];

        unset(
            $data['visit_type'],
            $data['visit_date'],
            $data['amount_due'],
            $data['amount_paid'],
            $data['payment_status'],
            $data['is_exception'],
            $data['exception_reason']
        );

        return $data;
    }

    protected function afterCreate(): void
    {
        Visit::create(array_merge(
            $this->visitData,
            ['patient_id' => $this->record->id]
        ));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Patient Information')
                    ->schema(PatientResource::getPatientForm())
                    ->columns(2),

                \Filament\Forms\Components\Section::make('Visit Information')
                    ->schema([
                        \Filament\Forms\Components\Select::make('visit_type')
                            ->options([
                                'new' => 'New Visit',
                                'followup' => 'Follow-up',
                            ])
                            ->required(),

                        \Filament\Forms\Components\DateTimePicker::make('visit_date')
                            ->required()
                            ->default(now()),

                        \Filament\Forms\Components\TextInput::make('amount_due')
                            ->numeric()
                            ->required()
                            ->label('Amount Due'),

                        \Filament\Forms\Components\TextInput::make('amount_paid')
                            ->numeric()
                            ->default(0)
                            ->label('Amount Paid'),

                        \Filament\Forms\Components\Select::make('payment_status')
                            ->options([
                                'paid' => 'Paid',
                                'debt' => 'Debt',
                            ])
                            ->required(),

                        \Filament\Forms\Components\Toggle::make('is_exception')
                            ->label('Exceptional Case')
                            ->reactive(),

                        \Filament\Forms\Components\Textarea::make('exception_reason')
                            ->label('Reason for Exception')
                            ->visible(fn ($get) => $get('is_exception') === true),
                    ])
                    ->columns(2),
            ]);
    }
}