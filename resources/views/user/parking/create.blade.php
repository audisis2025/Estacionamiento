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
