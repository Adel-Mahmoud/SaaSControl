<x-filament-panels::page>
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">معايير التقرير</h2>
        {{ $this->form }}
        
        <div class="mt-4">
            <x-filament::button wire:click="generateReport" icon="heroicon-o-chart-bar" class="w-full">
                توليد التقرير
            </x-filament::button>
        </div>
    </div>

    @if($totalReservations > 0)
        <!-- الإحصائيات العامة -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 text-center">
                <h3 class="text-lg font-semibold text-blue-800">عدد الحجوزات</h3>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($totalReservations) }}</p>
            </div>
            
            <div class="bg-green-50 p-4 rounded-lg border border-green-200 text-center">
                <h3 class="text-lg font-semibold text-green-800">إجمالي المبالغ</h3>
                <p class="text-2xl font-bold text-green-600">{{ number_format($totalAmount, 2) }} ر.س</p>
            </div>
            
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200 text-center">
                <h3 class="text-lg font-semibold text-purple-800">متوسط الحجز</h3>
                <p class="text-2xl font-bold text-purple-600">
                    {{ $totalReservations > 0 ? number_format($totalAmount / $totalReservations, 2) : 0 }} ر.س
                </p>
            </div>
        </div>

        <!-- تقرير الأطباء -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">التقرير حسب الأطباء</h3>
            @if($this->getDoctorStats()->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2">الطبيب</th>
                                <th class="px-4 py-2">عدد الحجوزات</th>
                                <th class="px-4 py-2">الإجمالي</th>
                                <th class="px-4 py-2">النسبة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->getDoctorStats() as $doctor => $stats)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $doctor }}</td>
                                <td class="px-4 py-2">{{ number_format($stats['count']) }}</td>
                                <td class="px-4 py-2">{{ number_format($stats['amount'], 2) }} ر.س</td>
                                <td class="px-4 py-2">
                                    {{ $totalAmount > 0 ? number_format(($stats['amount'] / $totalAmount) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">لا توجد بيانات للأطباء</p>
            @endif
        </div>

        <!-- تقرير الخدمات -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">التقرير حسب الخدمات</h3>
            @if($this->getServiceStats()->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2">الخدمة</th>
                                <th class="px-4 py-2">عدد الحجوزات</th>
                                <th class="px-4 py-2">الإجمالي</th>
                                <th class="px-4 py-2">النسبة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->getServiceStats() as $service => $stats)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $service }}</td>
                                <td class="px-4 py-2">{{ number_format($stats['count']) }}</td>
                                <td class="px-4 py-2">{{ number_format($stats['amount'], 2) }} ر.س</td>
                                <td class="px-4 py-2">
                                    {{ $totalAmount > 0 ? number_format(($stats['amount'] / $totalAmount) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">لا توجد بيانات للخدمات</p>
            @endif
        </div>

        <!-- تفاصيل الحجوزات -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">تفاصيل الحجوزات ({{ $totalReservations }})</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2">#</th>
                            <th class="px-4 py-2">المريض</th>
                            <th class="px-4 py-2">الطبيب</th>
                            <th class="px-4 py-2">الخدمة</th>
                            <th class="px-4 py-2">التاريخ</th>
                            <th class="px-4 py-2">المبلغ</th>
                            <th class="px-4 py-2">الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $reservation)
                        <tr class="border-b">
                            <td class="px-4 py-2">#{{ $reservation->reservation_number }}</td>
                            <td class="px-4 py-2">{{ $reservation->patient->name }}</td>
                            <td class="px-4 py-2">{{ $reservation->doctor->name }}</td>
                            <td class="px-4 py-2">{{ $reservation->service->name }}</td>
                            <td class="px-4 py-2">{{ $reservation->reservation_date->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-2">{{ number_format($reservation->service_price, 2) }} ر.س</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $reservation->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($reservation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $reservation->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @else
        <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-200 text-center">
            <p class="text-yellow-800">⚠️ لا توجد حجوزات تطابق معايير البحث</p>
            <p class="text-yellow-600 mt-2">جرب تغيير معايير البحث أو اختيار نوع تقرير مختلف</p>
        </div>
    @endif
</x-filament-panels::page>