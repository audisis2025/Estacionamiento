{{--
* Nombre de la vista           : _form.blade.php
* Descripción de la vista      : Formulario para crear o editar tipos de cliente.
* Fecha de creación            : 03/11/2025
* Elaboró                      : Elian Pérez
* Fecha de liberación          : 04/11/2025
* Autorizó                     : Angel Davila
* Version                      : 1.0
* Fecha de mantenimiento       : 
* Folio de mantenimiento       :
* Tipo de mantenimiento        : 
* Descripción del mantenimiento: 
* Responsable                  : 
* Revisor                      : 
--}}
@props(['action', 'method' => 'POST', 'clientType' => null])

<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf
    @if (strtoupper($method) === 'PUT')
        @method('PUT')
    @endif

    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-4">
        <flux:input
            name="typename"
            :label="__('Nombre del tipo de cliente')"
            value="{{ old('typename', $clientType->typename ?? '') }}"
            placeholder="Ej. Taxistas, Residentes, Empleados"
            required
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="block">
                <span class="text-sm font-medium text-black dark:text-white">
                    Tipo de descuento
                </span>

                @php
                    $dt = (int) old('discount_type', $clientType->discount_type ?? 0);
                @endphp

                <select
                    name="discount_type"
                    class="mt-1 block w-full rounded-md border border-zinc-200 dark:border-zinc-700
                           bg-white dark:bg-zinc-900 text-sm text-black dark:text-white
                           px-2 py-2 focus:outline-none focus:ring-2 focus:ring-custom-blue focus:border-custom-blue"
                >
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

        <div class="flex justify-end gap-3">
            @if ($clientType)
                <flux:button
                    type="submit"
                    variant="primary"
                    icon="check-circle"
                    icon-variant="outline"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm"
                >
                    Guardar cambios
                </flux:button>
            @else
                <flux:button
                    type="submit"
                    variant="primary"
                    icon="plus"
                    icon-variant="outline"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm"
                >
                    Crear tipo
                </flux:button>
            @endif
        </div>
    </div>
</form>

@push('scripts')
    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", () =>
            {
                let errorList = `
                    <ul style="text-align:left; margin-left:20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                `;

                Swal.fire(
                {
                    icon: "error",
                    title: "Errores en el formulario",
                    html: errorList,
                });
            });
        </script>
    @endif
@endpush
