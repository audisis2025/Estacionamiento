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
