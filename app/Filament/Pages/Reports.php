<?php

namespace App\Filament\Pages;

use App\Models\Reservation;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Service;
use Filament\Pages\Page;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Reports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.reports';
    protected static ?string $title = 'التقارير';
    protected static ?string $navigationGroup = 'التقارير والإحصاءات';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    public ?array $data = [];
    public $reportType = 'reservations';
    public $chartData = [];
    public $summaryData = [];
    public $tableData = [];

    public function mount()
    {
        $this->form->fill([
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->toDateString(),
            'doctor_id' => null,
            'status' => null,
        ]);
        $this->generateReport();
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Section::make('معايير التقرير')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('report_type')
                                ->label('نوع التقرير')
                                ->options([
                                    'reservations' => 'تقرير الحجوزات',
                                    'revenue' => 'تقرير الإيرادات',
                                    'patients' => 'تقرير المرضى',
                                    'doctors' => 'تقرير الأطباء',
                                ])
                                ->reactive()
                                ->required(),

                            DatePicker::make('start_date')
                                ->label('من تاريخ')
                                ->required()
                                ->displayFormat('d/m/Y')
                                ->native(false),

                            DatePicker::make('end_date')
                                ->label('إلى تاريخ')
                                ->required()
                                ->displayFormat('d/m/Y')
                                ->native(false),
                        ]),

                        Grid::make(2)->schema([
                            Select::make('doctor_id')
                                ->label('الطبيب')
                                ->options(Doctor::with('user')->get()->pluck('user.name', 'id'))
                                ->searchable()
                                ->nullable(),

                            Select::make('status')
                                ->label('حالة الحجز')
                                ->options([
                                    'pending' => 'قيد الانتظار',
                                    'confirmed' => 'مؤكد',
                                    'completed' => 'مكتمل',
                                    'cancelled' => 'ملغي',
                                ])
                                ->nullable()
                                ->visible(fn ($get) => $get('report_type') === 'reservations'),
                        ]),
                    ]),
            ]);
    }

    public function generateReport()
    {
        $data = $this->form->getState();
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        switch ($data['report_type']) {
            case 'reservations':
                $this->generateReservationsReport($startDate, $endDate, $data);
                break;
            case 'revenue':
                $this->generateRevenueReport($startDate, $endDate, $data);
                break;
            case 'patients':
                $this->generatePatientsReport($startDate, $endDate);
                break;
            case 'doctors':
                $this->generateDoctorsReport($startDate, $endDate);
                break;
        }
    }

    private function generateReservationsReport($startDate, $endDate, $filters)
    {
        $query = Reservation::with(['patient.user', 'doctor.user', 'service'])
            ->whereBetween('reservation_date', [$startDate, $endDate]);

        if ($filters['doctor_id']) {
            $query->where('doctor_id', $filters['doctor_id']);
        }

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        // Chart data - reservations by day
        $chartQuery = clone $query;
        $this->chartData = $chartQuery->select(
            DB::raw('DATE(reservation_date) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();

        // Summary data
        $this->summaryData = [
            'total_reservations' => $query->count(),
            'pending' => $query->clone()->where('status', 'pending')->count(),
            'confirmed' => $query->clone()->where('status', 'confirmed')->count(),
            'completed' => $query->clone()->where('status', 'completed')->count(),
            'cancelled' => $query->clone()->where('status', 'cancelled')->count(),
        ];

        // Table data
        $this->tableData = $query->orderBy('reservation_date', 'desc')
            ->get()
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'patient' => $reservation->patient->user->name,
                    'doctor' => $reservation->doctor->user->name,
                    'service' => $reservation->service->name,
                    'date' => $reservation->reservation_date->format('d/m/Y'),
                    'status' => $reservation->status,
                    'price' => $reservation->service_price,
                ];
            });
    }

    private function generateRevenueReport($startDate, $endDate, $filters)
    {
        $query = Reservation::with(['doctor.user', 'service'])
            ->whereBetween('reservation_date', [$startDate, $endDate])
            ->where('status', 'completed');

        if ($filters['doctor_id']) {
            $query->where('doctor_id', $filters['doctor_id']);
        }

        // Chart data - revenue by day
        $chartQuery = clone $query;
        $this->chartData = $chartQuery->select(
            DB::raw('DATE(reservation_date) as date'),
            DB::raw('SUM(service_price) as revenue')
        )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();

        // Summary data
        $this->summaryData = [
            'total_revenue' => $query->sum('service_price'),
            'total_reservations' => $query->count(),
            'average_revenue' => $query->avg('service_price'),
        ];

        // Table data - revenue by doctor
        $this->tableData = $query->select(
            'doctor_id',
            DB::raw('SUM(service_price) as total_revenue'),
            DB::raw('COUNT(*) as reservations_count')
        )
            ->groupBy('doctor_id')
            ->with('doctor.user')
            ->get()
            ->map(function ($item) {
                return [
                    'doctor' => $item->doctor->user->name,
                    'reservations' => $item->reservations_count,
                    'revenue' => $item->total_revenue,
                ];
            });
    }

    private function generatePatientsReport($startDate, $endDate)
    {
        $query = Patient::with(['user', 'reservations'])
            ->whereHas('reservations', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('reservation_date', [$startDate, $endDate]);
            });

        // Chart data - patients by registration date
        $this->chartData = Patient::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();

        // Summary data
        $this->summaryData = [
            'total_patients' => $query->count(),
            'new_patients' => Patient::whereBetween('created_at', [$startDate, $endDate])->count(),
            'active_patients' => $query->distinct()->count(),
        ];

        // Table data - patients with reservation count
        $this->tableData = $query->withCount(['reservations' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('reservation_date', [$startDate, $endDate]);
        }])
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'name' => $patient->user->name,
                    'phone' => $patient->user->phone,
                    'reservations_count' => $patient->reservations_count,
                    'last_visit' => $patient->reservations->max('reservation_date')?->format('d/m/Y'),
                ];
            });
    }

    private function generateDoctorsReport($startDate, $endDate)
    {
        $query = Doctor::with(['user', 'reservations' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('reservation_date', [$startDate, $endDate]);
        }]);

        // Chart data - doctors by performance
        $this->chartData = $query->withCount(['reservations' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('reservation_date', [$startDate, $endDate])
                ->where('status', 'completed');
        }])
            ->get()
            ->map(function ($doctor) {
                return [
                    'doctor' => $doctor->user->name,
                    'reservations' => $doctor->reservations_count,
                ];
            })
            ->toArray();

        // Summary data
        $this->summaryData = [
            'total_doctors' => $query->count(),
            'active_doctors' => $query->whereHas('reservations')->count(),
            'total_appointments' => $query->withCount('reservations')->get()->sum('reservations_count'),
        ];

        // Table data - doctors with performance metrics
        $this->tableData = $query->withCount([
            'reservations',
            'reservations as completed_reservations' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('reservation_date', [$startDate, $endDate])
                    ->where('status', 'completed');
            },
            'reservations as revenue' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('reservation_date', [$startDate, $endDate])
                    ->where('status', 'completed')
                    ->select(DB::raw('SUM(service_price)'));
            }
        ])
            ->get()
            ->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->user->name,
                    'total_reservations' => $doctor->reservations_count,
                    'completed_reservations' => $doctor->completed_reservations,
                    'revenue' => $doctor->revenue,
                    'specialization' => $doctor->specialization,
                ];
            });
    }

    public function exportReport()
    {
        $data = $this->form->getState();
        $fileName = 'report_' . $data['report_type'] . '_' . now()->format('Y-m-d') . '.xlsx';

        // Here you would implement the export logic using Laravel Excel
        // For now, we'll just show a notification
        Notification::make()
            ->title('جاري تصدير التقرير')
            ->body('سيتم تحميل الملف خلال ثوانٍ')
            ->success()
            ->send();

        // Return download response (pseudo-code)
        // return Excel::download(new ReportExport($this->tableData), $fileName);
    }
}