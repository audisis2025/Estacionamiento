<x-layouts.app :title="__('Entradas abiertas')">
    <div class="max-w-6xl mx-auto p-6 space-y-6">
        <h1 class="text-2xl font-semibold">Entradas abiertas</h1>

        {{-- Mostrar tipo de tarifa --}}
        @php $parking = auth()->user()->parking; @endphp
        <p class="text-sm text-zinc-500">
            Tipo de cobro:
            @if ($parking->type === 1)
                Por hora — ${{ number_format($parking->price, 2) }} / hora
            @else
                Tarifa fija — ${{ number_format($parking->price, 2) }}
            @endif
        </p>

        <form method="GET" class="flex gap-3 items-end">
            <div>
                <label class="text-xs text-zinc-500">Buscar por teléfono</label>
                <input type="text" name="q" value="{{ $phone }}" class="border rounded-lg px-3 py-2"
                    placeholder="Ej. 7221234567">
            </div>
            <button class="px-4 py-2 rounded-lg border">Filtrar</button>
        </form>

        {{-- Tabla --}}
        <div class="rounded-xl border overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-zinc-100 dark:bg-zinc-800">
                    <tr class="text-left">
                        <th class="px-4 py-3 font-semibold">ID</th>
                        <th class="px-4 py-3 font-semibold">Usuario</th>
                        <th class="px-4 py-3 font-semibold">Entrada</th>
                        <th class="px-4 py-3 font-semibold text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y">
                    @forelse ($entries as $t)
                        <tr>
                            <td class="px-4 py-3">{{ $t->id }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $t->user->name ?? 'N/D' }}</div>
                                <div class="text-xs text-zinc-500">{{ $t->user->email ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($t->entry_date)->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('parking.entries.release', $t) }}"
                                    class="form-release">
                                    @csrf
                                    <button type="submit" class="px-3 py-2 rounded-lg border hover:bg-zinc-50 text-sm">
                                        Liberar salida
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-zinc-500">
                                No hay entradas pendientes de salida.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $entries->links() }}
    </div>

    {{-- SweetAlert mensajes --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Confirmación al liberar
        document.querySelectorAll('.form-release').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const result = await Swal.fire({
                    title: '¿Liberar salida?',
                    text: 'Se calculará automáticamente el monto según el tiempo y la tarifa.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, liberar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6b7280'
                });
                if (result.isConfirmed) form.submit();
            });
        });

        // Éxito
        @if (session('ok'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: "{{ session('ok') }}",
                confirmButtonColor: '#22c55e'
            });
        @endif

        // Error
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                confirmButtonColor: '#dc2626'
            });
        @endif
    </script>
</x-layouts.app>
