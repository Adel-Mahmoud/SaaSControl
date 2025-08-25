<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationsChart extends ChartWidget
{
    protected static ?string $heading = 'إحصائيات الحجوزات';
    protected static ?int $sort = 2;
    
    // جعل الـ widget يشغل العرض الكامل
    protected int | string | array $columnSpan = 'full';
    
    // جعل ارتفاع الرسم البياني أكبر
    protected static ?string $maxHeight = '400px';

    public ?string $filter = 'month';

    protected function getData(): array
    {
        $data = $this->getFilteredData();
        
        return [
            'datasets' => [
                [
                    'label' => 'عدد الحجوزات',
                    'data' => $data['values'],
                    'backgroundColor' => 'rgba(79, 70, 229, 0.2)',
                    'borderColor' => 'rgb(79, 70, 229)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'اليوم',
            'week' => 'هذا الأسبوع',
            'month' => 'هذا الشهر',
        ];
    }

    private function getFilteredData(): array
    {
        $query = Reservation::query();
        $format = 'd M';
        $groupBy = 'DATE(reservation_date)'; // غيرت من created_at إلى reservation_date

        switch ($this->filter) {
            case 'today':
                $query->whereDate('reservation_date', Carbon::today());
                $format = 'H:i';
                $groupBy = 'HOUR(reservation_date)';
                break;
                
            case 'week':
                $query->whereBetween('reservation_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                $format = 'D d';
                break;
                
            case 'month':
                $query->whereBetween('reservation_date', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ]);
                $format = 'd M';
                break;
        }

        $results = $query->select(
                DB::raw($groupBy . ' as period'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $values = [];
        $labels = [];

        foreach ($results as $result) {
            $values[] = $result->count;
            
            if ($this->filter === 'today') {
                $labels[] = Carbon::createFromTime($result->period, 0, 0)->format($format);
            } else {
                $labels[] = Carbon::parse($result->period)->translatedFormat($format);
            }
        }

        return [
            'values' => $values,
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'font' => [
                            'size' => 14,
                            'family' => 'Cairo, Tahoma, sans-serif',
                        ],
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                        'font' => [
                            'size' => 12,
                            'family' => 'Cairo, Tahoma, sans-serif',
                        ],
                    ],
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
                'x' => [
                    'ticks' => [
                        'font' => [
                            'size' => 12,
                            'family' => 'Cairo, Tahoma, sans-serif',
                        ],
                    ],
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
            'layout' => [
                'padding' => [
                    'top' => 20,
                    'right' => 20,
                    'bottom' => 20,
                    'left' => 20,
                ],
            ],
        ];
    }

    // إضافة وصف للتغييرات
    public function getDescription(): ?string
    {
        $current = $this->getCurrentPeriodCount();
        $previous = $this->getPreviousPeriodCount();
        $change = $this->calculatePercentageChange($current, $previous);

        return $this->getChangeDescription($change);
    }

    private function getCurrentPeriodCount(): int
    {
        switch ($this->filter) {
            case 'today':
                return Reservation::whereDate('reservation_date', Carbon::today())->count();
                
            case 'week':
                return Reservation::whereBetween('reservation_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])->count();
                
            case 'month':
                return Reservation::whereBetween('reservation_date', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ])->count();
                
            default:
                return 0;
        }
    }

    private function getPreviousPeriodCount(): int
    {
        switch ($this->filter) {
            case 'today':
                return Reservation::whereDate('reservation_date', Carbon::yesterday())->count();
                
            case 'week':
                return Reservation::whereBetween('reservation_date', [
                    Carbon::now()->subWeek()->startOfWeek(),
                    Carbon::now()->subWeek()->endOfWeek()
                ])->count();
                
            case 'month':
                return Reservation::whereBetween('reservation_date', [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth()
                ])->count();
                
            default:
                return 0;
        }
    }

    private function calculatePercentageChange(int $current, int $previous): float
    {
        if ($previous === 0) {
            return $current > 0 ? 100 : 0;
        }

        return (($current - $previous) / $previous) * 100;
    }

    private function getChangeDescription(float $percentage): string
    {
        if ($percentage === 0) {
            return 'لا تغيير';
        }

        $formatted = number_format(abs($percentage), 1);
        $direction = $percentage > 0 ? 'زيادة' : 'انخفاض';
        $period = $this->getPeriodText();

        return "{$direction} بنسبة {$formatted}% عن {$period}";
    }

    private function getPeriodText(): string
    {
        return match($this->filter) {
            'today' => 'الأمس',
            'week' => 'الأسبوع الماضي',
            'month' => 'الشهر الماضي',
            default => 'الفترة الماضية',
        };
    }
}