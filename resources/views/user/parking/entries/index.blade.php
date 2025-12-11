{{-- 
* Nombre de la vista           : index.blade.php
* Descripción de la vista      : Página para gestionar las entradas abiertas en el estacionamiento del usuario.
* Fecha de creación            : 06/11/2025
* Elaboró                      : Elian Pérez
* Fecha de liberación          : 06/11/2025
* Autorizó                     : Angel Davila
* Version                      : 1.1
* Fecha de mantenimiento       : 10/12/2025
* Folio de mantenimiento       : 
* Tipo de mantenimiento        : Perfectivo
* Descripción del mantenimiento: Se agregó QR especial de salida y búsqueda en vivo por teléfono.
* Responsable                  : Elian Pérez
* Revisor                      : Angel Davila
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

        <form id="entries-filter-form" method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="w-full sm:w-auto">
                <flux:input
                    id="filter-phone"
                    name="q"
                    :label="__('Buscar por teléfono')"
                    type="text"
                    value="{{ $phone }}"
                    placeholder="Ej. 7221234567"
                />
            </div>
        </form>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-x-auto bg-white dark:bg-zinc-900">
            <table class="min-w-full text-sm divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-100 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-black dark:text-white">
                            <flux:text class="font-semibold text-black dark:text-white">
                                ID
                            </flux:text>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold text-black dark:text-white">
                            <flux:text class="font-semibold text-black dark:text-white">
                                Usuario
                            </flux:text>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold text-black dark:text-white">
                            <flux:text class="font-semibold text-black dark:text-white">
                                Entrada
                            </flux:text>
                        </th>
                        <th class="px-4 py-3 text-center font-semibold text-black dark:text-white">
                            <flux:text class="font-semibold text-center text-black dark:text-white">
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
                                <div class="text-xs text-black/60 dark:text-white/60">
                                    {{ $t->user->phone_number ?? '' }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-black/80 dark:text-white/80">
                                {{ \Carbon\Carbon::parse($t->entry_date)->format('Y-m-d H:i') }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                <form
                                    method="POST"
                                    action="{{ route('parking.entries.manual-exit-qr', $t) }}"
                                    class="form-manual-exit inline-block"
                                >
                                    @csrf

                                    <flux:button
                                        type="submit"
                                        size="sm"
                                        icon="check-badge"
                                        icon-variant="outline"
                                        variant="primary"
                                        class="bg-green-600 hover:bg-green-700 text-white text-xs md:text-sm"
                                    >
                                        Generar QR
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
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

    <script>
        (function ()
        {
            function bindLiveFilter()
            {
                const form  = document.getElementById('entries-filter-form');
                const input = document.getElementById('filter-phone');

                if (!form || !input) 
                {
                    return;
                }

                let timer = null;

                input.addEventListener('input', () =>
                {
                    clearTimeout(timer);
                    timer = setTimeout(() =>
                    {
                        form.requestSubmit();
                    }, 500);
                });

                input.addEventListener('keypress', (e) =>
                {
                    if (e.key === 'Enter')
                    {
                        e.preventDefault();
                        clearTimeout(timer);
                        form.requestSubmit();
                    }
                });
            }

            function bindManualExitForms()
            {
                document.querySelectorAll('.form-manual-exit').forEach((form) =>
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
                            title: '¿Generar QR de salida?',
                            text: 'Este QR permitirá una salida sin cargo (pago en efectivo) para esta estancia. Se puede usar una sola vez y tiene una vigencia de 15 minutos a partir de ahora.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Generar',
                            cancelButtonText: 'Cancelar',
                            confirmButtonColor: '#42A958',
                            cancelButtonColor: '#EE0000'
                        });

                        if (! result.isConfirmed)
                        {
                            return;
                        }

                        form.submit();
                    });
                });
            }

            document.addEventListener('DOMContentLoaded', () =>
            {
                bindLiveFilter();
                bindManualExitForms();
            });

            document.addEventListener('livewire:navigated', () =>
            {
                bindLiveFilter();
                bindManualExitForms();
            });

            @if (session('ok'))
                Swal.fire(
                {
                    icon: 'success',
                    title: '¡Éxito!',
                    text: "{{ session('ok') }}",
                    confirmButtonColor: '#494949'
                });
            @endif

            @if (session('error'))
                Swal.fire(
                {
                    icon: 'error',
                    title: 'Error',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#494949'
                });
            @endif

            @if (session('special_exit_qr'))
                document.addEventListener('DOMContentLoaded', function ()
                {
                    const payload = @json(session('special_exit_qr'));

                    Swal.fire(
                    {
                        icon: 'info',
                        title: 'QR de salida generado',
                        html: `
                            <p class="text-sm mb-2">
                                Escanea este código para liberar la salida sin cargo desde el lector.
                                Tiene una vigencia de 15 minutos y se puede usar una sola vez.
                            </p>
                            <div id="special-exit-qr" style="display:flex;justify-content:center;margin-top:8px;"></div>
                            <p class="text-xs mt-3 text-gray-500">
                                Puedes tomar una captura de pantalla o imprimir este código para entregarlo al cliente.
                            </p>
                        `,
                        confirmButtonColor: '#494949',
                        didOpen: () =>
                        {
                            const container = document.getElementById('special-exit-qr');

                            if (container && typeof QRCode !== 'undefined')
                            {
                                new QRCode(container,
                                {
                                    text: payload,
                                    width: 220,
                                    height: 220
                                });
                            }
                        }
                    });
                });
            @endif
        })();
    </script>
</x-layouts.app>