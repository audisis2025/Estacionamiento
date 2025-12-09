{{-- 
* Nombre de la vista           : index.blade.php
* Descripción de la vista      : Pantalla para gestionar las solicitudes de aprobación de clientes.
* Fecha de creación            : 05/11/2025
* Elaboró                      : Elian Pérez
* Fecha de liberación          : 06/11/2025
* Autorizó                     : Angel Davila
* Version                      : 3.0
* Fecha de mantenimiento       : 09/12/2025
* Folio de mantenimiento       : 
* Tipo de mantenimiento        : Correctivo
* Descripción del mantenimiento: Descuento sin fecha de expiración y cancelación manual.
* Responsable                  : Elian Pérez
* Revisor                      : Angel Davila
--}}

<x-layouts.app :title="__('Solicitudes de clientes')">
    <div class="p-6 w-full max-w-6xl mx-auto space-y-8">

        <div class="flex items-center justify-between mb-2">
            <flux:heading level="2" size="xl" class="text-2xl !font-black text-black dark:text-white">
                Solicitudes de clientes
            </flux:heading>
        </div>

        <form id="client-approvals-filter-form" method="GET" class="max-w-xs mb-6">
            <flux:label for="filter-phone" class="text-xs font-medium text-black dark:text-white">
                Filtrar por número de teléfono
            </flux:label>

            <flux:input
                id="filter-phone"
                name="phone"
                type="text"
                placeholder="Ej. 5550001111"
                class="mt-1 text-xs md:text-sm"
                value="{{ $phone ?? '' }}"
            />
        </form>

        <div class="flex items-center justify-between">
            <flux:heading level="2" size="lg" class="text-xl !font-black text-black dark:text-white">
                Solicitudes pendientes
            </flux:heading>
        </div>

        @if ($pending->isEmpty())
            <flux:text class="text-black/60 dark:text-white/60">
                No hay solicitudes pendientes.
            </flux:text>
        @else
            <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-100 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                                Usuario
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                                Tipo
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                                Estado
                            </th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-black dark:text-white">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                        @foreach ($pending as $r)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition">
                                <td class="px-4 py-3 text-sm text-black dark:text-white">
                                    <div class="font-medium">
                                        {{ $r->user->name }}
                                    </div>
                                    <div class="text-xs text-black/60 dark:text-white/60">
                                        {{ $r->user->phone_number }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-sm text-black/80 dark:text-white/80">
                                    {{ $r->clientType->type_name }}
                                </td>

                                <td class="px-4 py-3 text-sm">
                                    <span
                                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                                               bg-custom-orange text-white">
                                        Pendiente
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-sm text-center">
                                    <div class="flex items-center justify-center gap-3">

                                        <form
                                            method="POST"
                                            action="{{ route('parking.client-approvals.approve', $r) }}"
                                            class="inline-block"
                                        >
                                            @csrf

                                            <flux:button
                                                size="sm"
                                                icon="check"
                                                icon-variant="outline"
                                                variant="primary"
                                                as="button"
                                                type="submit"
                                                class="bg-green-600 hover:bg-green-700 text-white text-xs md:text-sm"
                                            >
                                                Confirmar
                                            </flux:button>
                                        </form>

                                        <form
                                            method="POST"
                                            action="{{ route('parking.client-approvals.reject', $r) }}"
                                            class="reject-form inline-block"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <flux:button
                                                size="sm"
                                                icon="trash"
                                                icon-variant="outline"
                                                variant="danger"
                                                as="button"
                                                type="submit"
                                                class="text-xs md:text-sm"
                                            >
                                                Rechazar
                                            </flux:button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="flex items-center justify-between mt-8">
            <flux:heading level="2" size="lg" class="text-xl !font-black text-black dark:text-white">
                Aprobados
            </flux:heading>
        </div>

        <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-100 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                            Usuario
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                            Tipo
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                            Estado
                        </th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-black dark:text-white">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                    @forelse ($approved as $r)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition">
                            <td class="px-4 py-3 text-sm text-black dark:text-white">
                                <div class="font-medium">
                                    {{ $r->user->name }}
                                </div>
                                <div class="text-xs text-black/60 dark:text-white/60">
                                    {{ $r->user->phone_number }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm text-black/80 dark:text-white/80">
                                {{ $r->clientType->type_name }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span
                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                                           bg-custom-green/15 text-custom-green
                                           dark:bg-custom-green/20 dark:text-custom-green">
                                    Activo
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm text-center">
                                <form
                                    method="POST"
                                    action="{{ route('parking.client-approvals.reject', $r) }}"
                                    class="reject-form inline-block"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <flux:button
                                        size="sm"
                                        icon="x-circle"
                                        icon-variant="outline"
                                        variant="danger"
                                        as="button"
                                        type="submit"
                                        class="text-xs md:text-sm"
                                    >
                                        Cancelar beneficio
                                    </flux:button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <td colspan="4" class="px-4 py-4 justify-center text-center">
                            <flux:text class="text-xs text-black/60 dark:text-white/60">
                                Sin registros
                            </flux:text>
                        </td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        (function () 
        {
            const form = document.getElementById('client-approvals-filter-form');
            if (!form) return;

            const phoneInput = document.getElementById('filter-phone');
            let timer = null;

            if (phoneInput) {
                phoneInput.addEventListener('input', () => 
                {
                    clearTimeout(timer);
                    timer = setTimeout(() => {
                        form.requestSubmit();
                    }, 500);
                });

                phoneInput.addEventListener('keypress', (e) => 
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

    @push('js')
        <script>
            function bindApprovalUI()
            {
                document.querySelectorAll('.reject-form').forEach((form) =>
                {
                    if (form.dataset.bound) return;
                    form.dataset.bound = '1';

                    form.addEventListener('submit', (e) =>
                    {
                        e.preventDefault();

                        Swal.fire(
                        {
                            title: '¿Eliminar beneficio?',
                            text: 'Esta acción eliminará la solicitud / beneficio.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#42A958',
                            cancelButtonColor: '#EE0000',
                            confirmButtonText: 'Confirmar',
                            cancelButtonText: 'Cancelar',
                        }).then((res) =>
                        {
                            if (res.isConfirmed)
                            {
                                form.submit();
                            }
                        });
                    });
                });
            }

            document.addEventListener('DOMContentLoaded', bindApprovalUI);
            document.addEventListener('livewire:navigated', bindApprovalUI);
        </script>
    @endpush

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
                    confirmButtonColor: '#494949'
                });
            });
        </script>
    @endif
</x-layouts.app>
