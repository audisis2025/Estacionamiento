<x-layouts.app :title="__('Tipos de cliente')">
    <div class="p-6 w-full max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-2xl font-bold text-black dark:text-white">
                Tipos de cliente
            </h2>

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

        @if ($clientTypes->isEmpty())
            <div class="text-center text-black/60 dark:text-white/60 py-8 text-sm">
                No hay tipos de cliente registrados aún.
            </div>
        @else
            <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-100 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                                Tipo
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                                Descuento
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                                Monto
                            </th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-black dark:text-white">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                        @foreach ($clientTypes as $ct)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition">
                                <td class="px-4 py-3 text-sm text-black dark:text-white">
                                    {{ $ct->typename }}
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
