<x-filament::page>
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-4">
            {{ $this->form }}
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-blue-100 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="mr-3">
                        <p class="text-sm font-medium text-gray-600">إجمالي الحجوزات</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalReservations }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-green-100 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="mr-3">
                        <p class="text-sm font-medium text-gray-600">الحجوزات المكتملة</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $completedReservations }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-yellow-100 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mr-3">
                        <p class="text-sm font-medium text-gray-600">قيد الانتظار</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $pendingReservations }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-purple-100 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mr-3">
                        <p class="text-sm font-medium text-gray-600">إجمالي الإيرادات</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalRevenue, 2) }} ر.س</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-lg font-bold mb-4">توزيع الحجوزات حسب الحالة</h3>
                <div class="w-full h-64">
                    <canvas id="statusChart" width="400" height="200"></canvas>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-lg font-bold mb-4">أفضل الأطباء حسب عدد الحجوزات</h3>
                <div class="space-y-3">
                    @forelse($topDoctors as $doctor)
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium">{{ $doctor['doctor'] }}</span>
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full">
                            {{ $doctor['reservations'] }} حجز
                        </span>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center">لا توجد بيانات</p>
                    @endforelse
                </div>
            </div>
        </div>
        
        <div class="flex justify-end space-x-3">
            <button wire:click="generateExcelReport" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200 active:bg-green-600 disabled:opacity-25 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                تصدير إلى Excel
            </button>
            
            <button wire:click="generatePDFReport" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-600 disabled:opacity-25 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                تصدير إلى PDF
            </button>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            window.statusChart = null;

            function initChart() {
                const ctx = document.getElementById('statusChart');
                if (!ctx) return;
                
                if (window.statusChart) {
                    window.statusChart.destroy();
                }
                
                window.statusChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['مكتمل', 'قيد الانتظار', 'ملغي'],
                        datasets: [{
                            data: [
                                {{ $completedReservations }},
                                {{ $pendingReservations }},
                                {{ $cancelledReservations }}
                            ],
                            backgroundColor: [
                                'rgb(34, 197, 94)',
                                'rgb(250, 204, 21)',
                                'rgb(239, 68, 68)'
                            ],
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                rtl: true,
                                labels: {
                                    font: {
                                        family: 'Tahoma, Arial, sans-serif'
                                    }
                                }
                            }
                        }
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                initChart();
            });

            Livewire.on('updateChart', (data) => {
                if (window.statusChart) {
                    window.statusChart.data.datasets[0].data = [
                        data.completed || 0,
                        data.pending || 0,
                        data.cancelled || 0
                    ];
                    window.statusChart.update();
                } else {
                    initChart();
                }
            });

            document.addEventListener('livewire:load', function() {
                initChart();
            });
        </script>
    @endpush
</x-filament::page>