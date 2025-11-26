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
    <div class="p-6 w-full max-w-6xl mx-auto">

        <div class="flex items-center justify-between mb-5">
            <flux:heading level="2" size="xl" class="text-2xl !font-black text-black dark:text-white">
                Panel de administración
            </flux:heading>

            <flux:text class="text-xs text-black/60 dark:text-white/60">
                Resumen general de planes e ingresos
            </flux:text>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 mb-6">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <flux:heading level="3" size="xs" class="text-xs font-medium text-black/60 dark:text-white/60">
                        Planes activos (Estacionamiento)
                    </flux:heading>

                    <flux:text
                        class="inline-flex items-center rounded-md px-2 py-1 text-[11px] font-semibold
                               bg-custom-blue/10 text-custom-blue
                               dark:bg-custom-blue/15 dark:text-custom-blue"
                    >
                        Hoy
                    </flux:text>
                </div>

                <flux:text variant="strong" class="mt-3 text-3xl font-bold text-custom-blue">
                    {{ number_format($active_parking) }}
                </flux:text>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <flux:heading level="3" size="xs" class="text-xs font-medium text-black/60 dark:text-white/60">
                        Planes activos (Usuario)
                    </flux:heading>

                    <flux:text
                        class="inline-flex items-center rounded-md px-2 py-1 text-[11px] font-semibold
                               bg-custom-blue-dark/10 text-custom-blue-dark
                               dark:bg-custom-blue-dark/20 dark:text-custom-blue-dark"
                    >
                        Hoy
                    </flux:text>
                </div>

                <flux:text variant="strong" class="mt-3 text-3xl font-bold text-custom-blue-dark">
                    {{ number_format($active_user) }}
                </flux:text>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <flux:heading level="3" size="xs" class="text-xs font-medium text-black/60 dark:text-white/60">
                        Ingresos acumulados
                    </flux:heading>

                    <flux:text
                        class="inline-flex items-center rounded-md px-2 py-1 text-[11px] font-semibold
                               bg-custom-green/10 text-custom-green
                               dark:bg-custom-green/20 dark:text-custom-green"
                    >
                        MXN
                    </flux:text>
                </div>

                <flux:text
                    variant="strong"
                    class="mt-3 text-3xl font-bold text-custom-green"
                >
                    ${{ number_format($total_revenue, 2) }}
                </flux:text>
            </div>
        </div>

        <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm bg-white dark:bg-zinc-900">

            <div class="flex flex-col gap-4 border-b border-zinc-200 dark:border-zinc-700 p-4 md:flex-row md:items-end md:justify-between">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <flux:label for="filter-role" class="text-xs font-medium text-black dark:text-white">
                            Tipo de usuario
                        </flux:label>

                        <flux:select
                            id="filter-role"
                            name="role"
                            class="mt-1 text-xs md:text-sm"
                            onchange="this.form.submit()"
                        >
                            <option value="" {{ $role_filter === '' ? 'selected' : '' }}>Todos</option>
                            <option value="2" {{ $role_filter === '2' ? 'selected' : '' }}>
                                Estacionamientos
                            </option>
                            <option value="3" {{ $role_filter === '3' ? 'selected' : '' }}>
                                Usuarios app
                            </option>
                            <option value="dynamic" {{ $role_filter === 'dynamic' ? 'selected' : '' }}>
                                Usuarios dinámicos
                            </option>
                        </flux:select>
                    </div>

                    <div>
                        <flux:label for="filter-plan" class="text-xs font-medium text-black dark:text-white">
                            Plan
                        </flux:label>

                        <flux:select
                            id="filter-plan"
                            name="plan"
                            class="mt-1 text-xs md:text-sm"
                            onchange="this.form.submit()"
                        >
                            <option value="" {{ $plan_filter === '' ? 'selected' : '' }}>Todos</option>
                            @foreach ($plans as $plan)
                                <option
                                    value="{{ $plan->id }}"
                                    {{ (string) $plan->id === (string) $plan_filter ? 'selected' : '' }}
                                >
                                    [{{ $plan->type === 'parking' ? 'Estacionamiento' : 'Usuario' }}]
                                    {{ $plan->name }} - ${{ number_format($plan->price, 2) }}
                                </option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="w-full md:w-64">
                        <flux:label for="filter-q" class="text-xs font-medium text-black dark:text-white">
                            Búsqueda
                        </flux:label>

                        <flux:input
                            id="filter-q"
                            name="q"
                            type="text"
                            placeholder="Nombre, correo o teléfono"
                            class="mt-1 text-xs md:text-sm"
                            value="{{ $search }}"
                        />
                    </div>

                    <div class="mt-2">
                        <flux:button
                            type="submit"
                            size="sm"
                            icon="magnifying-glass"
                            icon-variant="outline"
                            class="mt-3"
                        >
                            Aplicar filtros
                        </flux:button>
                    </div>
                </form>
            </div>

            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-100 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                            <flux:text class="text-sm font-semibold text-black dark:text-white">
                                ID
                            </flux:text>
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                            <flux:text class="text-sm font-semibold text-black dark:text-white">
                                Nombre
                            </flux:text>
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                            <flux:text class="text-sm font-semibold text-black dark:text-white">
                                Correo
                            </flux:text>
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                            <flux:text class="text-sm font-semibold text-black dark:text-white">
                                Teléfono
                            </flux:text>
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                            <flux:text class="text-sm font-semibold text-black dark:text-white">
                                Rol
                            </flux:text>
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                            <flux:text class="text-sm font-semibold text-black dark:text-white">
                                Tipo usuario
                            </flux:text>
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                            <flux:text class="text-sm font-semibold text-black dark:text-white">
                                Plan
                            </flux:text>
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                    @forelse ($users as $user_item)
                        @php
                            $role_id   = $user_item->id_role;
                            $role_name = optional($user_item->role)->name;

                            if ($role_id === 2) 
                            {
                                $type_label = 'Estacionamiento';
                            } elseif ($role_id === 3) 
                            {
                                $type_label = 'Usuario app';
                            } elseif (is_null($role_id)) 
                            {
                                $type_label = 'Dinámico';
                            } elseif ($role_id === 1) 
                            {
                                $type_label = 'Admin';
                            } else 
                            {
                                $type_label = 'Otro';
                            }

                            $plan = $user_item->plan;
                        @endphp

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition">
                            <td class="px-4 py-3 text-sm text-black dark:text-white align-top">
                                {{ $user_item->id }}
                            </td>

                            <td class="px-4 py-3 text-sm text-black dark:text-white font-medium align-top">
                                {{ $user_item->name }}
                            </td>

                            <td class="px-4 py-3 text-sm text-black/80 dark:text-white/80 align-top">
                                {{ $user_item->email }}
                            </td>

                            <td class="px-4 py-3 text-sm text-black/80 dark:text-white/80 align-top">
                                {{ $user_item->phone_number }}
                            </td>

                            <td class="px-4 py-3 text-sm text-black/80 dark:text-white/80 align-top">
                                {{ $role_name ?? '—' }}
                            </td>

                            <td class="px-4 py-3 text-sm text-black/80 dark:text-white/80 align-top">
                                {{ $type_label }}
                            </td>

                            <td class="px-4 py-3 text-sm text-black/80 dark:text-white/80 align-top">
                                @if ($plan)
                                    [{{ $plan->type === 'parking' ? 'Estacionamiento' : 'Usuario' }}]
                                    {{ $plan->name }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="7"
                                class="px-4 py-4 text-center text-sm text-black/60 dark:text-white/60"
                            >
                                No se encontraron usuarios con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($users->hasPages())
                <div class="border-t border-zinc-200 dark:border-zinc-800 px-4 py-3">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>