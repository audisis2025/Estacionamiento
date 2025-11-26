{{-- 
* Nombre de la vista           : index.blade.php
* Descripción de la vista      : Panel de visualización de facturas para una implementación futura.
* Fecha de creación            : 06/11/2025
* Elaboró                      : Elian Pérez
* Fecha de liberación          : 06/11/2025
* Autorizó                     : Angel Davila
* Version                      : 1.0
* Fecha de mantenimiento       : 16/11/2025
* Folio de mantenimiento       :
* Tipo de mantenimiento        : Correctivo
* Descripción del mantenimiento: Actualización de la interfaz
* Responsable                  : Elian Pérez
* Revisor                      : Angel Davila
--}}
<x-layouts.app :title="__('Facturación')">
    <div class="max-w-3xl mx-auto p-6 space-y-6">

        <header class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <x-sat-logo class="h-10 w-auto" />

                <div>
                    <flux:heading
                        level="2"
                        size="xl"
                        class="text-xl !font-black text-black dark:text-white"
                    >
                        Facturación (en desarrollo)
                    </flux:heading>

                    <flux:text class="text-sm text-black/70 dark:text-white/70 mt-1">
                        Esta opción está considerada para una próxima versión del sistema.
                        Hemos tenido en cuenta los requisitos, pero aún no está desarrollada.
                    </flux:text>
                </div>
            </div>

            <span class="mb-4 inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                       bg-custom-orange/10 text-custom-orange
                       dark:bg-custom-orange/15 dark:text-custom-orange">
                Próximamente
            </span>
        </header>

        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">
            <div class="rounded-xl bg-zinc-50 dark:bg-zinc-800 p-4 text-sm text-black/70 dark:text-white/70">
                Próximamente podrás emitir facturas, consultar historial de CFDI y descargar reportes
                directamente desde Parking+.
            </div>

            <flux:text class="mt-4 text-xs text-black/60 dark:text-white/60">
                El nombre y logotipo “SAT” pertenecen a la SHCP y se muestran únicamente con fines de referencia.
            </flux:text>
        </div>
    </div>
</x-layouts.app>
