<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['type'] === 'image' && !empty($data['image_upload'])) {
            $data['value'] = is_array($data['image_upload']) ? $data['image_upload'][0] : $data['image_upload'];
        } elseif ($data['type'] === 'boolean') {
            $data['value'] = (!empty($data['bool_upload']) || $data['bool_upload'] === true) ? '1' : '0';
        }
        
        unset($data['image_upload'], $data['bool_upload']);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $data = $this->form->getState();
        $setting = $this->record;
        
        if ($data['type'] === 'image' && !empty($data['image_upload'])) {
            $setting->value = is_array($data['image_upload']) ? $data['image_upload'][0] : $data['image_upload'];
            $setting->save();
        }
    }
}