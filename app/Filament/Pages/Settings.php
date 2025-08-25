<?php

namespace App\Filament\Pages;

use Filament\Forms;
use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

class Settings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament.pages.settings';
    protected static ?string $navigationLabel = 'الإعدادات';
    protected static ?string $title = 'إعدادات النظام';
    protected static ?string $navigationGroup = 'الإدارة';

    public ?array $data = [];
    public ?string $currentLogo = null;

    public function mount(): void
    {
        $settings = Setting::first();
        $this->currentLogo = $settings?->logo_path;
        $this->form->fill($settings?->toArray() ?? []);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('project_name')->label('اسم المشروع'),

                Forms\Components\ViewField::make('current_logo')
                    ->view('filament.forms.components.current-logo')
                    ->hidden(fn () => empty($this->currentLogo)),

                Forms\Components\FileUpload::make('logo_path')
                    ->label('تغيير الشعار')
                    ->image()
                    ->directory('logos')
                    ->preserveFilenames()
                    ->maxSize(1024)
                    ->disk('public')
                    ->nullable()
                    ->imagePreviewHeight('100')
                    ->downloadable()
                    ->openable(),

                Forms\Components\TextInput::make('consult_price')->numeric()->label('سعر الكشف'),
                Forms\Components\TextInput::make('followup_price')->numeric()->label('سعر الاستشارة'),
                Forms\Components\TextInput::make('followup_days')->numeric()->label('مدة الاستشارة (بالأيام)'),
            ])
            ->statePath('data');
    }

    public function save(): void
{
    $data = $this->form->getState();

    // معالجة الشعار
    if (!empty($data['logo_path']) && is_array($data['logo_path'])) {
        if ($this->currentLogo) {
            Storage::disk('public')->delete($this->currentLogo);
        }
        // يتم حفظ أول ملف فقط
        $data['logo_path'] = $data['logo_path'][0];
    } else {
        $data['logo_path'] = $this->currentLogo;
    }

    Setting::updateOrCreate([], $data);

    $this->currentLogo = $data['logo_path'];

    Notification::make()
        ->title('تم الحفظ بنجاح')
        ->success()
        ->send();
}

}
