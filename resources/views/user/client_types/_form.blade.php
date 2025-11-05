@props(['action', 'method' => 'POST', 'clientType' => null])

<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf
    @if (strtoupper($method) === 'PUT')
        @method('PUT')
    @endif

    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-5 space-y-4">

        <flux:input
            name="typename"
            :label="__('Nombre del tipo de cliente')"
            value="{{ old('typename', $clientType->typename ?? '') }}"
            placeholder="Ej. Taxistas, Residentes, Empleados"
            required
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="block">
                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipo de descuento</span>
                @php $dt = (int) old('discount_type', $clientType->discount_type ?? 0); @endphp
                <select name="discount_type"
                        class="mt-1 block w-full rounded-md border border-neutral-300 dark:border-neutral-700 p-2 text-sm bg-white dark:bg-zinc-900">
                    <option value="0" {{ $dt === 0 ? 'selected' : '' }}>Porcentaje (%)</option>
                    <option value="1" {{ $dt === 1 ? 'selected' : '' }}>Cantidad fija ($)</option>
                </select>
            </label>

            <flux:input
                name="amount"
                type="number"
                step="0.01"
                min="0"
                :label="__('Monto')"
                placeholder="Ej. 10 (si es %) o 20.00 (si es $)"
                value="{{ old('amount', $clientType->amount ?? '') }}"
                required
            />
        </div>

        <div class="flex justify-end">
            <flux:button type="submit" variant="primary">
                {{ $clientType ? 'Guardar cambios' : 'Crear tipo' }}
            </flux:button>
        </div>
    </div>
</form>
