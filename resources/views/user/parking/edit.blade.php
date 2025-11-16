{{--
* Nombre de la vista           : edit.blade.php
* Descripción de la vista      : Encabezado para la edición de un estacionamiento existente.
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
<x-layouts.app :title="__('Mi estacionamiento')">
  <div class="max-w-5xl mx-auto px-6 py-8">
    <h1 class="text-2xl font-bold mb-6">Editar estacionamiento</h1>
    
    @include('user.parking._form', [
        'action' => route('parking.update'),
        'method' => 'PUT',
        'parking' => $parking,
        'formId' => 'parking-edit'
    ])
  </div>
</x-layouts.app>
