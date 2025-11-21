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
        <h2 class="text-2xl font-bold mb-5">Editar tipo de cliente</h2>
        @include('user.client_types._form', [
            'action' => route('parking.client-types.update', $clientType),
            'method' => 'PUT',
            'clientType' => $clientType,
        ])
    </div>
</x-layouts.app>
