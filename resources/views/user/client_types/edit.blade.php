{{--
* Nombre de la vista           : edit.blade.php
* Descripción de la vista      : Encabezado para la edicion de tipos de cliente.
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
<x-layouts.app :title="__('Editar tipo de cliente')">
    <div class="p-6 w-full max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-2">
            <flux:heading level="2" size="xl" class="text-2xl !font-black mb-5 text-black dark:text-white">
                Editar tipo de cliente
            </flux:heading>

            <flux:button variant="ghost" icon="arrow-long-left" icon-variant="outline" :href="route('parking.client-types.index')" wire:navigate>
                    Regresar
            </flux:button>
        </div>

        @include('user.client_types._form', [
            'action' => route('parking.client-types.update', $clientType),
            'method' => 'PUT',
            'client_type' => $clientType,
        ])
    </div>
</x-layouts.app>
