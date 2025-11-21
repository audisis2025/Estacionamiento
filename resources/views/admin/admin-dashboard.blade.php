{{--
* Nombre de la vista           : admin-dashboard.blade.php
* Descripción de la vista      : Panel de administración del sistema.
* Fecha de creación            : 06/11/2025
* Elaboró                      : Elian Pérez
* Fecha de liberación          : 06/11/2025
* Autorizó                     : Angel Davila
* Version                      : 1.1
* Fecha de mantenimiento       : 17/11/2025
* Folio de mantenimiento       :
* Tipo de mantenimiento        : Correctivo
* Descripción del mantenimiento: Actualización de la interfaz
* Responsable                  : Elian Pérez
* Revisor                      : Angel Davila
--}}
<x-layouts.admin :title="__('Inicio')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-medium text-black/60 dark:text-white/60">
                        Planes activos (Estacionamiento)
                    </h3>

                    <div
                        class="inline-flex items-center rounded-md px-2 py-1 text-[11px] font-semibold
                               bg-custom-blue/10 text-custom-blue dark:bg-custom-blue/15 dark:text-custom-blue">
                        Hoy
                    </div>
                </div>

                <div class="mt-3 text-3xl font-bold text-custom-blue">
                    {{ number_format($activeParking) }}
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-medium text-black/60 dark:text-white/60">
                        Planes activos (Usuario)
                    </h3>

                    <div
                        class="inline-flex items-center rounded-md px-2 py-1 text-[11px] font-semibold
                               bg-custom-blue-dark/10 text-custom-blue-dark dark:bg-custom-blue-dark/20 dark:text-custom-blue-dark">
                        Hoy
                    </div>
                </div>

                <div class="mt-3 text-3xl font-bold text-custom-blue-dark">
                    {{ number_format($activeUser) }}
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-medium text-black/60 dark:text-white/60">
                        Ingresos acumulados
                    </h3>

                    <div
                        class="inline-flex items-center rounded-md px-2 py-1 text-[11px] font-semibold
                               bg-custom-green/10 text-custom-green dark:bg-custom-green/20 dark:text-custom-green">
                        MXN
                    </div>
                </div>

                <div class="mt-3 text-3xl font-bold text-custom-green">
                    ${{ number_format($totalRevenue, 2) }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
