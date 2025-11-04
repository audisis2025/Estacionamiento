<x-layouts.app :title="__('Editar lector QR')">
    <div class="max-w-3xl mx-auto px-6 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Editar lector #{{ $reader->id }}</h1>
            <flux:button variant="ghost" icon="arrow-uturn-left" :href="route('parking.qr-readers.index')" wire:navigate>
                Volver
            </flux:button>
        </div>

        @include('user.qr_readers._form', [
            'action' => route('parking.qr-readers.update', $reader),
            'method' => 'PUT',
            'reader' => $reader,
            'formId' => 'qr-edit',
        ])
    </div>
</x-layouts.app>
