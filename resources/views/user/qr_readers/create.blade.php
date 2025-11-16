{{--
* Nombre de la vista           : create.blade.php
* Descripción de la vista      : Encabezado para la creación de un nuevo lector QR.
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
<x-layouts.app :title="__('Crear lector QR')">
    <div class="max-w-3xl mx-auto px-6 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Crear lector QR</h1>
            <flux:button variant="ghost" icon="arrow-long-left" icon-variant="outline" :href="route('parking.qr-readers.index')" wire:navigate>
                Regresar
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
