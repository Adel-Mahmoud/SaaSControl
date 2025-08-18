<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Models\Visit;
use App\Models\Patient;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PatientResource;

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;

    protected array $visitData = [];

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Patient
    {
        $patient = Patient::where('name', $data['name'])
            ->where('phone', $data['phone'])
            ->first();

        if ($patient) {
            if (isset($data['visits']) && is_array($data['visits'])) {
                foreach ($data['visits'] as $visit) {
                    $patient->visits()->create($visit);
                }
            }
            return $patient;
        }

        $patient = Patient::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
        ]);

        if (isset($data['visits']) && is_array($data['visits'])) {
            foreach ($data['visits'] as $visit) {
                $patient->visits()->create($visit);
            }
        }

        return $patient;
    }
}