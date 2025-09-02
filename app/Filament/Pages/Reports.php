<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use App\Models\Reservation;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Reports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.reports';
    protected static ?string $title = 'التقارير والإحصائيات';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'التقارير';
    protected static ?int $navigationSort = 5;

    public $startDate;
    public $endDate;
    public $doctorId;
    public $status;
    
    public $totalReservations = 0;
    public $completedReservations = 0;
    public $pendingReservations = 0;
    public $cancelledReservations = 0;
    public $totalRevenue = 0;
    public $averageReservationsPerDay = 0;
    public $topDoctors = [];

    public function mount()
    {
        $this->startDate = now()->subMonth()->toDateString();
        $this->endDate = now()->toDateString();
        $this->loadReports();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('معايير التقرير')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                DatePicker::make('startDate')
                                    ->label('من تاريخ')
                                    ->default(now()->subMonth())
                                    ->displayFormat('d/m/Y')
                                    ->native(false)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state) {
                                        $this->startDate = $state;
                                        $this->loadReports();
                                    }),
                                    
                                DatePicker::make('endDate')
                                    ->label('إلى تاريخ')
                                    ->default(now())
                                    ->displayFormat('d/m/Y')
                                    ->native(false)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state) {
                                        $this->endDate = $state;
                                        $this->loadReports();
                                    }),
                                    
                                Select::make('doctorId')
                                    ->label('الطبيب')
                                    ->options(Doctor::with('user')->get()->pluck('user.name', 'id'))
                                    ->searchable()
                                    ->placeholder('جميع الأطباء')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state) {
                                        $this->doctorId = $state;
                                        $this->loadReports();
                                    }),
                                    
                                Select::make('status')
                                    ->label('حالة الحجز')
                                    ->options([
                                        'pending' => 'قيد الانتظار',
                                        'completed' => 'مكتمل',
                                        'cancelled' => 'ملغي'
                                    ])
                                    ->placeholder('جميع الحالات')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state) {
                                        $this->status = $state;
                                        $this->loadReports();
                                    }),
                            ]),
                    ]),
            ]);
    }

    public function loadReports()
    {
        $query = Reservation::with(['patient.user', 'doctor.user', 'service'])
            ->whereBetween('reservation_date', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);

        if ($this->doctorId) {
            $query->where('doctor_id', $this->doctorId);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        $reservations = $query->get();
        
        $this->totalReservations = $reservations->count();
        $this->completedReservations = $reservations->where('status', 'completed')->count();
        $this->pendingReservations = $reservations->where('status', 'pending')->count();
        $this->cancelledReservations = $reservations->where('status', 'cancelled')->count();
        $this->totalRevenue = $reservations->where('status', 'completed')->sum('service_price');
        
        $daysCount = Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate)) + 1;
        $this->averageReservationsPerDay = $daysCount > 0 ? round($this->totalReservations / $daysCount, 2) : 0;
        
        $this->topDoctors = Reservation::with('doctor.user')
            ->select('doctor_id', DB::raw('COUNT(*) as total_reservations'))
            ->whereBetween('reservation_date', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ])
            ->groupBy('doctor_id')
            ->orderByDesc('total_reservations')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'doctor' => $item->doctor->user->name,
                    'reservations' => $item->total_reservations
                ];
            });
        
        // إرسال حدث JavaScript لتحديث المخطط
        $this->dispatch('updateChart', data: [
            'completed' => $this->completedReservations,
            'pending' => $this->pendingReservations,
            'cancelled' => $this->cancelledReservations
        ]);
    }

    public function generateExcelReport()
    {
        try {
            return redirect()->route('export.reservations.excel', [
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
                'doctorId' => $this->doctorId,
                'status' => $this->status
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'danger', message: 'خطأ في إنشاء التقرير: ' . $e->getMessage());
        }
    }

    public function generatePDFReport()
    {
        try {
            return redirect()->route('export.reservations.pdf', [
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
                'doctorId' => $this->doctorId,
                'status' => $this->status
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'danger', message: 'خطأ في إنشاء التقرير: ' . $e->getMessage());
        }
    }
    
    protected function getViewData(): array
    {
        return [
            'totalReservations' => $this->totalReservations,
            'completedReservations' => $this->completedReservations,
            'pendingReservations' => $this->pendingReservations,
            'cancelledReservations' => $this->cancelledReservations,
            'totalRevenue' => $this->totalRevenue,
            'averageReservationsPerDay' => $this->averageReservationsPerDay,
            'topDoctors' => $this->topDoctors,
        ];
    }
    
    public function updated($property, $value)
    {
        if (in_array($property, ['startDate', 'endDate', 'doctorId', 'status'])) {
            $this->$property = $value;
            $this->loadReports();
        }
    }
}