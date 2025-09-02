<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Pages\Page; 
use Filament\Forms\Form;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Notifications\Notification;

class Settings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $title = 'إعدادات المشروع';
    protected static ?string $slug = 'settings';
    protected static ?string $navigationLabel = 'الإعدادات';
    protected static string $view = 'filament.pages.settings';
    protected static ?int $navigationSort = 9999;

    public ?array $data = [];
    public ?string $oldLogo = null;
    public ?string $oldBrand = null;

    public function mount(): void
    {
        $settings = Setting::first();

        if ($settings) {
            $this->oldLogo = $settings->project_logo;
            $this->oldBrand = $settings->project_brand;
            $this->form->fill($settings->toArray());
        } else {
            $this->oldLogo = null;
            $this->oldBrand = null;
            $this->form->fill();
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات أساسية')
                    ->schema([
                        Forms\Components\TextInput::make('project_name')
                            ->label('اسم المشروع')
                            ->required(),
                    ]),

                Forms\Components\Section::make('الشعارات')
                    ->schema([
                        Forms\Components\FileUpload::make('project_logo')
                            ->label('شعار المشروع')
                            ->image()
                            ->directory('settings')
                            ->maxSize(2048)
                            ->nullable()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file): string =>
                                'logo_' . time() . '.' . $file->getClientOriginalExtension()
                            )
                            ->disk('public'),

                        Forms\Components\FileUpload::make('project_brand')
                            ->label('الخلفية')
                            ->image()
                            ->directory('settings')
                            ->maxSize(4096)
                            ->nullable()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file): string =>
                                'brand_' . time() . '.' . $file->getClientOriginalExtension()
                            )
                            ->disk('public'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $settings = Setting::firstOrCreate([]);
            $data = $this->form->getState();

            $this->handleImageDeletion('project_logo', $this->oldLogo, $data);
            $this->handleImageDeletion('project_brand', $this->oldBrand, $data);

            $settings->update($data);

            $this->oldLogo = $settings->fresh()->project_logo;
            $this->oldBrand = $settings->fresh()->project_brand;

            Notification::make()
                ->title('تم حفظ الإعدادات بنجاح')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('حدث خطأ أثناء الحفظ')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function handleImageDeletion(string $field, ?string $oldImage, array $data): void
    {
        if (!empty($data[$field]) && $oldImage && $data[$field] !== $oldImage) {
            $this->deleteImageFile($oldImage);
        }

        if (empty($data[$field]) && $oldImage) {
            $this->deleteImageFile($oldImage);
        }
    }

    protected function deleteImageFile(?string $filePath): void
    {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }
}
