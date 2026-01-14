{{--
* Nombre de la vista           : _form.blade.php
* Descripción de la vista      : Formulario para la creación y edición de lectores QR.
* Fecha de creación            : 03/11/2025
* Elaboró                      : Elian Pérez
* Fecha de liberación          : 04/11/2025
* Autorizó                     : Angel Davila
* Version                      : 1.1
* Fecha de mantenimiento       : 16/11/2025
* Folio de mantenimiento       : L0010
* Tipo de mantenimiento        : Correctivo
* Descripción del mantenimiento: Se realizaron ajustes en la validación del formulario.
* Responsable                  : Elian Pérez
* Revisor                      : Angel Davila
--}}
@php
    use Illuminate\Support\Str;

    $formId = $formId ?? 'qr-form-' . Str::random(6);
    $method = $method ?? 'POST';
@endphp

<form id="{{ $formId }}" method="POST" action="{{ $action }}" class="space-y-5" novalidate>
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-5">

        <div class="grid gap-4 md:grid-cols-2">
            <flux:input
                name="serial_number"
                :label="__('Número de serie')"
                :error="null"
                value="{{ old('serial_number', $reader->serial_number ?? '') }}"
                placeholder="SN-ABC123"
                required
            />

            <flux:field class="w-full">
                <flux:label class="text-sm font-medium text-black dark:text-white">
                    Sentido
                </flux:label>

                @php
                    $sense = (int) old('sense', $reader->sense ?? 2);
                @endphp

                <flux:select
                    name="sense"
                    class="mt-1 w-full"
                    required
                >
                    <option value="0" @selected($sense === 0)>Entrada</option>
                    <option value="1" @selected($sense === 1)>Salida</option>
                    <option value="2" @selected($sense === 2)>Mixto</option>
                </flux:select>
            </flux:field>

        </div>

        <div class="flex justify-end gap-3">

            @if ($method === 'PUT')
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
                    Guardar lector
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
