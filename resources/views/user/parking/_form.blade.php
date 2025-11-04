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

    {{-- 1) Datos del estacionamiento (arriba) --}}
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

    {{-- 2) Mapa (debajo del formulario) --}}
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-3">
        <div id="{{ $formId }}-map" class="w-full h-96 min-h-[380px] rounded-lg overflow-hidden"></div>
        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
            Haz click en el mapa o arrastra el marcador para establecer la ubicación.
        </p>
    </div>

    {{-- 3/4) Horarios --}}
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-5">
        <h2 class="text-lg font-semibold mb-3">Horario del estacionamiento</h2>

        <div class="mb-4">
            <flux:checkbox name="same_schedule" id="{{ $formId }}-same-schedule"
                label="Usar el mismo horario para todos los días" :checked="false" />
        </div>

        {{-- Horario global (visible si se marca la casilla) --}}
        <div id="{{ $formId }}-global-schedule" class="grid grid-cols-1 md:grid-cols-2 gap-3 hidden">
            <flux:input type="time" name="schedules[all][open]" label="Hora de apertura (todos los días)" />
            <flux:input type="time" name="schedules[all][close]" label="Hora de cierre (todos los días)" />
        </div>

        {{-- Horario por día (visible si NO se marca la casilla) --}}
        <div id="{{ $formId }}-days-schedule" class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach ($days ?? [] as $day)
                @php
                    $existing = isset($parking) ? $parking->schedules->firstWhere('id_day', $day->id) : null;
                    $openOld = old("schedules.$day->id.open", $existing?->opening_time);
                    $closeOld = old("schedules.$day->id.close", $existing?->closing_time);
                @endphp

                <div class="rounded-lg border border-neutral-200 dark:border-neutral-700 p-3">
                    <div class="flex items-center justify-between mb-2">
                        <div class="font-medium">{{ $day->name }}</div>

                        {{-- Agrego un hidden para asegurar clave "closed" incluso si el checkbox queda desmarcado --}}
                        <input type="hidden" name="schedules[{{ $day->id }}][closed]" value="0">
                        <flux:checkbox name="schedules[{{ $day->id }}][closed]" value="1" label="Cerrado"
                            class="ml-2" id="{{ $formId }}-day-{{ $day->id }}-closed" />
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

    {{-- 5) Un solo botón para todo --}}
    <div class="flex justify-end">
        <flux:button type="submit" variant="primary">
            {{ $method === 'PUT' ? 'Actualizar todo' : 'Guardar todo' }}
        </flux:button>
    </div>
