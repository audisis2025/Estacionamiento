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

    {{-- 1) Datos del estacionamiento --}}
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-5 space-y-4">
        <flux:input name="name" :label="__('Nombre del estacionamiento')"
            value="{{ old('name', $parking->name ?? '') }}" required placeholder="Ej. Parking Centro" />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:input name="lat" id="{{ $formId }}-lat" type="number" step="any" :label="__('Latitud')"
                value="{{ old('lat', $parking->latitude_coordinate ?? ($parking->lat ?? '')) }}" required />
            <flux:input name="lng" id="{{ $formId }}-lng" type="number" step="any"
                :label="__('Longitud')"
                value="{{ old('lng', $parking->longitude_coordinate ?? ($parking->lng ?? '')) }}" required />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="block">
                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipo de estacionamiento</span>
                @php $type = (int) old('type', $parking->type ?? 0); @endphp
                <select name="type" id="{{ $formId }}-type"
                    class="mt-1 block w-full rounded-md border border-neutral-300 dark:border-neutral-700 p-2 text-sm bg-white dark:bg-zinc-900">
                    <option value="0" {{ $type === 0 ? 'selected' : '' }}>Tiempo libre</option>
                    <option value="1" {{ $type === 1 ? 'selected' : '' }}>Precio por hora</option>
                </select>
            </label>

            <flux:input name="price" id="{{ $formId }}-price" type="number" step="0.01" min="0"
                :label="__('Precio')"
                placeholder="{{ $type === 1 ? 'Ej. 25.00 (por hora)' : 'Ej. 50.00 (tarifa fija)' }}"
                value="{{ old('price', $parking->price ?? '') }}" />
        </div>

        <div class="flex gap-3">
            <button type="button" id="{{ $formId }}-btn-geo"
                class="bg-custom-blue text-white rounded-lg px-3 py-2 hover:bg-custom-blue-dark">
                Usar mi ubicación
            </button>
            <button type="button" id="{{ $formId }}-btn-center" class="border rounded-lg px-3 py-2">
                Centrar mapa
            </button>
        </div>
    </div>

    {{-- 2) Mapa --}}
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-3">
        <div id="{{ $formId }}-map" class="w-full h-96 min-h-[380px] rounded-lg overflow-hidden"></div>
        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
            Haz click en el mapa o arrastra el marcador para establecer la ubicación.
        </p>
    </div>

    {{-- 3) Horarios --}}
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-5">
        <h2 class="text-lg font-semibold mb-3">Horario del estacionamiento</h2>

        <div class="mb-4">
            <flux:checkbox name="same_schedule" id="{{ $formId }}-same-schedule"
                label="Usar el mismo horario para todos los días" value="1"
                :checked="(bool) old('same_schedule', false)" />

        </div>

        {{-- Horario global (se muestra si está marcada la casilla) --}}
        <div id="{{ $formId }}-global-schedule" class="grid grid-cols-1 md:grid-cols-2 gap-3 hidden">
            <flux:input type="time" name="schedules[all][open]" label="Hora de apertura (todos los días)"
                value="{{ old('schedules.all.open') }}" />
            <flux:input type="time" name="schedules[all][close]" label="Hora de cierre (todos los días)"
                value="{{ old('schedules.all.close') }}" />
        </div>

        {{-- Horario por día (visible si NO está marcada la casilla) --}}
        <div id="{{ $formId }}-days-schedule" class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach ($days ?? [] as $day)
                @php
                    $existing = isset($parking) ? $parking->schedules->firstWhere('id_day', $day->id) : null;
                    $openOld = old("schedules.$day->id.open", $existing?->opening_time);
                    $closeOld = old("schedules.$day->id.close", $existing?->closing_time);
                    $closedOld = (bool) old("schedules.$day->id.closed", 0);
                @endphp

                <div class="rounded-lg border border-neutral-200 dark:border-neutral-700 p-3">
                    <div class="flex items-center justify-between mb-2">
                        <div class="font-medium">{{ $day->name }}</div>

                        {{-- Enviar siempre la clave closed --}}
                        <input type="hidden" name="schedules[{{ $day->id }}][closed]" value="0">
                        <flux:checkbox name="schedules[{{ $day->id }}][closed]" value="1" label="Cerrado"
                            class="ml-2" id="{{ $formId }}-day-{{ $day->id }}-closed"
                            :checked="(bool) $closedOld" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <flux:input type="time" name="schedules[{{ $day->id }}][open]" label="Apertura"
                            value="{{ $openOld }}" id="{{ $formId }}-day-{{ $day->id }}-open" />
                        <flux:input type="time" name="schedules[{{ $day->id }}][close]" label="Cierre"
                            value="{{ $closeOld }}" id="{{ $formId }}-day-{{ $day->id }}-close" />
                    </div>
                </div>
            @endforeach
        </div>

        <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
            Si no usas horario global, ingresa apertura y cierre por día o marca “Cerrado”.
        </p>
    </div>

    {{-- 4) Guardar --}}
    <div class="flex justify-end">
        <flux:button type="submit" variant="primary">
            {{ $method === 'PUT' ? 'Actualizar todo' : 'Guardar todo' }}
        </flux:button>
    </div>
