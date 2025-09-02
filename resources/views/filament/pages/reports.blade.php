<x-filament-panels::page>
    <x-filament-panels::form wire:submit="generateReport">
        {{ $this->form }}

        <div class="flex justify-end gap-4 mt-4">
            <x-filament::button type="submit">
                توليد التقرير
            </x-filament::button>
            
            <x-filament::button color="success" wire:click="exportReport">
                تصدير إلى Excel
            </x-filament::button>
        </div>
    </x-filament-panels::form>

    @if(!empty($summaryData))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
            @foreach($summaryData as $key => $value)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">
                        {{ $this->getSummaryLabel($key) }}
                    </h3>
                    <p class="text-3xl font-bold text-primary-600">
                        {{ $value }}
                    </p>
                </div>
            @endforeach
        </div>
    @endif

    @if(!empty($chartData))
        <div class="bg-white rounded-lg shadow p-6 mt-8">
            <h3 class="text-lg font-semibold mb-4">الرسم البياني</h3>
            <div wire:ignore>
                <canvas id="reportChart" width="400" height="200"></canvas>
            </div>
        </div>
    @endif

    @if(!empty($tableData))
        <div class="bg-white rounded-lg shadow p-6 mt-8">
            <h3 class="text-lg font-semibold mb-4">البيانات التفصيلية</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-gray-50">
                            @foreach(array_keys($tableData[0] ?? []) as $header)
                                <th class="px-4 py-2">{{ $this->getHeaderLabel($header) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tableData as $row)
                            <tr class="border-b">
                                @foreach($row as $cell)
                                    <td class="px-4 py-2">{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('livewire:load', function() {
                Livewire.on('reportGenerated', function() {
                    const ctx = document.getElementById('reportChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: @json(array_column($chartData, 'date')),
                            datasets: [{
                                label: 'البيانات',
                                data: @json(array_column($chartData, 'count')),
                                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    rtl: true,
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
</x-filament-panels::page>