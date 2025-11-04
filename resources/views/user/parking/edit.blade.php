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
