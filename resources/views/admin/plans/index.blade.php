<x-layouts.admin :title="__('Planes registrados')">
    <div class="p-6 w-full max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                Planes registrados
            </h2>
        </div>

        @if ($plans->isEmpty())
            <div class="text-center text-zinc-500 dark:text-zinc-400 py-8">
                No hay planes registrados aún
            </div>
        @else
            <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-100 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-zinc-700 dark:text-zinc-200">{{ __('Tipo') }}</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-zinc-700 dark:text-zinc-200">{{ __('Nombre') }}</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-zinc-700 dark:text-zinc-200">{{ __('Precio (MXN)') }}</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-zinc-700 dark:text-zinc-200">{{ __('Duración (días)') }}</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-zinc-700 dark:text-zinc-200">{{ __('Descripción') }}</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-zinc-700 dark:text-zinc-200">{{ __('Acciones') }}</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                        @foreach ($plans as $plan)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition">
                                <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300 capitalize">
                                    {{ $plan->type === 'parking' ? 'Estacionamiento' : 'Usuario' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300 font-medium">
                                    {{ $plan->name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                    ${{ number_format($plan->price, 2) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300 text-center">
                                    {{ $plan->duration_days }}
                                </td>
                                <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                    {{ $plan->description }}
                                </td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <flux:button size="sm" icon="pencil-square" variant="ghost" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800"
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

