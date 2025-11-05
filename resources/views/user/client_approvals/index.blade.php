<x-layouts.app :title="__('Solicitudes de clientes')">
    <div class="p-6 w-full max-w-6xl mx-auto space-y-8">

        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold">Solicitudes pendientes</h2>
        </div>

        @if ($pending->isEmpty())
            <div class="text-center text-zinc-500 dark:text-zinc-400 py-8">
                No hay solicitudes pendientes.
            </div>
        @else
            <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-xl">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-100 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Usuario</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Correo</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Teléfono</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Tipo solicitado</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                        @foreach ($pending as $r)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60">
                                <td class="px-4 py-3 text-sm">{{ $r->user->name }}</td>
                                <td class="px-4 py-3 text-sm">{{ $r->user->email }}</td>
                                <td class="px-4 py-3 text-sm">{{ $r->user->phone_number }}</td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $r->clientType->typename }}
                                    —
                                    {{ $r->clientType->discount_type ? '$' . $r->clientType->amount : $r->clientType->amount . '%' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex items-center justify-center gap-3">
                                        <form method="POST"
                                            action="{{ route('parking.client-approvals.approve', $r) }}"
                                            class="approve-form">
                                            @csrf
                                            <input type="hidden" name="expiration_date" value="">
                                            <flux:button size="sm" icon="check" variant="primary" as="button"
                                                type="submit">
                                                Aprobar
                                            </flux:button>
                                        </form>

                                        <form method="POST" action="{{ route('parking.client-approvals.reject', $r) }}"
                                            class="reject-form">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button size="sm" icon="x-mark" variant="danger" as="button"
                                                type="submit">
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

        <h2 class="text-2xl font-bold">Aprobados recientes</h2>
        <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-xl">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-100 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Usuario</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Tipo</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Expira</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                    @forelse($approved as $r)
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ $r->user->name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $r->clientType->typename }}</td>
                            <td class="px-4 py-3 text-sm">{{ optional($r->expiration_date)->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-6 text-center text-sm text-zinc-500" colspan="3">Sin registros</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('js')
        <script>
            function bindApprovalUI() {
                // Aprobar -> pedir fecha con SweetAlert
                document.querySelectorAll('.approve-form').forEach(form => {
                    if (form.dataset.bound) return;
                    form.dataset.bound = '1';
                    form.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const min = new Date(Date.now() + 86400000).toISOString().slice(0, 10);
                        const {
                            value: date
                        } = await Swal.fire({
                            title: 'Fecha de expiración',
                            input: 'date',
                            inputAttributes: {
                                min
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Aprobar',
                            cancelButtonText: 'Cancelar',
                        });
                        if (date) {
                            form.querySelector('input[name="expiration_date"]').value = date;
                            form.submit();
                        }
                    });
                });

                // Rechazar -> confirmación
                document.querySelectorAll('.reject-form').forEach(form => {
                    if (form.dataset.bound) return;
                    form.dataset.bound = '1';
                    form.addEventListener('submit', (e) => {
                        e.preventDefault();
                        Swal.fire({
                            title: '¿Rechazar solicitud?',
                            text: 'Esta acción eliminará la solicitud.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc2626',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Sí, rechazar',
                            cancelButtonText: 'Cancelar',
                        }).then(res => {
                            if (res.isConfirmed) form.submit();
                        });
                    });
                });
            }

            document.addEventListener('DOMContentLoaded', bindApprovalUI);
            document.addEventListener('livewire:navigated', bindApprovalUI);
        </script>
    @endpush
</x-layouts.app>
