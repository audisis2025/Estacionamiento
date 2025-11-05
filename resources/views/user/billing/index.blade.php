<x-layouts.app :title="__('Facturación')">
    <div class="max-w-3xl mx-auto p-6 space-y-6">
        <header class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <x-sat-logo class="h-10 w-auto" />
                <div>
                    <h1 class="text-xl font-semibold">Facturación (en desarrollo)</h1>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        Esta opción está considerada para una próxima versión del sistema. Hemos tenido en cuenta los
                        requisitos,
                        pero aún no está desarrollada.
                    </p>
                </div>
            </div>
            <span
                class="text-xs rounded-full px-2 py-1 bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                Próximamente
            </span>
        </header>

        <div class="rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6">
            <div class="rounded-xl bg-zinc-50 dark:bg-zinc-800 p-4 text-sm text-zinc-600 dark:text-zinc-300">
                Próximamente podrás emitir facturas, consultar historial de CFDI y descargar reportes.
            </div>
            <p class="mt-4 text-xs text-zinc-500">
                Marca y logotipo “SAT” pertenecen a sus respectivos titulares. Se muestran con fines de referencia.
            </p>
        </div>
    </div>
</x-layouts.app>
