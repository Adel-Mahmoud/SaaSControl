<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use App\Models\Visit;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditPatient extends EditRecord
{
    protected static string $resource = PatientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // public function form(Form $form): Form
    // {
    //     $visit = Visit::where('patient_id', $this->record->id)->first();

    //     return $form
    //         ->schema([
    //             \Filament\Forms\Components\Section::make('Patient Information')
    //                 ->schema(PatientResource::getPatientForm())
    //                 ->columns(2),

    //             \Filament\Forms\Components\Section::make('Visit Information')
    //                 ->schema([
    //                     \Filament\Forms\Components\Select::make('visit_type')
    //                         ->options([
    //                             'new' => 'New Visit',
    //                             'followup' => 'Follow-up',
    //                         ])
    //                         ->required()
    //                         ->default($visit->visit_type ?? null),

    //                     \Filament\Forms\Components\DateTimePicker::make('visit_date')
    //                         ->required()
    //                         ->default($visit->visit_date ?? now()),

    //                     \Filament\Forms\Components\TextInput::make('amount_due')
    //                         ->numeric()
    //                         ->required()
    //                         ->label('Amount Due')
    //                         ->default($visit->amount_due ?? null),

    //                     \Filament\Forms\Components\TextInput::make('amount_paid')
    //                         ->numeric()
    //                         ->default($visit->amount_paid ?? 0)
    //                         ->label('Amount Paid'),

    //                     \Filament\Forms\Components\Select::make('payment_status')
    //                         ->options([
    //                             'paid' => 'Paid',
    //                             'debt' => 'Debt',
    //                         ])
    //                         ->required()
    //                         ->default($visit->payment_status ?? null),

    //                     \Filament\Forms\Components\Toggle::make('is_exception')
    //                         ->label('Exceptional Case')
    //                         ->reactive()
    //                         ->default($visit->is_exception ?? false),

    //                     \Filament\Forms\Components\Textarea::make('exception_reason')
    //                         ->label('Reason for Exception')
    //                         ->default($visit->exception_reason ?? null)
    //                         ->visible(fn ($get) => $get('is_exception') === true),
    //                 ])
    //                 ->columns(2),
    //         ]);
    // }

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     $visitData = [
    //         'visit_type' => $data['visit_type'],
    //         'visit_date' => $data['visit_date'],
    //         'amount_due' => $data['amount_due'],
    //         'amount_paid' => $data['amount_paid'],
    //         'payment_status' => $data['payment_status'],
    //         'is_exception' => $data['is_exception'] ?? false,
    //         'exception_reason' => $data['exception_reason'] ?? null,
    //     ];

    //     unset(
    //         $data['visit_type'],
    //         $data['visit_date'],
    //         $data['amount_due'],
    //         $data['amount_paid'],
    //         $data['payment_status'],
    //         $data['is_exception'],
    //         $data['exception_reason']
    //     );

    //     return $data;
    // }

    // protected function afterSave(): void
    // {
    //     $visit = Visit::where('patient_id', $this->record->id)->first();

    //     if ($visit) {
    //         $visit->update([
    //             'visit_type' => $this->data['visit_type'],
    //             'visit_date' => $this->data['visit_date'],
    //             'amount_due' => $this->data['amount_due'],
    //             'amount_paid' => $this->data['amount_paid'],
    //             'payment_status' => $this->data['payment_status'],
    //             'is_exception' => $this->data['is_exception'] ?? false,
    //             'exception_reason' => $this->data['exception_reason'] ?? null,
    //         ]);
    //     } else {
    //         Visit::create(array_merge(
    //             [
    //                 'patient_id' => $this->record->id
    //             ],
    //             [
    //                 'visit_type' => $this->data['visit_type'],
    //                 'visit_date' => $this->data['visit_date'],
    //                 'amount_due' => $this->data['amount_due'],
    //                 'amount_paid' => $this->data['amount_paid'],
    //                 'payment_status' => $this->data['payment_status'],
    //                 'is_exception' => $this->data['is_exception'] ?? false,
    //                 'exception_reason' => $this->data['exception_reason'] ?? null,
    //             ]
    //         ));
    //     }
    // }
}
