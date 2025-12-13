{{--
* Nombre de la vista           : scan.blade.php
* Descripción de la vista      : Pantalla para escanear códigos QR con un lector específico.
* Fecha de creación            : 04/11/2025
* Elaboró                      : Elian Pérez
* Fecha de liberación          : 05/11/2025
* Autorizó                     : Angel Davila
* Version                      : 1.0
* Fecha de mantenimiento       : 
* Folio de mantenimiento       : 
* Tipo de mantenimiento        :
* Descripción del mantenimiento: 
* Responsable                  : 
* Revisor                      : 
--}}
<x-layouts.app :title="__('Escanear — Lector #' . $reader->id)">
    <div class="p-6 max-w-2xl mx-auto space-y-6 text-black dark:text-white">
        <div class="flex items-start justify-between gap-4">
            <div>
                <flux:heading level="2" size="xl" class="text-2xl !font-black">
                    Escanear — Lector #{{ $reader->id }}
                </flux:heading>

                <flux:text class="text-sm text-black/70 dark:text-white/70 mt-1">
                    Tipo:
                    <span class="font-medium">
                        {{ ['Entrada', 'Salida', 'Mixto'][$reader->sense] ?? $reader->sense }}
                    </span>
                </flux:text>
            </div>

            <div class="inline-flex items-center gap-2 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-1.5">
                <span class="relative flex h-2.5 w-2.5">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-custom-green opacity-75"
                    ></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-custom-green"></span>
                </span>

                <flux:text class="text-sm font-medium text-custom-green">
                    Listo para escanear
                </flux:text>
            </div>

            <div class="flex items-center justify-between mb-6">
                <flux:button variant="ghost" icon="arrow-long-left" icon-variant="outline" :href="route('parking.qr-readers.index')" wire:navigate>
                    Regresar
                </flux:button>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <flux:text class="text-sm text-black/70 dark:text-white/70">
                Coloca el cursor en <strong>“Capturar”</strong> si se perdio y escanea el código QR desde el lector USB.
            </flux:text>
        </div>

        <div class="mt-3 flex justify-end">
            <form id="scan-form" class="flex items-center gap-3" onsubmit="return false;">
                @csrf

                <input type="hidden" id="csrf-token" value="{{ csrf_token() }}">
                <input id="scan-input" type="text" autocomplete="off" class="sr-only" />

                <flux:button
                    id="focus-btn"
                    type="button"
                    variant="primary"
                    icon="computer-desktop"
                    icon-variant="outline"
                    class="bg-gray-500 hover:bg-gray-600 text-white text-sm"
                >
                    Capturar
                </flux:button>
            </form>
        </div>

         <div class="mt-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <flux:heading level="3" size="md" class="text-sm !font-black mb-2 text-black dark:text-white">
                Prueba manual por teléfono
            </flux:heading>

          <div class="flex flex-col sm:flex-row gap-3 items-center">
            <div class="w-full">
                <flux:label for="manual-phone" class="text-xs font-medium text-black dark:text-white">
                    Número de teléfono
                </flux:label>

                <div class="flex items-start justify-between gap-3 mt-1">
                    <flux:input
                        id="manual-phone"
                        type="text"
                        placeholder="Ej. 5550001111"
                        class="text-xs md:text-sm w-48 sm:w-64"
                    />
                    <flux:button
                        id="manual-test-btn"
                        type="button"
                        variant="primary"
                        icon="check"
                        icon-variant="outline"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm"
                    >
                        Confirmar
                    </flux:button>
                </div>
            </div>
        </div>

        <div class="hidden rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <flux:heading level="3" size="md" class="text-sm !font-black mb-3 text-black dark:text-white">
                Log de actividad
            </flux:heading>

            <pre id="result" class="text-xs bg-zinc-900 text-white p-3 rounded min-h-[120px] overflow-x-auto">{}</pre>
        </div>
    </div>

    @push('js')
        <script>
            document.addEventListener("DOMContentLoaded", () =>
            {
                window.initQrScanner(@json(route('parking.qr-readers.scan.ingest', $reader)), @json(csrf_token()));
            });

            document.addEventListener("livewire:navigated", () =>
            {
                window.initQrScanner(@json(route('parking.qr-readers.scan.ingest', $reader)), @json(csrf_token()));
            });

            document.addEventListener('DOMContentLoaded', () =>
            {
                const btn   = document.getElementById('manual-test-btn');
                const input = document.getElementById('manual-phone');

                if (!btn || !input) return;

                btn.addEventListener('click', async () =>
                {
                    const phone = input.value.trim();

                    if (!phone)
                    {
                        Swal.fire(
                        {
                            title: 'Teléfono requerido',
                            text: 'Ingresa un número de teléfono para la prueba.',
                            icon: 'warning',
                            confirmButtonColor: '#494949'
                        });
                        return;
                    }

                    try
                    {
                        const fd = new FormData();
                        fd.append('phone', phone);

                        const res = await fetch(
                            @json(route('parking.qr-readers.scan.simulate', $reader)),
                            {
                                method : 'POST',
                                headers : 
                                {
                                    'X-CSRF-TOKEN': @json(csrf_token()) 
                                },
                                body : fd,
                                credentials : 'same-origin'
                            }
                        );

                        const json = await res.json().catch(() => ({}));

                        if (!res.ok || !json.ok)
                        {
                            Swal.fire(
                            {
                                title: 'No se pudo generar el QR',
                                text: json.message ?? 'Revisa el número de teléfono.',
                                icon: 'error',
                                confirmButtonColor: '#494949'
                            });
                            return;
                        }

                        if (json.needs_billing_mode)
                        {
                            const result = await Swal.fire(
                            {
                                title: 'Selecciona el modo de cobro',
                                text: 'Elige cómo se cobrará esta estancia.',
                                icon: 'question',
                                showDenyButton: true,
                                showCancelButton: true,
                                confirmButtonText: `Tiempo libre ($${json.price_flat})`,
                                denyButtonText: `Por hora ($${json.price_hour}/hora)`,
                                cancelButtonText: 'Cancelar',
                                confirmButtonColor: '#42A958',
                                denyButtonColor: '#494949',
                                cancelButtonColor: '#EE0000',
                            });

                            let mode = null;

                            if (result.isConfirmed)
                            {
                                mode = 'flat';
                            } else if (result.isDenied)
                            {
                                mode = 'hour';
                            }

                            if (!mode)
                            {
                                return;
                            }

                            await processBillingMode(mode, phone);
                            return;
                        }

                        if (window.qrManualSubmit)
                        {
                            window.qrManualSubmit(json.payload);
                        } else
                        {
                            Swal.fire(
                            {
                                title: 'Inicialización pendiente',
                                text: 'El lector aún no está listo. Recarga la página.',
                                icon: 'error',
                                confirmButtonColor: '#42A958',
                                confirmButtonText: 'Entendido'
                            });
                        }
                    } catch (e)
                    {
                        Swal.fire(
                        {
                            title: 'Error de red',
                            text: 'No se pudo contactar al servidor.',
                            icon: 'error',
                            confirmButtonColor: '#42A958',
                            confirmButtonText: 'Entendido'
                        });
                    }
                });

                async function processBillingMode(mode, phone)
                {
                    Swal.fire(
                    {
                        title: 'Procesando...',
                        text: 'Generando entrada manual',
                        allowOutsideClick: false,
                        didOpen: () => 
                        {
                            Swal.showLoading();
                        }
                    });

                    const fd2 = new FormData();
                    fd2.append('phone', phone);
                    fd2.append('billing_mode', mode);

                    const res2 = await fetch(
                        @json(route('parking.qr-readers.scan.simulate', $reader)),
                        {
                            method : 'POST',
                            headers : 
                            { 
                                'X-CSRF-TOKEN': @json(csrf_token()) 
                            },
                            body : fd2,
                            credentials : 'same-origin'
                        }
                    );

                    const json2 = await res2.json().catch(() => ({}));

                    Swal.close();

                    if (res2.ok && json2.ok && json2.payload)
                    {
                        if (window.qrManualSubmit)
                        {
                            window.qrManualSubmit(json2.payload);
                        } else
                        {
                            Swal.fire(
                            {
                                title: 'Inicialización pendiente',
                                text: 'El lector aún no está listo. Recarga la página.',
                                icon: 'error',
                                confirmButtonColor: '#42A958',
                            });
                        }
                    } else
                    {
                        Swal.fire(
                        {
                            title: 'Error al procesar',
                            text: json2.message ?? 'No se pudo generar la entrada.',
                            icon: 'error',
                            confirmButtonColor: '#42A958',
                        });
                    }
                }
            });
        </script>
    @endpush
</x-layouts.app>