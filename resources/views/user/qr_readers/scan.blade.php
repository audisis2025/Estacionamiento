<x-layouts.app :title="__('Escanear — Lector #' . $reader->id)">
    <div class="p-6 max-w-2xl mx-auto space-y-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold">
                    Escanear — Lector #{{ $reader->id }}
                </h2>
                <p class="text-sm text-zinc-500 mt-1">
                    Tipo: <span
                        class="font-medium">{{ ['Entrada', 'Salida', 'Mixto'][$reader->sense] ?? $reader->sense }}</span>
                </p>
            </div>

            <!-- Estado -->
            <div class="inline-flex items-center gap-2 rounded-full bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1.5">
                <span class="relative flex h-2.5 w-2.5">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
                <span class="text-sm font-medium text-emerald-700 dark:text-emerald-300">Listo para escanear</span>
            </div>
        </div>

        <!-- Indicaciones -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <p class="text-sm text-zinc-700 dark:text-zinc-300">
                Coloca el cursor en "Capturar" y escanea el código QR desde el lector USB.
            </p>
        </div>

        <!-- Acciones rápidas -->
        <form id="scan-form" class="flex items-center gap-3" onsubmit="return false;">
            @csrf
            <input type="hidden" id="csrf-token" value="{{ csrf_token() }}">
            <input id="scan-input" type="text" autocomplete="off" class="sr-only" />

            <flux:button id="focus-btn" type="button" variant="primary" icon="qr-code">
                Capturar
            </flux:button>

            <flux:link :href="route('parking.qr-readers.index')" icon="arrow-left">
                Volver a lectores
            </flux:link>
        </form>

        <!-- Logs de actividad (manteniendo tu funcionalidad) -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
            <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-3">Log de actividad</h3>
            <pre id="result" class="text-xs bg-zinc-900 text-zinc-100 p-3 rounded min-h-[120px] overflow-x-auto">{}</pre>
        </div>
    </div>

    @push('js')
        <script>
            (function() {
                // Evitar re-inicializar si ya está montado
                if (window.__qrScannerInit) return;

                function initScannerOnce() {
                    if (window.__qrScannerBound) return; // ya está activo
                    window.__qrScannerBound = true;

                    const input = document.getElementById('scan-input');
                    const focusB = document.getElementById('focus-btn');
                    const result = document.getElementById('result');
                    const csrfTok = document.getElementById('csrf-token')?.value || '';

                    if (!input || !focusB) {
                        window.__qrScannerBound = false;
                        return;
                    }

                    // --- Lógica original (resumida) ---
                    let busy = false,
                        lastText = '',
                        lastWhen = 0,
                        buf = '',
                        last = 0,
                        timer = null;
                    const DUP_WINDOW_MS = 2500,
                        BUSY_COOLDOWN = 2500;

                    function sanitize(s) {
                        return (s || '').replace(/\r?\n/g, '').replace(/\r/g, '').replace(/[\u201C\u201D]/g, '"').trim();
                    }

                    function sameAsLastNow(txt) {
                        const now = Date.now();
                        return txt === lastText && (now - lastWhen) < DUP_WINDOW_MS;
                    }

                    function remember(txt) {
                        lastText = txt;
                        lastWhen = Date.now();
                    }

                    async function submitScan(text) {
                        const clean = sanitize(text);
                        if (!clean || sameAsLastNow(clean) || busy) return;
                        busy = true;
                        remember(clean);
                        result.textContent = 'Procesando...';
                        try {
                            const fd = new FormData();
                            fd.append('qr', clean);
                            const res = await fetch("{{ route('parking.qr-readers.scan.ingest', $reader) }}", {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfTok
                                },
                                body: fd,
                                credentials: 'same-origin',
                            });
                            const json = await res.json().catch(() => ({}));
                            result.textContent = JSON.stringify(json, null, 2);
                            if (res.ok && json.ok && !json.silent) {
                                const evt = json?.data?.event;
                                if (evt === 'entry' || evt === 'exit') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: json.message || (evt === 'entry' ? 'Entrada registrada' :
                                            'Salida registrada'),
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                            }
                            if (!res.ok || (json && json.ok === false)) {
                                Swal.fire({
                                    icon: 'error',
                                    title: (json && json.message) ? json.message : `Error ${res.status}`,
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            }
                        } catch (e) {
                            result.textContent = e?.message || String(e);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error de red'
                            });
                        } finally {
                            setTimeout(() => busy = false, BUSY_COOLDOWN);
                        }
                    }

                    function flush() {
                        if (!buf) return;
                        const payload = buf.trim();
                        buf = '';
                        if (payload.startsWith('{') && payload.endsWith('}')) submitScan(payload);
                    }

                    function onKeydown(e) {
                        const ae = document.activeElement,
                            tag = (ae?.tagName || '').toLowerCase();
                        const typingElsewhere = (tag === 'input' || tag === 'textarea') && ae !== input;
                        if (typingElsewhere) return;
                        const now = Date.now();
                        if (now - last > 150) buf = '';
                        last = now;
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            clearTimeout(timer);
                            flush();
                            return;
                        }
                        if (e.key.length === 1) {
                            buf += e.key;
                            clearTimeout(timer);
                            timer = setTimeout(flush, 220);
                        }
                    }

                    // Bind únicos
                    if (!window.__qrKeydown) {
                        window.__qrKeydown = onKeydown;
                        document.addEventListener('keydown', window.__qrKeydown);
                    }

                    input.addEventListener('input', (e) => {
                        const v = (e.target.value || '');
                        if (/\r?\n$/.test(v) || /\r$/.test(v)) {
                            submitScan(v);
                            input.value = '';
                        }
                    });

                    // Foco y estilo del input oculto
                    input.style.position = 'fixed';
                    input.style.left = '-9999px';
                    input.style.top = '0';
                    input.setAttribute('tabindex', '-1');
                    setTimeout(() => input.focus(), 50);
                    focusB.addEventListener('click', () => setTimeout(() => input.focus(), 10));
                    document.addEventListener('visibilitychange', () => {
                        if (!document.hidden) setTimeout(() => input.focus(), 10);
                    });
                }

                // Re-bind en cada navegación SPA de Livewire
                function mountScanner() {
                    // Marcar que ya instalamos el manejador global de navegación
                    if (!window.__qrScannerInit) {
                        window.__qrScannerInit = true;
                        document.addEventListener('livewire:navigated', () => {
                            // Al llegar a la vista de scan, el DOM ya existe: intenta montar
                            setTimeout(initScannerOnce, 0);
                        });
                    }
                    // Primer montaje (carga directa o recarga)
                    setTimeout(initScannerOnce, 0);
                }

                // Cubrir ambos flujos: carga completa y navegación SPA
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', mountScanner);
                } else {
                    mountScanner();
                }
            })();
        </script>
    @endpush

</x-layouts.app>
