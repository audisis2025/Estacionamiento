{{-- 
* Nombre de la vista           : index.blade.php
* Descripción de la vista      : Administración de suscripciones de usuarios (planes).
* Fecha de creación            : 08/12/2025
* Elaboró                      : Elian Pérez
* Fecha de liberación          : 08/12/2025
* Autorizó                     : Angel Davila
* Version                      : 1.0
* Fecha de mantenimiento       :
* Folio de mantenimiento       :
* Tipo de mantenimiento        : 
* Descripción del mantenimiento: 
* Responsable                  : 
* Revisor                      : 
--}}

<x-layouts.admin :title="__('Administrar suscripciones')">
    <div class="p-6 w-full max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-5">
            <flux:heading level="2" size="xl" class="text-2xl !font-black text-black dark:text-white">
                Administrar suscripciones
            </flux:heading>

            <flux:text class="text-xs text-black/60 dark:text-white/60">
                Gestiona los planes de estacionamientos y usuarios de la app.
            </flux:text>
        </div>

        <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm bg-white dark:bg-zinc-900">
            <div class="flex flex-col gap-4 border-b border-zinc-200 dark:border-zinc-700 p-4 md:flex-row md:items-end md:justify-between">
                <form id="subscriptions-filters-form" method="GET" class="flex flex-wrap gap-3 items-end">
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
                            <option value="" {{ $role_filter === '' ? 'selected' : '' }}>
                                Todos
                            </option>
                            <option value="2" {{ $role_filter === '2' ? 'selected' : '' }}>
                                Estacionamientos
                            </option>
                            <option value="3" {{ $role_filter === '3' ? 'selected' : '' }}>
                                Usuarios app
                            </option>
                        </flux:select>
                    </div>

                    <div>
                        <flux:label for="filter-status" class="text-xs font-medium text-black dark:text-white">
                            Estado del plan
                        </flux:label>

                        <flux:select
                            id="filter-status"
                            name="status"
                            class="mt-1 text-xs md:text-sm"
                            onchange="this.form.submit()"
                        >
                            <option value="" {{ $status_filter === '' ? 'selected' : '' }}>
                                Todos
                            </option>
                            <option value="active" {{ $status_filter === 'active' ? 'selected' : '' }}>
                                Activo
                            </option>
                            <option value="expired" {{ $status_filter === 'expired' ? 'selected' : '' }}>
                                Vencido
                            </option>
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
                </form>
            </div>

            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-100 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                            ID
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                            Usuario
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                            Tipo usuario
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                            Plan
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                            Fin de plan
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                            Estado plan
                        </th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-black dark:text-white">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                    @forelse ($users as $user)
                        @php
                            $roleId = (int) $user->id_role;
                            $plan = $user->plan;
                            $today = \Illuminate\Support\Carbon::today();
                            $typeLabel = $roleId === 2 ? 'Estacionamiento' : 'Usuario app';

                            $isLifetime = $user->id_plan == 4 && is_null($user->end_date);
                            $isActivePlan = $user->id_plan !== null && ($isLifetime || ($user->end_date && $user->end_date->gte($today)));
                        @endphp

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition">
                            <td class="px-4 py-3 text-sm text-black dark:text-white align-top">
                                {{ $user->id }}
                            </td>

                            <td class="px-4 py-3 text-sm text-black dark:text-white align-top">
                                <div class="font-medium">
                                    {{ $user->name }}
                                </div>
                                <div class="text-xs text-black/60 dark:text-white/60">
                                    {{ $user->email }}
                                </div>
                                <div class="text-xs text-black/60 dark:text-white/60">
                                    {{ $user->phone_number }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm text-black/80 dark:text-white/80 align-top">
                                {{ $typeLabel }}
                            </td>

                            <td class="px-4 py-3 text-sm text-black/80 dark:text-white/80 align-top">
                                @if ($plan)
                                    [{{ $plan->type === 'parking' ? 'Estacionamiento' : 'Usuario' }}]
                                    {{ $plan->name }}
                                @else
                                    Sin plan
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm text-black/80 dark:text-white/80 align-top">
                                @if ($isLifetime)
                                    Sin vencimiento
                                @elseif ($user->end_date)
                                    {{ $user->end_date->format('Y-m-d') }}
                                @else
                                    —
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm align-top">
                                @if ($isActivePlan)
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-custom-green/15 text-custom-green dark:bg-custom-green/20 dark:text-custom-green">
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-red-500/10 text-red-600 dark:bg-red-500/20 dark:text-red-400">
                                        Vencido
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm text-center align-top">
                                <div class="flex items-center justify-center gap-2">
                                    <form method="POST" action="{{ route('admin.subscriptions.renew', $user) }}" class="inline-block">
                                        @csrf
                                        @method('PATCH')

                                        <flux:button
                                            type="submit"
                                            size="sm"
                                            icon="arrow-path"
                                            icon-variant="outline"
                                            variant="primary"
                                            class="text-xs md:text-sm bg-green-600 hover:bg-green-700 text-white"
                                        >
                                            Renovar
                                        </flux:button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('admin.subscriptions.cancel', $user) }}" class="inline-block cancel-subscription-form">
                                        @csrf
                                        @method('PATCH')

                                        <flux:button
                                            type="submit"
                                            size="sm"
                                            icon="x-circle"
                                            icon-variant="outline"
                                            variant="primary"
                                            class="text-xs md:text-sm bg-red-600 hover:bg-red-700 text-white"
                                        >
                                            Cancelar
                                        </flux:button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-4 justify-center text-center">
                                <flux:text class="text-xs text-black/60 dark:text-white/60">
                                    No se encontraron usuarios con suscripciones según los filtros aplicados.
                                </flux:text>
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

    <script>
        (function () 
        {
            const form = document.getElementById('subscriptions-filters-form');
            if (!form) return;

            const qInput = document.getElementById('filter-q');
            let timer = null;

            if (qInput) 
            {
                qInput.addEventListener('input', () => 
                {
                    clearTimeout(timer);
                    timer = setTimeout(() => 
                    {
                        form.requestSubmit();
                    }, 500);
                });

                qInput.addEventListener('keypress', (e) => 
                {
                    if (e.key === 'Enter') 
                    {
                        e.preventDefault();
                        clearTimeout(timer);
                        form.requestSubmit();
                    }
                });
            }
        })();
    </script>

    @if (session('swal'))
        <script>
            document.addEventListener('DOMContentLoaded', function () 
            {
                const data = @json(session('swal'));

                Swal.fire(
                {
                    icon: data.icon ?? 'info',
                    title: data.title ?? 'Mensaje',
                    text: data.text ?? '',
                    confirmButtonColor: '#494949',
                });
            });
        </script>
    @endif

    @push('js')
        <script>
            function attachCancelListeners()
            {
                document.querySelectorAll('.cancel-subscription-form').forEach((form) =>
                {
                    if (form.dataset.listenerAttached)
                    {
                        return;
                    }

                    form.dataset.listenerAttached = 'true';

                    form.addEventListener('submit', function (event)
                    {
                        event.preventDefault();

                        Swal.fire(
                        {
                            title: "¿Cancelar suscripción?",
                            text: "Esta acción cancelará el plan del usuario.",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#42A958",
                            cancelButtonColor: "#EE0000",
                            confirmButtonText: "Confirmar",
                            cancelButtonText: "Cancelar",
                        }).then((result) =>
                        {
                            if (result.isConfirmed)
                            {
                                form.submit();
                            }
                        });
                    });
                });
            }

            document.addEventListener('DOMContentLoaded', attachCancelListeners);
            document.addEventListener('livewire:navigated', attachCancelListeners);
        </script>
    @endpush
</x-layouts.admin>
