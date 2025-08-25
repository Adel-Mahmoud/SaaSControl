<?php

namespace App\Filament\Pages;

use Filament\Forms;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Service;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\Reservation;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;

class ManageReservations extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.manage-reservations';
    protected static ?string $title = 'إدارة الحجوزات';
    protected static ?string $navigationGroup = 'الحجوزات';

    public ?array $data = [];
    public ?Patient $selectedPatient = null;
    public ?Doctor $selectedDoctor = null;
    public $showPatientModal = false;
    public $showDoctorModal = false;
    public $showWaitingCountModal = false;

    public $searchName;
    public $searchFrom;
    public $searchTo;
    public $searchDate;
    public $reservations = [];
    public $waitingCount = 0;

    public function mount()
    {
        $this->form->fill();
        $this->searchDate = now()->toDateString();
        $this->loadReservations();
    }

    public function loadReservations()
    {
        $query = Reservation::with(['patient.user', 'doctor.user', 'service']);

        if ($this->searchName) {
            $query->whereHas('patient.user', function ($q) {
                $q->where('name', 'like', "%{$this->searchName}%")
                    ->orWhere('phone', 'like', "%{$this->searchName}%");
            });
        }

        if ($this->searchDate) {
            $query->whereDate('reservation_date', $this->searchDate);
        }

        if ($this->searchFrom && $this->searchTo) {
            $query->whereBetween('reservation_date', [
                Carbon::parse($this->searchFrom)->startOfDay(),
                Carbon::parse($this->searchTo)->endOfDay()
            ]);
        }

        $this->reservations = $query->orderBy('reservation_number')->get();
    }

    public function search()
    {
        $this->loadReservations();
    }

    public function showPatient($id)
    {
        $this->selectedPatient = Patient::with('user')->find($id);
        $this->showPatientModal = true;
    }

    public function showDoctor($id)
    {
        $this->selectedDoctor = Doctor::with('user')->find($id);
        $this->showDoctorModal = true;
    }

    public function showWaitingCount($id)
    {
        $reservation = Reservation::with(['patient.user', 'doctor.user'])->find($id);

        if ($reservation) {
            $this->waitingCount = Reservation::whereDate('reservation_date', $reservation->reservation_date)
                ->where('status', 'pending')
                ->where('reservation_number', '<', $reservation->reservation_number)
                ->count();

            $this->showWaitingCountModal = true;
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Section::make('بيانات المريض')
                    ->schema([
                        Select::make('patient_id')
                            ->label('المريض')
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return Patient::query()
                                    ->whereHas('user', function ($q) use ($search) {
                                        $q->where('name', 'like', "%{$search}%")
                                            ->orWhere('phone', 'like', "%{$search}%");
                                    })
                                    ->with('user')
                                    ->limit(10)
                                    ->get()
                                    ->mapWithKeys(fn($patient) => [
                                        $patient->id => $patient->user->name . ' - ' . $patient->user->phone
                                    ])
                                    ->toArray();
                            })
                            ->getOptionLabelUsing(function ($value) {
                                if (!$value) return null;
                                $patient = Patient::with('user')->find($value);
                                return $patient ? ($patient->user->name . ' - ' . $patient->user->phone) : null;
                            })
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('الاسم')
                                    ->required(),
                                TextInput::make('phone')
                                    ->label('الهاتف')
                                    ->required(),
                                TextInput::make('address')
                                    ->label('العنوان'),
                            ])
                            ->createOptionUsing(function (array $data) {
                                $user = User::firstOrCreate(
                                    ['phone' => $data['phone']],
                                    [
                                        'name'     => $data['name'],
                                        'email'    => $data['phone'] . '@example.com',
                                        'password' => bcrypt('00000000'),
                                    ]
                                );

                                $patient = Patient::firstOrCreate(
                                    ['user_id' => $user->id],
                                    ['address' => $data['address'] ?? null]
                                );

                                return $patient->id;
                            })
                            ->required()
                            ->reactive(),
                    ]),

                Section::make('بيانات الحجز')
                    ->schema([
                        DatePicker::make('reservation_date') 
                            ->label('تاريخ الحجز')
                            ->default(now()->toDateString()) 
                            ->required()
                            ->displayFormat('d/m/Y') 
                            ->native(false),
                    ]),

                Section::make('الخدمة')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('doctor_id')
                                ->label('اختر الطبيب')
                                ->options(Doctor::with('user')->get()->pluck('user.name', 'id'))
                                ->required()
                                ->searchable()
                                ->reactive(),

                            Select::make('service_id')
                                ->label('اختر الخدمة')
                                ->options(function (callable $get) {
                                    $doctorId = $get('doctor_id');
                                    if ($doctorId) {
                                        return Service::where('doctor_id', $doctorId)
                                            ->pluck('name', 'id');
                                    }
                                    return [];
                                })
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state) {
                                        $service = Service::find($state);
                                        if ($service) {
                                            $set('price', $service->price);
                                        }
                                    } else {
                                        $set('price', null);
                                    }
                                }),
                        ]),

                        TextInput::make('price')
                            ->label('سعر الخدمة')
                            ->disabled()
                            ->dehydrated(true),
                    ])
            ]);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $patient = Patient::find($data['patient_id']);

        if (!$patient) {
            Notification::make()
                ->title('خطأ في بيانات المريض')
                ->danger()
                ->send();
            return;
        }

        $reservationDate = Carbon::parse($data['reservation_date']);
        $maxReservationNumber = Reservation::whereDate('reservation_date', $reservationDate->toDateString())
            ->max('reservation_number');

        $reservationNumber = ($maxReservationNumber ?: 0) + 1;

        Reservation::create([
            'patient_id' => $patient->id,
            'doctor_id' => $data['doctor_id'],
            'service_id' => $data['service_id'],
            'service_price' => $data['price'],
            'status' => 'pending',
            'reservation_date' => $data['reservation_date'],
            'reservation_number' => $reservationNumber,
        ]);

        $this->form->fill();
        $this->loadReservations();

        Notification::make()
            ->title('تم حفظ الحجز بنجاح')
            ->success()
            ->send();
    }

    public function updateStatus($id, $status)
    {
        $reservation = Reservation::find($id);
        if ($reservation) {
            $reservation->update(['status' => $status]);
            $this->loadReservations();

            Notification::make()
                ->title('تم تحديث حالة الحجز')
                ->success()
                ->send();
        }
    }
}