</form>

@push('styles')
    <style>
        #{{ $formId }}-map {
            background: #e8f3e6;
        }

        .gm-style {
            min-height: 380px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            const id = @json($formId);
            const $ = (suf) => document.getElementById(`${id}-${suf}`);
            const mapDivId = `${id}-map`;
            let map, marker;

            /* === UI de horarios (global vs por-día) === */
            function initScheduleUi() {
                const sameCheck = $(`same-schedule`);
                const globalDiv = $(`global-schedule`);
                const daysDiv = $(`days-schedule`);

                const applyClosedState = (chk) => {
                    const card = chk.closest('.rounded-lg');
                    if (!card) return;
                    const timeInputs = card.querySelectorAll('input[type="time"]');
                    const closed = chk.checked;
                    timeInputs.forEach(inp => {
                        inp.disabled = closed;
                        if (closed) inp.removeAttribute('required');
                    });
                };

                const toggleGlobal = () => {
                    const activeGlobal = !!sameCheck?.checked;

                    globalDiv?.classList.toggle('hidden', !activeGlobal);
                    daysDiv?.classList.toggle('hidden', activeGlobal);

                    // required en global
                    const gOpen = document.querySelector(`input[name="schedules[all][open]"]`);
                    const gClose = document.querySelector(`input[name="schedules[all][close]"]`);
                    if (gOpen && gClose) {
                        if (activeGlobal) {
                            gOpen.setAttribute('required', 'required');
                            gClose.setAttribute('required', 'required');
                        } else {
                            gOpen.removeAttribute('required');
                            gClose.removeAttribute('required');
                        }
                    }

                    // Al volver a per-día, re-sincroniza "Cerrado"
                    if (!activeGlobal && daysDiv) {
                        daysDiv.querySelectorAll('input[type="checkbox"][name$="[closed]"]').forEach(
                            applyClosedState);
                    }
                };

                sameCheck?.addEventListener('change', toggleGlobal);

                // Inicializar estado de "Cerrado" por día
                if (daysDiv) {
                    const boxes = daysDiv.querySelectorAll('input[type="checkbox"][name$="[closed]"]');
                    boxes.forEach(chk => {
                        applyClosedState(chk);
                        chk.addEventListener('change', () => applyClosedState(chk));
                    });
                }

                // Vista inicial (respeta old('same_schedule'))
                toggleGlobal();
            }

            /* === UI general (precio, centrar, geoloc) === */
            function initUiBindings() {
                const typeSel = $('type');
                const priceInp = $('price');

                const updatePriceUi = () => {
                    if (!priceInp || !typeSel) return;
                    priceInp.placeholder = parseInt(typeSel.value, 10) === 1 ?
                        'Ej. 25.00 (por hora)' :
                        'Ej. 50.00 (tarifa fija)';
                };
                updatePriceUi();
                typeSel?.addEventListener('change', updatePriceUi);

                $('btn-center')?.addEventListener('click', () => {
                    if (!marker || !map) return;
                    map.setZoom(Math.max(map.getZoom(), 16));
                    map.setCenter(marker.getPosition());
                });

                $('btn-geo')?.addEventListener('click', () => {
                    const isSecure = location.protocol === 'https:' ||
                        location.hostname === 'localhost' ||
                        location.hostname === '127.0.0.1';
                    if (!isSecure) return alert(
                        'Para usar tu ubicación, abre el sitio en HTTPS o usa http://localhost.');
                    if (!navigator.geolocation) return alert('Tu navegador no soporta geolocalización.');

                    const btn = $('btn-geo');
                    const original = btn?.textContent || 'Usar mi ubicación';
                    const setBtn = (dis, text) => {
                        if (!btn) return;
                        btn.disabled = !!dis;
                        if (text) btn.textContent = text;
                    };
                    setBtn(true, 'Buscando…');

                    let best = {
                        acc: Infinity,
                        lat: null,
                        lng: null
                    };
                    let watchId = null;
                    const stop = () => {
                        if (watchId != null) navigator.geolocation.clearWatch(watchId);
                        setBtn(false, original);
                    };

                    const apply = (pos, pan = false) => {
                        const {
                            latitude,
                            longitude,
                            accuracy
                        } = pos.coords || {};
                        if (latitude == null || longitude == null) return;
                        if (accuracy < best.acc) {
                            best = {
                                acc: accuracy,
                                lat: latitude,
                                lng: longitude
                            };
                            setInputs(latitude, longitude, pan);
                        }
                    };

                    navigator.geolocation.getCurrentPosition(
                        (p) => apply(p, true),
                        () => {}, {
                            enableHighAccuracy: true,
                            timeout: 15000,
                            maximumAge: 0
                        }
                    );

                    watchId = navigator.geolocation.watchPosition(
                        (p) => {
                            apply(p, !isFinite(best.acc));
                            if (p.coords?.accuracy <= 10) stop();
                        },
                        (e) => {
                            console.warn(e);
                            if (!isFinite(best.acc)) alert('No se pudo obtener tu ubicación.');
                            stop();
                        }, {
                            enableHighAccuracy: true,
                            timeout: 20000,
                            maximumAge: 0
                        }
                    );

                    setTimeout(() => {
                        if (isFinite(best.acc)) setInputs(best.lat, best.lng, true);
                        else alert('No se pudo determinar tu ubicación con precisión.');
                        stop();
                    }, 12000);
                });
            }

            function setInputs(lat, lng, pan = false) {
                const latInput = $('lat');
                const lngInput = $('lng');
                if (latInput) latInput.value = Number(lat).toFixed(6);
                if (lngInput) lngInput.value = Number(lng).toFixed(6);
                if (marker) marker.setPosition({
                    lat: Number(lat),
                    lng: Number(lng)
                });
                if (pan && map) map.setCenter({
                    lat: Number(lat),
                    lng: Number(lng)
                });
            }

            /* === Google Maps === */
            function initGoogleMap() {
                const mapDiv = document.getElementById(mapDivId);
                if (!mapDiv) return;

                const startLat = parseFloat(($('lat')?.value) || '19.4326'); // CDMX
                const startLng = parseFloat(($('lng')?.value) || '-99.1332');

                map = new google.maps.Map(mapDiv, {
                    center: {
                        lat: startLat,
                        lng: startLng
                    },
                    zoom: 13,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: true,
                });

                marker = new google.maps.Marker({
                    position: {
                        lat: startLat,
                        lng: startLng
                    },
                    map,
                    draggable: true,
                });

                map.addListener('click', (e) => setInputs(e.latLng.lat(), e.latLng.lng(), true));
                marker.addEventListener('dragend', () => {
                    const pos = marker.getPosition();
                    setInputs(pos.lat(), pos.lng(), false);
                });

                const ro = new ResizeObserver(() => google.maps.event.trigger(map, 'resize'));
                ro.observe(mapDiv);
            }

            // Callback que invoca Google cuando termina de cargar
            window.__initParkingMap__ = function() {
                initUiBindings();
                initScheduleUi(); // <-- importante
                initGoogleMap();
            };

            // Asegurar ejecución cuando el DOM está listo
            const runWhenDom = () => {
                if (window.google && window.google.maps) window.__initParkingMap__();
            };
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', runWhenDom, {
                    once: true
                });
            } else {
                runWhenDom();
            }
            document.addEventListener('livewire:navigated', runWhenDom);
        })();
    </script>

    {{-- Carga la API de Google con tu key y callback --}}
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ urlencode(config('services.google_maps.key')) }}&callback=__initParkingMap__"
        async defer></script>

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
