{{-- resources/views/filament/pages/partials/reservations-tab.blade.php --}}
<div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-4">
        <input type="text" wire:model.defer="searchName" placeholder="ادخل اسم المريض أو الهاتف" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2">
        <input type="date" wire:model.defer="searchDate" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2">
        <input type="date" wire:model.defer="searchFrom" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2">
        <input type="date" wire:model.defer="searchTo" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2">
    </div>

    <x-filament::button wire:click="loadReservations" class="mb-4">بحث</x-filament::button>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach ($this->reservations as $reservation)
            @php
                $waiting = \App\Models\Reservation::whereDate('reservation_date', $reservation->reservation_date)
                    ->where('status','pending')
                    ->where('reservation_number','<',$reservation->reservation_number)
                    ->count();
            @endphp

            <div class="p-4 bg-white shadow rounded border hover:shadow-lg transition">
                <div class="font-bold text-lg cursor-pointer text-blue-600"
                     wire:click="showPatient({{ $reservation->patient->id }})">
                    {{ $reservation->patient->name }}
                </div>
                <div class="text-sm text-gray-600 cursor-pointer text-blue-600"
                     wire:click="showDoctor({{ $reservation->doctor->id }})">
                    دكتور: {{ $reservation->doctor->name }}
                </div>

                <div class="mt-2">
                    الدور: {{ $reservation->reservation_number }}
                </div>

                @if($reservation->status == 'pending')
                    <div class="mt-1">المتبقي على الدور: {{ $waiting }}</div>
                @endif

                <div class="mt-2 flex items-center space-x-2">
                    <span>الحالة:</span>
                    <select wire:change="updateStatus({{ $reservation->id }}, $event.target.value)" class="border rounded px-2 py-1 text-sm">
                        <option value="pending"   @selected($reservation->status == 'pending')>قيد الانتظار</option>
                        <option value="completad" @selected($reservation->status == 'completad')>مؤكد</option>
                        <option value="cancelled" @selected($reservation->status == 'cancelled')>ملغي</option>
                    </select>
                </div>
            </div>
        @endforeach
    </div>
</div>
