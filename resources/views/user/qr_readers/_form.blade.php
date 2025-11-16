{{--
* Nombre de la vista           : _form.blade.php
* Descripción de la vista      : Formulario para la creación y edición de lectores QR.
* Fecha de creación            : 03/11/2025
* Elaboró                      : Elian Pérez
* Fecha de liberación          : 04/11/2025
* Autorizó                     : Angel Davila
* Version                      : 1.0
* Fecha de mantenimiento       : 16/11/2025
* Folio de mantenimiento       : 
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

            <label class="block">
                <span class="text-sm font-medium text-black dark:text-white">
                    Sentido
                </span>

                @php
                    $sense = (int) old('sense', $reader->sense ?? 2);
                @endphp

                <select
                    name="sense"
                    class="mt-1 block w-full rounded-md border border-zinc-200 dark:border-zinc-700
                           bg-white dark:bg-zinc-900 text-sm text-black dark:text-white
                           px-2 py-2 focus:outline-none focus:ring-2 focus:ring-custom-blue focus:border-custom-blue"
                    required
                >
                    <option value="0" {{ $sense === 0 ? 'selected' : '' }}>Entrada</option>
                    <option value="1" {{ $sense === 1 ? 'selected' : '' }}>Salida</option>
                    <option value="2" {{ $sense === 2 ? 'selected' : '' }}>Mixto</option>
                </select>
            </label>
        </div>

        <div class="flex justify-end gap-3">

            @if ($method === 'PUT')
                <flux:button
                    type="submit"
                    variant="primary"
                    icon="arrow-path"
                    icon-variant="outline"
                    class="bg-blue-600 hover:bg-blue-600 text-white text-sm"
                >
                    Guardar lector
                </flux:button>
            @else
                <flux:button
                    type="submit"
                    variant="primary"
                    icon="plus"
                    icon-variant="outline"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm"
                >
                    Crear lector
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