</form>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous">
    <style>
        #{{ $formId }}-map {
            background: #e8f3e6;
        }

        .leaflet-container {
            min-height: 380px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>
    <script>
        (function() {
            const id = @json($formId);
            const $ = (suf) => document.getElementById(`${id}-${suf}`);
            const mapDivId = `${id}-map`;

            // ---------- MAPA ----------
            window.__leafletMaps ??= {};

            function hasSize(el) {
                return el && el.offsetWidth > 0 && el.offsetHeight > 0;
            }

            function initMap() {
                const mapDiv = document.getElementById(mapDivId);
                if (!mapDiv) return;

                if (window.__leafletMaps[mapDivId]) {
                    try {
                        window.__leafletMaps[mapDivId].remove();
                    } catch (_) {}
                    delete window.__leafletMaps[mapDivId];
                }

                const latInput = $('lat');
                const lngInput = $('lng');
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

                const startLat = parseFloat(latInput?.value || '19.4326');
                const startLng = parseFloat(lngInput?.value || '-99.1332');

                const map = L.map(mapDivId, {
                        zoomControl: true,
                        scrollWheelZoom: true
                    })
                    .setView([startLat, startLng], 13);
                window.__leafletMaps[mapDivId] = map;

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap',
                }).addTo(map);

                const marker = L.marker([startLat, startLng], {
                    draggable: true
                }).addTo(map);

                function setInputs(lat, lng, pan = false) {
                    if (latInput) latInput.value = Number(lat).toFixed(6);
                    if (lngInput) lngInput.value = Number(lng).toFixed(6);
                    marker.setLatLng([lat, lng]);
                    if (pan) map.setView([lat, lng], 16);
                }

                map.on('click', (e) => setInputs(e.latlng.lat, e.latlng.lng, true));
                marker.on('dragend', (e) => {
                    const {
                        lat,
                        lng
                    } = e.target.getLatLng();
                    setInputs(lat, lng, false);
                });

                const invalidate = () => {
                    if (map && map._loaded) requestAnimationFrame(() => map.invalidateSize({
                        debounceMoveend: true
                    }));
                };
                map.whenReady(() => setTimeout(invalidate, 0));
                window.addEventListener('resize', invalidate);

                $('btn-center')?.addEventListener('click', () => {
                    invalidate();
                    map.setView(marker.getLatLng(), 16);
                });

                $('btn-geo')?.addEventListener('click', () => {
                    const geoBtn = $('btn-geo');
                    const isSecure = location.protocol === 'https:' ||
                        location.hostname === 'localhost' ||
                        location.hostname === '127.0.0.1';

                    if (!isSecure) return alert(
                        'Para usar tu ubicación, abre el sitio en HTTPS o usa http://localhost.');
                    if (!navigator.geolocation) return alert('Tu navegador no soporta geolocalización.');

                    const setBtn = (disabled, text) => {
                        if (!geoBtn) return;
                        geoBtn.disabled = !!disabled;
                        if (typeof text === 'string') geoBtn.textContent = text;
                    };
                    const originalText = geoBtn ? geoBtn.textContent : 'Usar mi ubicación';
                    setBtn(true, 'Buscando…');

                    let best = {
                        acc: Infinity,
                        lat: null,
                        lng: null
                    };
                    let watchId = null,
                        finished = false;

                    const stop = () => {
                        if (watchId !== null) navigator.geolocation.clearWatch(watchId);
                        watchId = null;
                        finished = true;
                        setBtn(false, originalText);
                    };
                    const applyCandidate = (pos, pan = false) => {
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
                        (pos) => applyCandidate(pos, true),
                        () => {}, {
                            enableHighAccuracy: true,
                            timeout: 15000,
                            maximumAge: 0
                        }
                    );

                    watchId = navigator.geolocation.watchPosition(
                        (pos) => {
                            if (finished) return;
                            applyCandidate(pos, !isFinite(best.acc));
                            if (pos.coords && pos.coords.accuracy <= 10) stop();
                        },
                        (err) => {
                            console.warn('watchPosition error', err);
                            if (!isFinite(best.acc)) {
                                let msg = 'No se pudo obtener tu ubicación.';
                                if (err?.code === 1) msg = 'Permiso de ubicación denegado.';
                                else if (err?.code === 2) msg = 'Ubicación no disponible.';
                                else if (err?.code === 3) msg = 'La solicitud expiró.';
                                alert(msg);
                            }
                            stop();
                        }, {
                            enableHighAccuracy: true,
                            timeout: 20000,
                            maximumAge: 0
                        }
                    );

                    setTimeout(() => {
                        if (finished) return;
                        if (isFinite(best.acc)) setInputs(best.lat, best.lng, true);
                        else alert('No se pudo determinar tu ubicación con precisión.');
                        stop();
                    }, 12000);
                });
            }

            function initWhenReady() {
                const el = document.getElementById(mapDivId);
                if (!el) return;
                if (hasSize(el)) return initMap();
                const ro = new ResizeObserver(() => {
                    if (hasSize(el)) {
                        ro.disconnect();
                        initMap();
                    }
                });
                ro.observe(el);
            }

            // ---------- HORARIOS ----------
            function initScheduleUi() {
                const sameCheck = $(`same-schedule`);
                const globalDiv = $(`global-schedule`);
                const daysDiv = $(`days-schedule`);

                // helper: habilitar/required de inputs por día según "Cerrado"
                const applyClosedState = (chk) => {
                    const card = chk.closest('.rounded-lg');
                    if (!card) return;
                    const timeInputs = card.querySelectorAll('input[type="time"]');

                    const closed = chk.checked;
                    timeInputs.forEach(inp => {
                        inp.disabled = closed;
                        // CLAVE del fix: solo exigir (required) si NO está cerrado
                        if (closed) {
                            inp.removeAttribute('required');
                        } else {
                            // no forzamos required globalmente, solo quitamos el disabled
                            // y dejamos que el usuario decida qué días llenar
                            // Si quieres forzar ambos en cada día abierto, descomenta:
                            // inp.setAttribute('required', 'required');
                        }
                    });
                };

                // helper: cuando el usuario cambie entre global/per-día
                const toggleGlobal = () => {
                    const activeGlobal = !!sameCheck?.checked;
                    globalDiv?.classList.toggle('hidden', !activeGlobal);
                    daysDiv?.classList.toggle('hidden', activeGlobal);

                    // Si se activa global, quita required en per-día
                    if (activeGlobal && daysDiv) {
                        daysDiv.querySelectorAll('input[type="time"]').forEach(i => i.removeAttribute('required'));
                    }

                    // Si se vuelve a per-día, re-sincroniza "Cerrado" y estados
                    if (!activeGlobal && daysDiv) {
                        daysDiv.querySelectorAll('input[type="checkbox"][name$="[closed]"]').forEach(
                            applyClosedState);
                    }

                    // También maneja required en global cuando esté activo
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
                };

                sameCheck?.addEventListener('change', toggleGlobal);

                // Inicializar per-día
                if (daysDiv) {
                    const boxes = daysDiv.querySelectorAll('input[type="checkbox"][name$="[closed]"]');
                    boxes.forEach(chk => {
                        applyClosedState(chk); // estado inicial
                        chk.addEventListener('change', () => applyClosedState(chk));
                    });
                }
                toggleGlobal();
            }

            const run = () => {
                initWhenReady();
                initScheduleUi();
            };
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', run, {
                    once: true
                });
            } else {
                run();
            }
            document.addEventListener('livewire:navigated', run);
        })();
    </script>
@endpush

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
