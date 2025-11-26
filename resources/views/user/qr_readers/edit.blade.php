{{--
* Nombre de la vista           : edit.blade.php
* Descripción de la vista      : Encabezado para la edición de un lector QR existente.
* Fecha de creación            : 04/11/2025
* Elaboró                      : Elian Pérez
* Fecha de liberación          : 05/11/2025
* Autorizó                     : Angel Davila
* Version                      : 1.0
* Fecha de mantenimiento       : 
* Folio de mantenimiento       : 
* Tipo de mantenimiento        :
* Descripción del mantenimiento: 
* Responsable                  : 
* Revisor                      : 
--}}
<x-layouts.app :title="__('Editar lector QR')">
    <div class="max-w-3xl mx-auto px-6 py-8">
        <div class="flex items-center justify-between mb-6">
            <flux:heading level="2" size="xl" class="text-2xl !font-black text-black dark:text-white">
                Editar lector #{{ $reader->id }}
            </flux:heading>
            <flux:button variant="ghost" icon="arrow-long-left" icon-variant="outline" :href="route('parking.qr-readers.index')" wire:navigate>
                Regresar
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
