<x-layouts.admin :title="__('Inicio')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {{-- Planes activos (Parking) --}}
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-5">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-zinc-600 dark:text-zinc-400">
                        Planes activos (Parking)
                    </h3>
                    <div class="rounded-md bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-300 px-2 py-1 text-xs font-semibold">
                        Hoy
                    </div>
                </div>
                <div class="mt-3 text-3xl font-bold text-zinc-900 dark:text-zinc-100">
                    {{ number_format($activeParking) }}
                </div>
            </div>

            {{-- Planes activos (Usuario) --}}
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-5">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-zinc-600 dark:text-zinc-400">
                        Planes activos (Usuario)
                    </h3>
                    <div class="rounded-md bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300 px-2 py-1 text-xs font-semibold">
                        Hoy
                    </div>
                </div>
                <div class="mt-3 text-3xl font-bold text-zinc-900 dark:text-zinc-100">
                    {{ number_format($activeUser) }}
                </div>
            </div>

            {{-- Ingresos acumulados --}}
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-5">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-zinc-600 dark:text-zinc-400">
                        Ingresos acumulados
                    </h3>
                    <div class="rounded-md bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 px-2 py-1 text-xs font-semibold">
                        MXN
                    </div>
                </div>
                <div class="mt-3 text-3xl font-bold text-zinc-900 dark:text-zinc-100">
                    ${{ number_format($totalRevenue, 2) }}
                </div>
            </div>
        </div>

        
        {{-- Espacio para crecer: gráficas o tablas
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6">
            <div class="text-sm text-zinc-600 dark:text-zinc-400">
                Aquí puedes agregar: ingresos por mes, altas por tipo de plan, próximos a vencer, etc.
            </div>
        </div> --}}
    </div>
</x-layouts.admin>
