{{--
* Nombre de la vista           : index.blade.php
* Descripción de la vista      : Página para gestionar las entradas abiertas en el estacionamiento del usuario.
* Fecha de creación            : 06/11/2025
* Elaboró                      : Elian Pérez
* Fecha de liberación          : 06/11/2025
* Autorizó                     : Angel Davila
* Version                      : 1.0
* Fecha de mantenimiento       : 
* Folio de mantenimiento       : 
* Tipo de mantenimiento        :
* Descripción del mantenimiento: 
* Responsable                  : 
* Revisor                      : 
--}}
<x-layouts.app :title="__('Entradas abiertas')">
    <div class="max-w-6xl mx-auto p-6 space-y-6">

        <flux:heading level="2" size="xl" class="text-2xl !font-black text-black dark:text-white">
            Entradas abiertas
        </flux:heading>

        @php $parking = auth()->user()->parking; @endphp
        <flux:text class="text-sm text-black/60 dark:text-white/60">
            Tipo de cobro:
            @switch((int) $parking->type)
                @case(0)
                    Tiempo libre — ${{ number_format($parking->price_flat ?? $parking->price, 2) }}
                    @break

                @case(1)
                    Por hora — ${{ number_format($parking->price, 2) }} / hora
                    @break

                @case(2)
                    Mixto —
                    Hora: ${{ number_format($parking->price, 2) }} / hora,
                    Fija: ${{ number_format($parking->price_flat ?? $parking->price, 2) }}
                    @break
            @endswitch
        </flux:text>

        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="w-full sm:w-auto">
                <flux:input
                    name="q"
                    :label="__('Buscar por teléfono')"
                    type="text"
                    value="{{ $phone }}"
                    placeholder="Ej. 7221234567"
                />
            </div>

            <flux:button
                type="submit"
                variant="primary"
                icon="magnifying-glass"
                icon-variant="outline"
                class="bg-gray-500 hover:bg-gray-600 text-white text-sm"
            >
                Buscar
            </flux:button>
        </form>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-x-auto bg-white dark:bg-zinc-900">
            <table class="min-w-full text-sm divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-100 dark:bg-zinc-800">
                    <tr class="text-left">
                        <th class="px-4 py-3 font-semibold text-black dark:text-white">
                            <flux:text class="font-semibold text-black dark:text-white">
                                ID
                            </flux:text>
                        </th>
                        <th class="px-4 py-3 font-semibold text-black dark:text-white">
                            <flux:text class="font-semibold text-black dark:text-white">
                                Usuario
                            </flux:text>
                        </th>
                        <th class="px-4 py-3 font-semibold text-black dark:text-white">
                            <flux:text class="font-semibold text-black dark:text-white">
                                Entrada
                            </flux:text>
                        </th>
                        <th class="px-4 py-3 font-semibold text-right text-black dark:text-white">
                            <flux:text class="font-semibold text-right text-black dark:text-white">
                                Acción
                            </flux:text>
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($entries as $t)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition">
                            <td class="px-4 py-3 text-black dark:text-white">
                                {{ $t->id }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="font-medium text-black dark:text-white">
                                    {{ $t->user->name ?? 'N/D' }}
                                </div>
                                <div class="text-xs text-black/60 dark:text-white/60">
                                    {{ $t->user->email ?? '' }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-black/80 dark:text-white/80">
                                {{ \Carbon\Carbon::parse($t->entry_date)->format('Y-m-d H:i') }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                <form
                                    method="POST"
                                    action="{{ route('parking.entries.release', $t) }}"
                                    class="form-release inline-block"
                                >
                                    @csrf

                                    <flux:button
                                        type="submit"
                                        size="sm"
                                        icon="lock-open"
                                        icon-variant="outline"
                                        variant="primary"
                                        class="bg-green-600 hover:bg-green-700 text-white text-xs md:text-sm"
                                    >
                                        Liberar salida
                                    </flux:button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-black/60 dark:text-white/60">
                                <flux:text class="text-sm text-black/60 dark:text-white/60">
                                    No hay entradas pendientes de salida.
                                </flux:text>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $entries->links() }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function ()
        {
            function bindReleaseForms()
            {
                document.querySelectorAll('.form-release').forEach((form) =>
                {
                    if (form.dataset.bound === '1')
                    {
                        return;
                    }

                    form.dataset.bound = '1';

                    form.addEventListener('submit', async (e) =>
                    {
                        e.preventDefault();

                        const result = await Swal.fire(
                        {
                            title: '¿Liberar salida?',
                            text: 'Se calculará automáticamente el monto según el tiempo, el tipo de cobro y el modo elegido por el usuario en la app.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, liberar',
                            cancelButtonText: 'Cancelar',
                            confirmButtonColor: '#3182ce',
                            cancelButtonColor: '#EE0000',
                        });

                        if (! result.isConfirmed)
                        {
                            return;
                        }

                        form.submit();
                    });
                });
            }

            document.addEventListener('DOMContentLoaded', bindReleaseForms);
            document.addEventListener('livewire:navigated', bindReleaseForms);

            @if (session('ok'))
                Swal.fire(
                {
                    icon: 'success',
                    title: '¡Éxito!',
                    text: "{{ session('ok') }}",
                });
            @endif

            @if (session('error'))
                Swal.fire(
                {
                    icon: 'error',
                    title: 'Error',
                    text: "{{ session('error') }}",
                });
            @endif
        })();
    </script>
</x-layouts.app>
