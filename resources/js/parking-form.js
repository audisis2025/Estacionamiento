
export default function initParkingForm(formId) {
    const $ = (suffix) => document.getElementById(`${formId}-${suffix}`);
    const mapDivId = `${formId}-map`;
    let map, marker;

    function initScheduleUi() {
        const sameCheck = $('same-schedule');
        const globalDiv = $('global-schedule');
        const daysDiv = $('days-schedule');

        const applyClosedState = (chk) => {
            const card = chk.closest('.rounded-lg');
            if (!card) return;

            const timeInputs = card.querySelectorAll('input[type="time"]');
            const closed = chk.checked;

            timeInputs.forEach((inp) => {
                inp.disabled = closed;
                if (closed) {
                    inp.removeAttribute('required');
                }
            });
        };

        const toggleGlobal = () => {
            const activeGlobal = !!sameCheck?.checked;

            globalDiv?.classList.toggle('hidden', !activeGlobal);
            daysDiv?.classList.toggle('hidden', activeGlobal);

            const gOpen = document.querySelector('input[name="schedules[all][open]"]');
            const gClose = document.querySelector('input[name="schedules[all][close]"]');

            if (gOpen && gClose) {
                if (activeGlobal) {
                    gOpen.setAttribute('required', 'required');
                    gClose.setAttribute('required', 'required');
                }
                else {
                    gOpen.removeAttribute('required');
                    gClose.removeAttribute('required');
                }
            }

            if (!activeGlobal && daysDiv) {
                daysDiv
                    .querySelectorAll('input[type="checkbox"][name$="[closed]"]')
                    .forEach(applyClosedState);
            }
        };

        sameCheck?.addEventListener('change', toggleGlobal);

        if (daysDiv) {
            const boxes = daysDiv.querySelectorAll('input[type="checkbox"][name$="[closed]"]');

            boxes.forEach((chk) => {
                applyClosedState(chk);
                chk.addEventListener('change', () => applyClosedState(chk));
            });
        }

        toggleGlobal();
    }

    function initUiBindings() {
        const typeSel = $('type');
        const priceInp = $('price');

        const updatePriceUi = () => {
            if (!priceInp || !typeSel) return;

            priceInp.placeholder = parseInt(typeSel.value, 10) === 1
                ? 'Ej. 25.00 (por hora)'
                : 'Ej. 50.00 (tarifa fija)';
        };

        updatePriceUi();
        typeSel?.addEventListener('change', updatePriceUi);

        $('btn-center')?.addEventListener('click', () => {
            if (!marker || !map) return;

            map.setZoom(Math.max(map.getZoom(), 16));
            map.setCenter(marker.getPosition());
        });

        $('btn-geo')?.addEventListener('click', () => {
            const isSecure = location.protocol === 'https:'
                || location.hostname === 'localhost'
                || location.hostname === '127.0.0.1';

            if (!isSecure) {
                Swal.fire(
                    {
                        icon: 'warning',
                        title: 'Geolocalización no disponible',
                        text: 'Para usar tu ubicación, abre el sitio en HTTPS o usa http://localhost.',
                    });
                return;
            }

            if (!navigator.geolocation) {
                Swal.fire(
                    {
                        icon: 'error',
                        title: 'Geolocalización no soportada',
                        text: 'Tu navegador no soporta geolocalización.',
                    });
                return;
            }

            const btn = $('btn-geo');
            const original = btn?.textContent || 'Usar mi ubicación';

            const setBtn = (dis, text) => {
                if (!btn) return;
                btn.disabled = !!dis;
                if (text) btn.textContent = text;
            };

            setBtn(true, 'Buscando…');

            let best =
            {
                acc: Infinity,
                lat: null,
                lng: null,
            };

            let watchId = null;

            const stop = () => {
                if (watchId != null) {
                    navigator.geolocation.clearWatch(watchId);
                }
                setBtn(false, original);
            };

            const apply = (pos, pan = false) => {
                const { latitude, longitude, accuracy } = pos.coords || {};

                if (latitude == null || longitude == null) return;

                if (accuracy < best.acc) {
                    best = { acc: accuracy, lat: latitude, lng: longitude };
                    setInputs(latitude, longitude, pan);
                }
            };

            navigator.geolocation.getCurrentPosition(
                (p) => apply(p, true),
                () => { },
                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0,
                }
            );

            watchId = navigator.geolocation.watchPosition(
                (p) => {
                    apply(p, !isFinite(best.acc));
                    if (p.coords?.accuracy <= 10) {
                        stop();
                    }
                },
                (e) => {
                    console.warn(e);

                    if (!isFinite(best.acc)) {
                        Swal.fire(
                            {
                                icon: 'error',
                                title: 'No se pudo obtener tu ubicación',
                                text: 'Intenta nuevamente o establece la ubicación manualmente en el mapa.',
                            });
                    }

                    stop();
                },
                {
                    enableHighAccuracy: true,
                    timeout: 20000,
                    maximumAge: 0,
                }
            );

            setTimeout(() => {
                if (isFinite(best.acc)) {
                    setInputs(best.lat, best.lng, true);
                }
                else {
                    Swal.fire(
                        {
                            icon: 'error',
                            title: 'Ubicación no determinada',
                            text: 'No se pudo determinar tu ubicación con precisión. Establece el punto manualmente.',
                        });
                }

                stop();
            }, 12000);
        });
    }

    function setInputs(lat, lng, pan = false) {
        const latInput = $('lat');
        const lngInput = $('lng');

        if (latInput) latInput.value = Number(lat).toFixed(6);
        if (lngInput) lngInput.value = Number(lng).toFixed(6);

        if (marker) {
            marker.setPosition(
                {
                    lat: Number(lat),
                    lng: Number(lng),
                });
        }

        if (pan && map) {
            map.setCenter(
                {
                    lat: Number(lat),
                    lng: Number(lng),
                });
        }
    }

    function initGoogleMap() {
        const mapDiv = document.getElementById(mapDivId);
        if (!mapDiv) return;

        const startLat = parseFloat(($('lat')?.value) || '19.4326');
        const startLng = parseFloat(($('lng')?.value) || '-99.1332');

        map = new google.maps.Map(mapDiv,
            {
                center:
                {
                    lat: startLat,
                    lng: startLng,
                },
                zoom: 13,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
            });

        marker = new google.maps.Marker(
            {
                position:
                {
                    lat: startLat,
                    lng: startLng,
                },
                map,
                draggable: true,
            });

        map.addListener('click', (e) => {
            setInputs(e.latLng.lat(), e.latLng.lng(), true);
        });

        marker.addEventListener('dragend', () => {
            const pos = marker.getPosition();
            setInputs(pos.lat(), pos.lng(), false);
        });

        const ro = new ResizeObserver(() => {
            google.maps.event.trigger(map, 'resize');
        });

        ro.observe(mapDiv);
    }

    window.__initParkingMap__ = function () {
        initUiBindings();
        initScheduleUi();
        initGoogleMap();
    };

    const runWhenDom = () => {
        if (window.google && window.google.maps) {
            window.__initParkingMap__();
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', runWhenDom, { once: true });
    } else {
        runWhenDom();
    }

    document.addEventListener('livewire:navigated', runWhenDom);
}