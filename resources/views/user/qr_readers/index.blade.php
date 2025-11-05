<x-layouts.app :title="__('Lectores QR')">
    <div class="p-6 w-full max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                Lectores QR
            </h2>

            <flux:button variant="primary" icon="plus" :href="route('parking.qr-readers.create')" wire:navigate>
                Crear lector
            </flux:button>
        </div>

        @if (session('status'))
            <div
                class="mb-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 px-4 py-3 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($readers->isEmpty())
            <div class="text-center text-zinc-500 dark:text-zinc-400 py-8">
                No hay lectores registrados aún
            </div>
        @else
            <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-100 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-zinc-700 dark:text-zinc-200">ID
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                                Número de serie</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                                Sentido</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                        @foreach ($readers as $reader)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition">
                                <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                    {{ $reader->id }}
                                </td>
                                <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300 font-medium">
                                    {{ $reader->serial_number }}
                                </td>
                                <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                    @php
                                        $labels = [0 => 'Entrada', 1 => 'Salida', 2 => 'Mixto'];
                                    @endphp
                                    <span
                                        class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium
                      {{ $reader->sense === 0 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : '' }}
                      {{ $reader->sense === 1 ? 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300' : '' }}
                      {{ $reader->sense === 2 ? 'bg-violet-100 text-violet-800 dark:bg-violet-900/30 dark:text-violet-300' : '' }}">
                                        {{ $labels[$reader->sense] ?? $reader->sense }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <flux:button size="sm" icon="pencil-square" variant="ghost"
                                            :href="route('parking.qr-readers.edit', $reader)" wire:navigate>
                                            Editar
                                        </flux:button>

                                        <flux:button size="sm" icon="qr-code" variant="primary"
                                            :href="route('parking.qr-readers.scan', $reader)">
                                            Escanear
                                        </flux:button>

                                        <form method="POST" action="{{ route('parking.qr-readers.destroy', $reader) }}"
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
                            title: "¿Eliminar lector?",
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
