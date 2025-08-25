<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use App\Models\Reservation;
use App\Models\Doctor;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Attributes\On;

class Reports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.reports';
    protected static ?string $navigationLabel = 'التقارير';
    protected static ?string $title = 'التقارير';
    protected static ?string $navigationGroup = 'تقارير';
    protected static ?string $slug = 'reports';

    public $reportType = 'daily';
    public $startDate;
    public $endDate;
    public $doctorId = null;
    public $serviceId = null;
    public $reportData = [];
    public $totalAmount = 0;
    public $totalReservations = 0;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->endOfMonth()->toDateString();
        $this->generateReport();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('معايير التقرير')
                    ->schema([
                        Select::make('reportType')
                            ->label('نوع التقرير')
                            ->options([
                                'daily' => 'يومي',
                                'monthly' => 'شهري', 
                                'yearly' => 'سنوي',
                                'custom' => 'مخصص',
                                'all' => 'جميع الحجوزات' // أضفنا خيار جميع الحجوزات
                            ])
                            ->reactive()
                            ->required()
                            ->default('daily'),

                        DatePicker::make('startDate')
                            ->label('من تاريخ')
                            ->required()
                            ->visible(fn($get) => $get('reportType') === 'custom'),

                        DatePicker::make('endDate')
                            ->label('إلى تاريخ')
                            ->required()
                            ->visible(fn($get) => $get('reportType') === 'custom'),

                        Select::make('doctorId')
                            ->label('تصفية حسب الطبيب')
                            ->options(Doctor::pluck('name', 'id'))
                            ->searchable()
                            ->nullable()
                            ->placeholder('جميع الأطباء'),

                        Select::make('serviceId')
                            ->label('تصفية حسب الخدمة')
                            ->options(Service::pluck('name', 'id'))
                            ->searchable()
                            ->nullable()
                            ->placeholder('جميع الخدمات'),
                    ])
                    ->columns(2),
            ]);
    }

    public function generateReport()
    {
        // بداية بناء الاستعلام
        $query = Reservation::with(['patient', 'doctor', 'service']);
            
        // تطبيق الفلتر حسب نوع التقرير
        switch ($this->reportType) {
            case 'daily':
                $query->whereDate('reservation_date', now()->toDateString());
                break;
                
            case 'monthly':
                $query->whereBetween('reservation_date', [
                    now()->startOfMonth(),
                    now()->endOfMonth()
                ]);
                break;
                
            case 'yearly':
                $query->whereYear('reservation_date', now()->year);
                break;
                
            case 'custom':
                if ($this->startDate && $this->endDate) {
                    $query->whereBetween('reservation_date', [
                        $this->startDate . ' 00:00:00',
                        $this->endDate . ' 23:59:59'
                    ]);
                }
                break;
                
            case 'all':
                // لا نضيف أي شرط تاريخ - جميع الحجوزات
                break;
        }

        // تطبيق الفلتر حسب الطبيب
        if ($this->doctorId) {
            $query->where('doctor_id', $this->doctorId);
        }

        // تطبيق الفلتر حسب الخدمة
        if ($this->serviceId) {
            $query->where('service_id', $this->serviceId);
        }

        // جلب البيانات
        $this->reportData = $query->orderBy('reservation_date', 'desc')->get();
        
        // حساب الإحصائيات
        $this->totalReservations = $this->reportData->count();
        $this->totalAmount = $this->reportData->sum('service_price');
        
        // Debug: عرض عدد الحجوزات المسترجعة
        \Log::info('عدد الحجوزات المسترجعة: ' . $this->totalReservations);
        \Log::info('معايير البحث:', [
            'reportType' => $this->reportType,
            'doctorId' => $this->doctorId,
            'serviceId' => $this->serviceId,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ]);
    }

    // دالة مساعدة لعرض البيانات المجمعة
    public function getGroupedData()
    {
        if ($this->reportData->isEmpty()) {
            return collect();
        }

        $grouped = [];

        switch ($this->reportType) {
            case 'daily':
                $grouped = $this->reportData->groupBy(function ($item) {
                    return Carbon::parse($item->reservation_date)->format('H:i');
                });
                break;
                
            case 'monthly':
                $grouped = $this->reportData->groupBy(function ($item) {
                    return Carbon::parse($item->reservation_date)->format('Y-m-d');
                });
                break;
                
            case 'yearly':
                $grouped = $this->reportData->groupBy(function ($item) {
                    return Carbon::parse($item->reservation_date)->format('Y-m');
                });
                break;
                
            default:
                $grouped = $this->reportData->groupBy(function ($item) {
                    return Carbon::parse($item->reservation_date)->format('Y-m-d');
                });
        }

        return $grouped;
    }

    // إحصائيات الأطباء
    public function getDoctorStats()
    {
        if ($this->reportData->isEmpty()) {
            return collect();
        }

        return $this->reportData->groupBy('doctor.name')->map(function ($group) {
            return [
                'count' => $group->count(),
                'amount' => $group->sum('service_price')
            ];
        });
    }

    // إحصائيات الخدمات
    public function getServiceStats()
    {
        if ($this->reportData->isEmpty()) {
            return collect();
        }

        return $this->reportData->groupBy('service.name')->map(function ($group) {
            return [
                'count' => $group->count(),
                'amount' => $group->sum('service_price')
            ];
        });
    }

    // تحديث التقرير عند تغيير المعايير
    public function updated($property)
    {
        if (in_array($property, ['reportType', 'startDate', 'endDate', 'doctorId', 'serviceId'])) {
            $this->generateReport();
        }
    }

    public static function getNavigationSort(): ?int
    {
        return 6;
    }
}