{{-- 
* Nombre de la vista           : index.blade.php
* Descripción de la vista      : Página de listado de lectores QR.
* Fecha de creación            : 04/11/2025
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
<x-layouts.app :title="__('Lectores QR')">
    <div class="p-6 w-full max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-5">
            <flux:heading level="2" size="xl" class="text-2xl !font-black text-black dark:text-white">
                Lectores QR
            </flux:heading>

            <flux:button
                variant="primary"
                icon="plus"
                icon-variant="outline"
                :href="route('parking.qr-readers.create')"
                wire:navigate
                class="bg-blue-600 hover:bg-blue-700 text-white"
            >
                Crear lector
            </flux:button>
        </div>

        @if (session('status'))
            @push('js')
                <script>
                    document.addEventListener('DOMContentLoaded', () =>
                    {
                        Swal.fire(
                        {
                            icon: 'success',
                            title: 'Operación exitosa',
                            text: @json(session('status')),
                            confirmButtonColor: '#241178',
                        });
                    });
                </script>
            @endpush
        @endif

        @if ($readers->isEmpty())
            <div class="text-center text-black/60 dark:text-white/60 py-8">
                <flux:text class="text-black/60 dark:text-white/60">
                    No hay lectores registrados aún.
                </flux:text>
            </div>
        @else
            <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-100 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                                <flux:text class="text-sm font-semibold text-black dark:text-white">
                                    ID
                                </flux:text>
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                                <flux:text class="text-sm font-semibold text-black dark:text-white">
                                    Número de serie
                                </flux:text>
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                                <flux:text class="text-sm font-semibold text-black dark:text-white">
                                    Sentido
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
                        @foreach ($readers as $reader)
                            @php
                                $labels = [0 => 'Entrada', 1 => 'Salida', 2 => 'Mixto'];

                                $badgeClasses = match ($reader->sense) 
                                {
                                    0       => 'bg-custom-green text-white',
                                    1       => 'bg-custom-orange text-white',
                                    2       => 'bg-custom-blue text-white',
                                    default => 'bg-zinc-200 text-black dark:bg-zinc-700 dark:text-white',
                                };
                            @endphp

                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition">
                                <td class="px-4 py-3 text-sm text-black dark:text-white">
                                    {{ $reader->id }}
                                </td>

                                <td class="px-4 py-3 text-sm text-black dark:text-white font-medium">
                                    {{ $reader->serial_number }}
                                </td>

                                <td class="px-4 py-3 text-sm text-black dark:text-white">
                                    <span
                                        class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium {{ $badgeClasses }}"
                                    >
                                        {{ $labels[$reader->sense] ?? $reader->sense }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-sm text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <flux:button
                                            size="sm"
                                            icon="pencil-square"
                                            icon-variant="outline"
                                            variant="primary"
                                            :href="route('parking.qr-readers.edit', $reader)"
                                            wire:navigate
                                            class="bg-gray-500 hover:bg-gray-600 text-white"
                                        >
                                            Editar
                                        </flux:button>

                                        <flux:button
                                            size="sm"
                                            icon="qr-code"
                                            icon-variant="outline"
                                            variant="primary"
                                            :href="route('parking.qr-readers.scan', $reader)"
                                            class="text-white bg-black hover:bg-custom-gray dark:text-white"
                                        >
                                            Escanear
                                        </flux:button>

                                        <form method="POST" action="{{ route('parking.qr-readers.destroy', $reader) }}" class="delete-form">
                                            @csrf
                                            @method('DELETE')

                                            <flux:button
                                                icon="trash"
                                                icon-variant="outline"
                                                type="submit"
                                                size="sm"
                                                variant="danger"
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
                    if (form.dataset.listenerAttached) return;
                    form.dataset.listenerAttached = 'true';

                    form.addEventListener('submit', function (event)
                    {
                        event.preventDefault();

                        Swal.fire(
                        {
                            title: '¿Eliminar lector?',
                            text: 'Esta acción no se puede deshacer.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3182ce', 
                            cancelButtonColor: '#EE0000',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar',
                        })
                        .then((result) =>
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
