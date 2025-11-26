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

            <div
                class="inline-flex items-center gap-2 rounded-full border border-zinc-200 dark:border-zinc-700
                       bg-white dark:bg-zinc-900 px-3 py-1.5"
            >
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
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <flux:text class="text-sm text-black/70 dark:text-white/70">
                Coloca el cursor en <strong>“Capturar”</strong> y escanea el código QR desde el lector USB.
            </flux:text>
        </div>

        <form id="scan-form" class="flex items-center gap-3" onsubmit="return false;">
            @csrf

            <input type="hidden" id="csrf-token" value="{{ csrf_token() }}">
            <input id="scan-input" type="text" autocomplete="off" class="sr-only" />

            <flux:button
                id="focus-btn"
                type="button"
                variant="primary"
                icon="qr-code"
                icon-variant="outline"
                class="bg-custom-blue hover:bg-custom-blue-dark text-white text-sm"
            >
                Capturar
            </flux:button>

            <flux:link
                :href="route('parking.qr-readers.index')"
                icon="arrow-long-left"
                icon-variant="outline"
                class="text-sm text-custom-blue hover:text-custom-blue-dark"
            >
                Regresar a lectores
            </flux:link>
        </form>

        <div class="hidden rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <flux:heading level="3" size="md" class="text-sm !font-black mb-3 text-black dark:text-white">
                Log de actividad
            </flux:heading>

            <pre
                id="result"
                class="text-xs bg-zinc-900 text-white p-3 rounded min-h-[120px] overflow-x-auto"
            >{}</pre>
        </div>
    </div>

    @push('js')
        <script>
            document.addEventListener("DOMContentLoaded", () => 
            {
                window.initQrScanner(@json(route('parking.qr-readers.scan.ingest', $reader)),@json(csrf_token()));
            });

            document.addEventListener("livewire:navigated", () => 
            {
                window.initQrScanner(@json(route('parking.qr-readers.scan.ingest', $reader)),@json(csrf_token()));
            });
        </script>
    @endpush
</x-layouts.app>
