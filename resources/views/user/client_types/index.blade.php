<x-layouts.app :title="__('Tipos de cliente')">
    <div class="p-6 w-full max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-2xl font-bold">Tipos de cliente</h2>

            <flux:button variant="primary" icon="plus" :href="route('parking.client-types.create')" wire:navigate>
                Nuevo tipo
            </flux:button>
        </div>

        @if ($clientTypes->isEmpty())
            <div class="text-center text-zinc-500 dark:text-zinc-400 py-8">
                No hay tipos de cliente registrados aún.
            </div>
        @else
            <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-100 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Tipo</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Descuento</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Monto</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                        @foreach ($clientTypes as $ct)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition">
                                <td class="px-4 py-3 text-sm">{{ $ct->typename }}</td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $ct->discount_type === 0 ? 'Porcentaje (%)' : 'Cantidad ($)' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $ct->discount_type === 0 ? number_format($ct->amount, 2) . ' %' : '$' . number_format($ct->amount, 2) }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex items-center justify-center gap-3">
                                        <flux:button size="sm" icon="pencil-square" variant="ghost"
                                            :href="route('parking.client-types.edit', $ct)">
                                            Editar
                                        </flux:button>

                                        <form method="POST" action="{{ route('parking.client-types.destroy', $ct) }}"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button icon="trash" type="submit" size="sm" variant="danger">
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
            function attachDeleteListeners() {
                document.querySelectorAll('.delete-form').forEach(form => {
                    if (form.dataset.listenerAttached) return;
                    form.dataset.listenerAttached = 'true';

                    form.addEventListener('submit', function(event) {
                        event.preventDefault();
                        Swal.fire({
                            title: "¿Estás seguro?",
                            text: "Esta acción no se puede deshacer.",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#dc2626",
                            cancelButtonColor: "#6b7280",
                            confirmButtonText: "Sí, eliminar",
                            cancelButtonText: "Cancelar",
                        }).then((result) => {
                            if (result.isConfirmed) form.submit();
                        });
                    });
                });
            }
            document.addEventListener('DOMContentLoaded', attachDeleteListeners);
            document.addEventListener('livewire:navigated', attachDeleteListeners);
        </script>
    @endpush
</x-layouts.app>
