<x-layouts.app :title="__('Crear lector QR')">
    <div class="max-w-3xl mx-auto px-6 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Crear lector QR</h1>
            <flux:button variant="ghost" icon="arrow-uturn-left" :href="route('parking.qr-readers.index')" wire:navigate>
                Volver
            </flux:button>
        </div>

        @include('user.qr_readers._form', [
            'action' => route('parking.qr-readers.store'),
            'method' => 'POST',
            'reader' => null,
            'formId' => 'qr-create',
        ])
    </div>
</x-layouts.app>
