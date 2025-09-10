<x-filament::page>
    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
        <div class="p-4 bg-white dark:bg-gray-900 shadow rounded">
            {{ $this->form }}
            <x-filament::button wire:click="create" class="mt-6">
                إضافة الحجز
            </x-filament::button>
        </div>
    </div>

    <div class="mt-6 bg-white dark:bg-gray-900 p-6 rounded border border-gray-200 dark:border-gray-700">
        <h3 class="text-xl font-bold mb-6 text-indigo-700 dark:text-indigo-400 flex items-center gap-2">
            <x-heroicon-o-magnifying-glass class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
            البحث المتقدم
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">اسم المريض أو الهاتف</label>
                <input type="text" wire:model.defer="searchName"
                    placeholder="ادخل اسم المريض أو الهاتف"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 bg-white dark:bg-gray-800 dark:text-gray-200">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">تاريخ اليوم</label>
                <input type="date" wire:model.defer="searchDate"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 bg-white dark:bg-gray-800 dark:text-gray-200">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">من تاريخ</label>
                <input type="date" wire:model.defer="searchFrom"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 bg-white dark:bg-gray-800 dark:text-gray-200">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">إلى تاريخ</label>
                <input type="date" wire:model.defer="searchTo"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 bg-white dark:bg-gray-800 dark:text-gray-200">
            </div>
        </div>

        <div class="mt-6 flex gap-3 justify-start">
            <x-filament::button wire:click="search">
                بحث
            </x-filament::button>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach ($this->reservations as $reservation)
        @php
        $waiting = \App\Models\Reservation::whereDate('reservation_date', $reservation->reservation_date)
        ->where('status','pending')
        ->where('reservation_number','<',$reservation->reservation_number)
            ->count();
            @endphp

            <div class="p-4 bg-white dark:bg-gray-900 shadow rounded border border-gray-200 dark:border-gray-700 hover:shadow-lg transition relative">
                <div class="flex items-center justify-between mb-2">
                    <div class="font-bold text-lg text-blue-600 dark:text-blue-400">
                        {{ $reservation->patient->user->name ?? 'غير معروف' }}
                    </div>
                    <button wire:click="showPatient({{ $reservation->patient->id }})"
                        class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                        <x-heroicon-o-eye class="w-5 h-5" />
                    </button>
                </div>

                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm text-gray-600 dark:text-gray-300">
                        دكتور: {{ $reservation->doctor->user->name ?? 'غير معروف' }}
                    </div>
                    <button wire:click="showDoctor({{ $reservation->doctor->id }})"
                        class="text-green-500 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300">
                        <x-heroicon-o-eye class="w-5 h-5" />
                    </button>
                </div>

                <div class="mt-1 dark:text-gray-300">
                    <strong>الدور:</strong> {{ $reservation->reservation_number }}
                </div>

                @if($reservation->status == 'pending')
                <div class="mt-1 dark:text-gray-300">
                    <strong>المتبقي على الدور:</strong> {{ $waiting }}
                </div>
                @endif

                <div class="mt-3 flex items-center justify-between dark:text-gray-300">
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 rounded-full border border-gray-300 dark:border-gray-600"
                            style="background-color: 
                @if($reservation->status == 'pending') #f59e0b
                @elseif($reservation->status == 'completad') #10b981
                @elseif($reservation->status == 'cancelled') #ef4444
                @endif"
                            title="@if($reservation->status == 'pending') قيد الانتظار
                    @elseif($reservation->status == 'completad') مؤكد
                    @elseif($reservation->status == 'cancelled') ملغي
                    @endif">
                        </div>
                        &nbsp;
                        <span class="font-semibold">الحالة:</span>
                    </div>

                    <select wire:change="updateStatus({{ $reservation->id }}, $event.target.value)"
                        class="border rounded px-3 py-1 text-sm bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 w-32">
                        <option value="pending" @selected($reservation->status == 'pending')>قيد الانتظار</option>
                        <option value="completad" @selected($reservation->status == 'completad')>مؤكد</option>
                        <option value="cancelled" @selected($reservation->status == 'cancelled')>ملغي</option>
                    </select>
                </div>

                <div class="mt-2 dark:text-gray-300">
                    <strong>السعر:</strong> {{ number_format($reservation->service_price) }} ج.م
                </div>
            </div>
            @endforeach
    </div>

    <!-- Patient Modal -->
    <div x-data="{ open: @entangle('showPatientModal') }" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
        <div class="absolute inset-0 bg-black/50" @click="open = false"></div>
        <div class="relative bg-white dark:bg-gray-900 w-full max-w-md mx-auto rounded-2xl shadow-xl p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">بيانات المريض</h2>
            @if($selectedPatient)
            <div class="space-y-3 text-gray-800 dark:text-gray-300">
                <p><span class="font-semibold">الاسم:</span> {{ $selectedPatient->user->name ?? 'غير معروف' }}</p>
                <p><span class="font-semibold">الهاتف:</span> {{ $selectedPatient->user->phone ?? 'غير معروف' }}</p>
                <p><span class="font-semibold">العنوان:</span> {{ $selectedPatient->patient->address ?? 'غير معروف' }}</p>
                <!-- <p><span class="font-semibold">تاريخ الحجز :</span> {{ $selectedPatient->reservation_date ?? 'غير معروف' }}</p> -->
            </div>
            @endif
            <div class="mt-6 text-right">
                <x-filament::button color="gray" wire:click="$set('showPatientModal', false)">إغلاق</x-filament::button>
            </div>
        </div>
    </div>

    <!-- Doctor Modal -->
    <div x-data="{ open: @entangle('showDoctorModal') }" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
        <div class="absolute inset-0 bg-black/50" @click="open = false"></div>
        <div class="relative bg-white dark:bg-gray-900 w-full max-w-md mx-auto rounded-2xl shadow-xl p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">بيانات الدكتور</h2>
            @if($selectedDoctor)
            <div class="space-y-3 text-gray-800 dark:text-gray-300">
                <p><span class="font-semibold">الاسم:</span> {{ $selectedDoctor->user->name ?? 'غير معروف' }}</p>
                <p><span class="font-semibold">التخصص:</span> {{ $selectedDoctor->specialization ?? 'غير معروف' }}</p>
                <p><span class="font-semibold">الهاتف:</span> {{ $selectedDoctor->user->phone ?? 'غير معروف' }}</p>
                <p><span class="font-semibold">البريد الإلكتروني:</span> {{ $selectedDoctor->user->email ?? 'غير معروف' }}</p>
            </div>
            @endif
            <div class="mt-6 text-right">
                <x-filament::button color="gray" wire:click="$set('showDoctorModal', false)">إغلاق</x-filament::button>
            </div>
        </div>
    </div>

    <!-- Waiting Count Modal -->
    <div x-data="{ open: @entangle('showWaitingCountModal') }" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
        <div class="absolute inset-0 bg-black/50" @click="open = false"></div>
        <div class="relative bg-white dark:bg-gray-900 w-full max-w-md mx-auto rounded-2xl shadow-xl p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">عدد الحجوزات المتبقية</h2>
            <div class="text-center text-3xl font-bold text-blue-600 dark:text-blue-400">
                {{ $waitingCount }}
            </div>
            <p class="text-center text-gray-600 dark:text-gray-300 mt-2">حجوزات قيد الانتظار قبل هذا الدور</p>
            <div class="mt-6 text-right">
                <x-filament::button color="gray" wire:click="$set('showWaitingCountModal', false)">إغلاق</x-filament::button>
            </div>
        </div>
    </div>
</x-filament::page>