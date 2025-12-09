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
            name="type_name"
            :label="__('Nombre del tipo de cliente')"
            value="{{ old('typename', $client_type->type_name ?? '') }}"
            placeholder="Ej. Taxistas, Residentes, Empleados"
            required
        />

        <flux:field class="w-full">
            <flux:label class="text-sm font-medium text-black dark:text-white">
                Tipo de descuento
            </flux:label>

            @php
                $dt = (int) old('discount_type', $client_type->discount_type ?? 0);
            @endphp

            <flux:select
                name="discount_type"
                class="mt-1 w-full"
            >
                <option value="0" @selected($dt === 0)>Porcentaje (%)</option>
                <option value="1" @selected($dt === 1)>Cantidad fija ($)</option>
            </flux:select>
        </flux:field>

            <flux:input
                name="amount"
                type="number"
                step="0.01"
                min="0"
                :label="__('Monto')"
                placeholder="Ej. 10 (si es %) o 20.00 (si es $)"
                value="{{ old('amount', $client_type->amount ?? '') }}"
                required
            />
        </div>

        <div class="flex justify-end gap-3">
            @if ($client_type)
                <flux:button
                    type="submit"
                    variant="primary"
                    icon="check-circle"
                    icon-variant="outline"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm"
                >
                    Guardar cambios
                </flux:button>
            @else
                <flux:button
                    type="submit"
                    variant="primary"
                    icon="check-circle"
                    icon-variant="outline"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm"
                >
                    Guardar cliente
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
                    confirmButtonColor: '#494949'
                });
            });
        </script>
    @endif
@endpush
