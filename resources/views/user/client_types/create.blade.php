{{--
* Nombre de la vista           : create.blade.php
* Descripción de la vista      : Encabezado para la creación de tipos de cliente.
* Fecha de creación            : 03/11/2025
* Elaboró                      : Elian Pérez
* Fecha de liberación          : 04/11/2025
* Autorizó                     : Angel Davila
* Version                      : 1.0
* Fecha de mantenimiento       : 
* Folio de mantenimiento       :
* Tipo de mantenimiento        : 
* Descripción del mantenimiento: 
* Responsable                  : 
* Revisor                      : 
--}}
<x-layouts.app :title="__('Nuevo tipo de cliente')">
    <div class="p-6 w-full max-w-3xl mx-auto space-y-4">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-2xl font-bold text-black dark:text-white">
                Nuevo tipo de cliente
            </h2>
            <flux:button variant="ghost" icon="arrow-long-left" icon-variant="outline" :href="route('parking.client-types.index')" wire:navigate>
                Regresar
            </flux:button>
        </div>

        @include('user.client_types._form', [
            'action' => route('parking.client-types.store'),
            'method' => 'POST',
            'clientType' => null,
        ])
    </div>
</x-layouts.app>
