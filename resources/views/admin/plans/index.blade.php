{{--
* Nombre de la vista           : index.blade.php
* Descripción de la vista      : Panel de donde se muestran todos los planes.
* Fecha de creación            : 03/11/2025
* Elaboró                      : Elian Pérez
* Fecha de liberación          : 04/11/2025
* Autorizó                     : Angel Davila
* Version                      : 1.1
* Fecha de mantenimiento       : 17/11/2025
* Folio de mantenimiento       :
* Tipo de mantenimiento        : Correctivo
* Descripción del mantenimiento: Actualización de la interfaz
* Responsable                  : Elian Pérez
* Revisor                      : Angel Davila
--}}
<x-layouts.admin :title="__('Planes registrados')">
    <div class="p-6 w-full max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-2xl font-bold text-black dark:text-white">
                Planes registrados
            </h2>
        </div>

        @if ($plans->isEmpty())
            <div class="text-center text-black/60 dark:text-white/60 py-8 text-sm">
                No hay planes registrados aún.
            </div>
        @else
            <div
                class="overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm bg-white dark:bg-zinc-900">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-100 dark:bg-zinc-800/80">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60">
                                {{ __('Tipo') }}
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60">
                                {{ __('Nombre') }}
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60">
                                {{ __('Precio (MXN)') }}
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60">
                                {{ __('Duración (días)') }}
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60">
                                {{ __('Descripción') }}
                            </th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-black/60 dark:text-white/60">
                                {{ __('Acciones') }}
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                        @foreach ($plans as $plan)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-colors">
                                <td class="px-4 py-3 text-sm text-black/80 dark:text-white/80 capitalize">
                                    {{ $plan->type === 'parking' ? 'Estacionamiento' : 'Usuario' }}
                                </td>

                                <td class="px-4 py-3 text-sm text-black dark:text-white font-medium">
                                    {{ $plan->name }}
                                </td>

                                <td class="px-4 py-3 text-sm text-black/80 dark:text-white/80">
                                    ${{ number_format($plan->price, 2) }}
                                </td>

                                <td class="px-4 py-3 text-sm text-black/80 dark:text-white/80 text-center">
                                    {{ $plan->duration_days }}
                                </td>

                                <td class="px-4 py-3 text-sm text-black/70 dark:text-white/70">
                                    {{ $plan->description }}
                                </td>

                                <td class="px-4 py-3 text-sm text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <flux:button size="sm" icon="pencil-square" icon-variant="outline"
                                            variant="primary" class="bg-gray-500 hover:bg-gray-600 text-white text-sm"
                                            :href="route('admin.plans.edit', $plan)">
                                            Editar
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.admin>
