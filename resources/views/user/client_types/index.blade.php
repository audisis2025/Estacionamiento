{{--
* Nombre de la vista           : index.blade.php
* Descripción de la vista      : Pantalla donde se muestran todos los tipos de cliente.
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
<x-layouts.app :title="__('Tipos de cliente')">
    <div class="p-6 w-full max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-5">
            <flux:heading level="2" size="xl" class="text-2xl !font-black text-black dark:text-white">
                Tipos de cliente
            </flux:heading>

            <flux:button
                variant="primary"
                icon="plus"
                icon-variant="outline"
                :href="route('parking.client-types.create')"
                wire:navigate
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm"
            >
                Crear nuevo tipo
            </flux:button>
        </div>

        @if ($client_types->isEmpty())
            <div class="text-center text-black/60 dark:text-white/60 py-8 text-sm">
                <flux:text class="text-sm text-black/60 dark:text-white/60">
                    No hay tipos de cliente registrados aún.
                </flux:text>
            </div>
        @else
            <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-100 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                                <flux:text class="text-sm font-semibold text-black dark:text-white">
                                    Tipo
                                </flux:text>
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                                <flux:text class="text-sm font-semibold text-black dark:text-white">
                                    Descuento
                                </flux:text>
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                                <flux:text class="text-sm font-semibold text-black dark:text-white">
                                    Monto
                                </flux:text>
                            </th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-black dark:text-white">
                                <flux:text class="text-sm font-semibold text-black dark:text-white">
                                    Acciones
                                </flux:text>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                        @foreach ($client_types as $ct)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition">
                                <td class="px-4 py-3 text-sm text-black dark:text-white">
                                    {{ $ct->type_name }}
                                </td>

                                <td class="px-4 py-3 text-sm text-black/80 dark:text-white/80">
                                    {{ $ct->discount_type === 0 ? 'Porcentaje (%)' : 'Cantidad ($)' }}
                                </td>

                                <td class="px-4 py-3 text-sm text-black/80 dark:text-white/80">
                                    @if ($ct->discount_type === 0)
                                        {{ number_format($ct->amount, 2) }} %
                                    @else
                                        ${{ number_format($ct->amount, 2) }}
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-sm">
                                    <div class="flex items-center justify-center gap-3">
                                        <flux:button
                                            size="sm"
                                            icon="pencil-square"
                                            icon-variant="outline"
                                            variant="primary"
                                            :href="route('parking.client-types.edit', $ct)"
                                            class="text-sm bg-gray-500 hover:bg-gray-600 text-white"
                                        >
                                            Editar
                                        </flux:button>

                                        <form
                                            method="POST"
                                            action="{{ route('parking.client-types.destroy', $ct) }}"
                                            class="delete-form"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <flux:button
                                                icon="trash"
                                                icon-variant="outline"
                                                type="submit"
                                                size="sm"
                                                variant="danger"
                                                class="text-sm"
                                            >
                                                Eliminar
                                            </flux:button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@push('js')
    <script>
        function attachDeleteListeners()
        {
            document.querySelectorAll('.delete-form').forEach((form) =>
            {
                if (form.dataset.listenerAttached)
                {
                    return;
                }

                form.dataset.listenerAttached = 'true';

                form.addEventListener('submit', function (event)
                {
                    event.preventDefault();

                    Swal.fire(
                    {
                        title: "¿Eliminar tipo de cliente?",
                        text: "Esta acción no se puede deshacer.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3182ce",
                        cancelButtonColor: "#EE0000",
                        confirmButtonText: "Sí, eliminar",
                        cancelButtonText: "Cancelar",
                    }).then((result) =>
                    {
                        if (result.isConfirmed)
                        {
                            form.submit();
                        }
                    });
                });
            });
        }

        document.addEventListener('DOMContentLoaded', attachDeleteListeners);
        document.addEventListener('livewire:navigated', attachDeleteListeners);
    </script>
@endpush
</x-layouts.app>
