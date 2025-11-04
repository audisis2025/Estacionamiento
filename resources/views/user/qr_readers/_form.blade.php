@php
    use Illuminate\Support\Str;
    $formId = $formId ?? 'qr-form-' . Str::random(6);
    $method = $method ?? 'POST';
@endphp

<form id="{{ $formId }}" method="POST" action="{{ $action }}" class="space-y-5">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-5 space-y-5">

        <div class="grid gap-4 md:grid-cols-2">
            <flux:input name="serial_number" :label="__('Número de serie')"
                value="{{ old('serial_number', $reader->serial_number ?? '') }}" placeholder="SN-ABC123" required />

            <label class="block">
                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Sentido</span>
                @php $sense = (int) old('sense', $reader->sense ?? 2); @endphp
                <select name="sense"
                    class="mt-1 block w-full rounded-md border border-neutral-300 dark:border-neutral-700 p-2 text-sm bg-white dark:bg-zinc-900"
                    required>
                    <option value="0" {{ $sense === 0 ? 'selected' : '' }}>Entrada</option>
                    <option value="1" {{ $sense === 1 ? 'selected' : '' }}>Salida</option>
                    <option value="2" {{ $sense === 2 ? 'selected' : '' }}>Mixto</option>
                </select>
            </label>
        </div>

        <div class="flex justify-end gap-3">
            <flux:button variant="ghost" :href="route('parking.qr-readers.index')" wire:navigate>Cancelar</flux:button>
            <flux:button type="submit" variant="primary">
                {{ $method === 'PUT' ? 'Guardar cambios' : 'Crear lector' }}
            </flux:button>
        </div>
    </div>
</form>

@push('scripts')
    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                let errorList = `
        <ul style="text-align:left; margin-left:20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    `;

                Swal.fire({
                    icon: "error",
                    title: "Errores en el formulario",
                    html: errorList,
                    confirmButtonText: "Entendido",
                    confirmButtonColor: "#3085d6"
                });
            });
        </script>
    @endif
@endpush
