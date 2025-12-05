{{--
* Nombre de la vista           : index.blade.php
* Descripción de la vista      : Pantalla para gestionar las solicitudes de aprobación de clientes.
* Fecha de creación            : 05/11/2025
* Elaboró                      : Elian Pérez
* Fecha de liberación          : 06/11/2025
* Autorizó                     : Angel Davila
* Version                      : 2.0
* Fecha de mantenimiento       : 16/11/2025
* Folio de mantenimiento       : 
* Tipo de mantenimiento        : Correctivo
* Descripción del mantenimiento: Se realizaron ajustes en la interfaz
* Responsable                  : Elian Pérez
* Revisor                      : Angel Davila
--}}
<x-layouts.app :title="__('Solicitudes de clientes')">
    <div class="p-6 w-full max-w-6xl mx-auto space-y-8">

        <div class="flex items-center justify-between">
            <flux:heading level="2" size="xl" class="text-2xl !font-black text-black dark:text-white">
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
                                <flux:text class="text-sm font-semibold text-black dark:text-white">
                                    Usuario
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
                                    Tipo solicitado
                                </flux:text>
                            </th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-black dark:text-white">
                                <flux:text class="text-sm font-semibold text-black dark:text-white">
                                    Acciones
                                </flux:text>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                        @foreach ($pending as $r)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition">
                                <td class="px-4 py-3 text-sm text-black dark:text-white">
                                    {{ $r->user->name }}
                                </td>

                                <td class="px-4 py-3 text-sm text-black/80 dark:text-white/80">
                                    {{ $r->user->email }}
                                </td>

                                <td class="px-4 py-3 text-sm text-black/80 dark:text-white/80">
                                    {{ $r->user->phone_number }}
                                </td>

                                <td class="px-4 py-3 text-sm text-black dark:text-white">
                                    {{ $r->clientType->type_name }}
                                    —
                                    {{ $r->clientType->discount_type
                                        ? '$' . number_format($r->clientType->amount, 2)
                                        : number_format($r->clientType->amount, 2) . ' %' }}
                                </td>

                                <td class="px-4 py-3 text-sm">
                                    <div class="flex items-center justify-center gap-3">

                                        <form
                                            method="POST"
                                            action="{{ route('parking.client-approvals.approve', $r) }}"
                                            class="approve-form"
                                        >
                                            @csrf
                                            <input type="hidden" name="expiration_date" value="">

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
                                            class="reject-form"
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
                                                Eliminar
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

        <div class="flex items-center justify-between mt-4">
            <flux:heading level="2" size="xl" class="text-2xl !font-black text-black dark:text-white">
                Aprobados recientes
            </flux:heading>
        </div>

        <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-100 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                            <flux:text class="text-sm font-semibold text-black dark:text-white">
                                Usuario
                            </flux:text>
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                            <flux:text class="text-sm font-semibold text-black dark:text-white">
                                Tipo
                            </flux:text>
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-black dark:text-white">
                            <flux:text class="text-sm font-semibold text-black dark:text-white">
                                Expira
                            </flux:text>
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                    @forelse ($approved as $r)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition">
                            <td class="px-4 py-3 text-sm text-black dark:text-white">
                                {{ $r->user->name }}
                            </td>

                            <td class="px-4 py-3 text-sm text-black/80 dark:text-white/80">
                                {{ $r->clientType->type_name }}
                            </td>

                            <td class="px-4 py-3 text-sm text-black/80 dark:text-white/80">
                                {{ optional($r->expiration_date)->format('Y-m-d') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                class="px-4 py-6 text-center text-sm text-black/60 dark:text-white/60"
                                colspan="3"
                            >
                                Sin registros
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('js')
        <script>
            function bindApprovalUI()
            {
                document.querySelectorAll('.approve-form').forEach((form) =>
                {
                    if (form.dataset.bound) return;
                    form.dataset.bound = '1';

                    form.addEventListener('submit', async (e) =>
                    {
                        e.preventDefault();

                        const min = new Date(Date.now() + 86400000).toISOString().slice(0, 10);

                        const { value: date } = await Swal.fire(
                        {
                            title: 'Fecha de expiración',
                            input: 'date',
                            inputAttributes:
                            {
                                min: min,
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Aprobar',
                            cancelButtonText: 'Cancelar',
                            cancelButtonColor: '#EE0000',
                            confirmButtonColor: '#3182ce'
                        });

                        if (date)
                        {
                            form.querySelector('input[name="expiration_date"]').value = date;
                            form.submit();
                        }
                    });
                });

                document.querySelectorAll('.reject-form').forEach((form) =>
                {
                    if (form.dataset.bound) return;
                    form.dataset.bound = '1';

                    form.addEventListener('submit', (e) =>
                    {
                        e.preventDefault();

                        Swal.fire(
                        {
                            title: '¿Rechazar solicitud?',
                            text: 'Esta acción eliminará la solicitud.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3182ce',
                            cancelButtonColor: '#EE0000',
                            confirmButtonText: 'Sí, rechazar',
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
</x-layouts.app>
