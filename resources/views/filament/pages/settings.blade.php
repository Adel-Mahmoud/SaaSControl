<x-filament::page>
    <form wire:submit.prevent="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex ltr:justify-end rtl:justify-start">
            <x-filament::button 
                type="submit" 
                color="success" 
                wire:loading.attr="disabled"
                wire:target="save">
                <span wire:loading.remove wire:target="save">حفظ</span>
                <span wire:loading wire:target="save">جاري الحفظ...</span>
            </x-filament::button>
        </div>
    </form>
</x-filament::page>
