{{--
* Nombre de la vista           : _form.blade.php
* Descripción de la vista      : Página de formulario para la creación o edición de un estacionamiento.
* Fecha de creación            : 04/11/2025
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

@php
    use Illuminate\Support\Str;
    $formId = $formId ?? 'parking-form-' . Str::random(6);
    $method = $method ?? 'POST';
@endphp

<form id="{{ $formId }}" method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 space-y-4">
        <flux:input
            name="name"
            :label="__('Nombre del estacionamiento')"
            value="{{ old('name', $parking->name ?? '') }}"
            required
            placeholder="Ej. Parking Centro"
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:input
                name="lat"
                id="{{ $formId }}-lat"
                type="number"
                step="any"
                :label="__('Latitud')"
                value="{{ old('lat', $parking->latitude_coordinate ?? ($parking->lat ?? '')) }}"
                required
            />

            <flux:input
                name="lng"
                id="{{ $formId }}-lng"
                type="number"
                step="any"
                :label="__('Longitud')"
                value="{{ old('lng', $parking->longitude_coordinate ?? ($parking->lng ?? '')) }}"
                required
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:field class="w-full">
                <flux:label for="{{ $formId }}-type" class="text-sm font-medium text-black dark:text-white">
                    Tipo de estacionamiento
                </flux:label>

                @php $type = (int) old('type', $parking->type ?? 0); @endphp

                <flux:select
                    id="{{ $formId }}-type"
                    name="type"
                    class="mt-1 block w-full"
                >
                    <option value="0" @selected($type === 0)>Tiempo libre (tarifa fija)</option>
                    <option value="1" @selected($type === 1)>Por hora</option>
                    <option value="2" @selected($type === 2)>Mixto (hora + fija)</option>
                </flux:select>
            </flux:field>

            <flux:input
                name="price_hour"
                id="{{ $formId }}-price-hour"
                type="number"
                step="0.01"
                min="0"
                :label="__('Precio por hora')"
                placeholder="Ej. 25.00"
                value="{{ old('price_hour', ($type === 1 || $type === 2) ? ($parking->price ?? '') : '') }}"
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:input
                name="price_flat"
                id="{{ $formId }}-price-flat"
                type="number"
                step="0.01"
                min="0"
                :label="__('Precio fijo (tiempo libre)')"
                placeholder="Ej. 50.00"
                value="{{ old('price_flat', ($type === 0 || $type === 2) ? ($parking->price_flat ?? '') : '') }}"
            />

            <div class="text-sm text-zinc-500 dark:text-zinc-400 flex items-end">
                <span id="{{ $formId }}-price-hint">
                    @switch($type)
                        @case(0) La tarifa usada será la fija (tiempo libre). @break
                        @case(1) La tarifa usada será por hora. @break
                        @case(2) En mixto se usan ambas: por hora al calcular y fija cuando aplique. @break
                    @endswitch
                </span>
            </div>
        </div>

        <div class="flex justify-center-safe flex-wrap gap-3 ">
            <flux:button
                type="button"
                id="{{ $formId }}-btn-geo"
                icon="map-pin"
                variant="primary"
                icon-variant="outline"
                class="bg-custom-blue hover:bg-custom-blue-dark text-white text-sm"
            >
                Usar mi ubicación
            </flux:button>

            <flux:button
                type="button"
                id="{{ $formId }}-btn-center"
                icon="cursor-arrow-rays"
                icon-variant="outline"
                variant="primary"
                class="bg-custom-blue hover:bg-custom-blue-dark text-white text-sm"
            >
                Centrar mapa
            </flux:button>
        </div>
    </div>

    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-3">
        <div id="{{ $formId }}-map" class="w-full h-96 min-h-[380px] rounded-lg overflow-hidden"></div>

        <flux:text class="mt-2 text-xs text-black/60 dark:text-white/60">
            Haz clic en el mapa o arrastra el marcador para establecer la ubicación del estacionamiento.
        </flux:text>
    </div>

    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5">
        <flux:heading level="3" size="lg" class="text-lg !font-black mb-3 text-black dark:text-white">
            Horario del estacionamiento
        </flux:heading>

        <div class="mb-4">
            <flux:checkbox
                name="same_schedule"
                id="{{ $formId }}-same-schedule"
                label="Usar el mismo horario para todos los días"
                value="1"
                :checked="(bool) old('same_schedule', false)"
            />
        </div>

        <div id="{{ $formId }}-global-schedule" class="grid grid-cols-1 md:grid-cols-2 gap-3 hidden">
            <flux:input
                type="time"
                name="schedules[all][open]"
                label="Hora de apertura (todos los días)"
                value="{{ old('schedules.all.open') }}"
            />

            <flux:input
                type="time"
                name="schedules[all][close]"
                label="Hora de cierre (todos los días)"
                value="{{ old('schedules.all.close') }}"
            />
        </div>

        <div id="{{ $formId }}-days-schedule" class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach ($days ?? [] as $day)
                @php
                    $existing  = isset($parking) ? $parking->schedules->firstWhere('id_day', $day->id) : null;
                    $openOld   = old("schedules.$day->id.open", $existing?->opening_time);
                    $closeOld  = old("schedules.$day->id.close", $existing?->closing_time);
                    $closedOld = (bool) old("schedules.$day->id.closed", 0);
                @endphp

                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-3">
                    <div class="flex items-center justify-between mb-2">
                        <div class="font-medium text-black dark:text-white">
                            {{ $day->name }}
                        </div>

                        <input type="hidden" name="schedules[{{ $day->id }}][closed]" value="0">

                        <flux:checkbox
                            name="schedules[{{ $day->id }}][closed]"
                            value="1"
                            label="Cerrado"
                            class="ml-2"
                            id="{{ $formId }}-day-{{ $day->id }}-closed"
                            :checked="(bool) $closedOld"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <flux:input
                            type="time"
                            name="schedules[{{ $day->id }}][open]"
                            label="Apertura"
                            value="{{ $openOld }}"
                            id="{{ $formId }}-day-{{ $day->id }}-open"
                        />

                        <flux:input
                            type="time"
                            name="schedules[{{ $day->id }}][close]"
                            label="Cierre"
                            value="{{ $closeOld }}"
                            id="{{ $formId }}-day-{{ $day->id }}-close"
                        />
                    </div>
                </div>
            @endforeach
        </div>

        <flux:text class="mt-3 text-xs text-black/60 dark:text:white/60">
            Si no usas horario global, ingresa la hora de apertura y cierre por día o marca “Cerrado” cuando aplique.
        </flux:text>
    </div>

    <div class="flex justify-center-safe">
        <flux:button
            type="submit"
            variant="primary"
            icon-variant="outline"
            :icon="$method === 'PUT' ? 'check-circle' : 'plus'"
            class="bg-blue-600 hover:bg-blue-700 text-white"
        >
            {{ $method === 'PUT' ? 'Guardar cambios' : 'Crear' }}
        </flux:button>
    </div>
</form>

@push('styles')
    <style>
        #{{ $formId }}-map 
        {
            background: #e8f3e6;
        }

        .gm-style 
        {
            min-height: 380px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function () 
        {
            const id = @json($formId);
            const $ = (s) => document.getElementById(`${id}-${s}`);

            function togglePrices() 
            {
                const typeElement = $('type');
                if (!typeElement) return;
                
                const t = parseInt(typeElement.value ?? '0', 10);

                const priceHourInput = $('price-hour');
                const priceFlatInput = $('price-flat');

                const priceHourWrapper = priceHourInput?.closest('div');
                const priceFlatWrapper = priceFlatInput?.closest('div');

                if (t === 0) 
                {
                    priceHourInput?.removeAttribute('required');
                    priceFlatInput?.setAttribute('required', 'required');

                    priceHourWrapper?.classList.add('hidden');
                    priceFlatWrapper?.classList.remove('hidden');
                } else if (t === 1) 
                {
                    priceHourInput?.setAttribute('required', 'required');
                    priceFlatInput?.removeAttribute('required');

                    priceHourWrapper?.classList.remove('hidden');
                    priceFlatWrapper?.classList.add('hidden');
                } else 
                {
                    priceHourInput?.setAttribute('required', 'required');
                    priceFlatInput?.setAttribute('required', 'required');

                    priceHourWrapper?.classList.remove('hidden');
                    priceFlatWrapper?.classList.remove('hidden');
                }

                const hint = $('price-hint');
                if (hint) 
                {
                    hint.textContent = t === 0 
                        ? 'La tarifa usada será la fija (tiempo libre).' 
                        : t === 1 
                        ? 'La tarifa usada será por hora.' 
                        : 'En mixto se usan ambas: por hora y fija.';
                }
            }

            function run()
            {
                requestAnimationFrame(() => 
                {
                    const typeElement = $('type');
                    if (typeElement) 
                    {
                        typeElement.removeEventListener('change', togglePrices);
                        typeElement.addEventListener('change', togglePrices);
                        togglePrices();
                    }
                });
            }

            if (document.readyState === 'loading') 
            {
                document.addEventListener('DOMContentLoaded', run, { once: true });
            } else 
            {
                run();
            }
            
            document.addEventListener('livewire:navigated', () => 
            {
                setTimeout(run, 100);
            });
        })();
    </script>

    <script>
        window.runParkingForm = () => window.initParkingForm(@json($formId));

        document.addEventListener("DOMContentLoaded", window.runParkingForm);
        document.addEventListener("livewire:navigated", window.runParkingForm);
    </script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ urlencode(config('services.google_maps.key')) }}&callback=__initParkingMap__"
        async
        defer>
    </script>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => 
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
                    icon: 'error',
                    title: 'Errores en el formulario',
                    html: errorList,
                });
            });
        </script>
    @endif
@endpush