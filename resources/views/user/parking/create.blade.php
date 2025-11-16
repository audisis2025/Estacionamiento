{{--
* Nombre de la vista           : create.blade.php
* Descripción de la vista      : Encabezado para la creación de un nuevo estacionamiento.
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
<x-layouts.app :title="__('Crear estacionamiento')">
  <div class="max-w-5xl mx-auto px-6 py-8">
    <h1 class="text-2xl font-bold mb-6">Crear estacionamiento</h1>

    @include('user.parking._form', [
        'action' => route('parking.store'),
        'method' => 'POST',
        'parking' => null,
        'formId' => 'parking-create'
    ])
  </div>
</x-layouts.app>
