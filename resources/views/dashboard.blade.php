<x-layouts.app :title="__('Estadísticas del Estacionamiento')">
    {{-- Encima de los KPIs --}}
    @if (!$hasParking)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
            <h3 class="font-semibold text-amber-800">Configura tu estacionamiento</h3>
            <p class="text-sm text-amber-800/90 mt-1">
                Aún no has registrado los datos de tu estacionamiento. Para ver estadísticas y comenzar a operar,
                primero crea tu estacionamiento.
            </p>
            <div class="mt-3">
                <a href="{{ route('parking.create') }}"
                    class="inline-flex items-center rounded-lg bg-amber-600 text-white px-3 py-2 text-sm">
                    Configurar estacionamiento
                </a>
            </div>
        </div>
    @elseif($readersCount === 0)
        <div class="rounded-xl border border-sky-200 bg-sky-50 p-5">
            <h3 class="font-semibold text-sky-800">Agrega tus lectores QR</h3>
            <p class="text-sm text-sky-800/90 mt-1">
                Ya tienes un estacionamiento configurado, pero no has creado lectores QR. Crea al menos un lector
                para registrar entradas/salidas y habilitar tus estadísticas.
            </p>
            <div class="mt-3 flex gap-2">
                <a href="{{ route('parking.qr-readers.create') }}"
                    class="inline-flex items-center rounded-lg bg-sky-600 text-white px-3 py-2 text-sm">
                    Crear lector
                </a>
                <a href="{{ route('parking.qr-readers.index') }}"
                    class="inline-flex items-center rounded-lg border px-3 py-2 text-sm">
                    Ver mis lectores
                </a>
            </div>
        </div>
    @endif

    <div class="max-w-6xl mx-auto p-6 space-y-8">

        {{-- Filtro compacto (se conserva al enviar) --}}
        <form method="GET" class="flex items-center gap-3">
            <label class="text-sm text-zinc-600 dark:text-zinc-300">Rango:</label>
            <select name="range" class="border rounded-lg px-3 py-2 bg-white dark:bg-zinc-900"
                onchange="this.form.submit()">
                <option value="day" {{ $range === 'day' ? 'selected' : '' }}>Día (hoy)</option>
                <option value="week" {{ $range === 'week' ? 'selected' : '' }}>Semana actual</option>
                <option value="month" {{ $range === 'month' ? 'selected' : '' }}>Mes actual</option>
            </select>
        </form>

        {{-- KPIs --}}
        <div class="grid md:grid-cols-3 gap-4">
            <div class="rounded-xl border p-4 bg-white dark:bg-zinc-900">
                <div class="text-xs text-zinc-500">Ingresos ({{ strtoupper($range) }})</div>
                <div class="text-3xl font-bold mt-1">${{ number_format($kpis['revenue'], 0, ',', '.') }}</div>
            </div>
            <div class="rounded-xl border p-4 bg-white dark:bg-zinc-900">
                <div class="text-xs text-zinc-500">Usuarios normales (rol 3)</div>
                <div class="text-3xl font-bold mt-1">{{ $kpis['users_normal'] }}</div>
            </div>
            <div class="rounded-xl border p-4 bg-white dark:bg-zinc-900">
                <div class="text-xs text-zinc-500">Usuarios dinámicos</div>
                <div class="text-3xl font-bold mt-1">{{ $kpis['users_dynamic'] }}</div>
            </div>
        </div>

        {{-- Gráficas Fijas (barras simples, altura fija, sin animaciones) --}}
        <div class="grid md:grid-cols-3 gap-4">
            <div class="rounded-xl border p-4 bg-white dark:bg-zinc-900">
                <h3 class="text-sm font-semibold mb-2">Ingresos</h3>
                <div style="height:220px">
                    <canvas id="chartRevenue"></canvas>
                </div>
            </div>
            <div class="rounded-xl border p-4 bg-white dark:bg-zinc-900">
                <h3 class="text-sm font-semibold mb-2">Usuarios normales (rol 3)</h3>
                <div style="height:220px">
                    <canvas id="chartUsersNormal"></canvas>
                </div>
            </div>
            <div class="rounded-xl border p-4 bg-white dark:bg-zinc-900">
                <h3 class="text-sm font-semibold mb-2">Usuarios dinámicos</h3>
                <div style="height:220px">
                    <canvas id="chartUsersDyn"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Datos (ya vienen agregados por bucket)
        const dataRevenue = @json($revenue);
        const dataUsersNormal = @json($usersNormal);
        const dataUsersDyn = @json($usersDynamic);

        // Helpers: etiquetas y valores
        const L = arr => arr.map(i => i.label);
        const V = arr => arr.map(i => Number(i.total || 0));

        // Opciones “fijas”: sin animación, barras acotadas, sin auto-resize raro
        const baseOptions = {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true
                }
            },
            scales: {
                x: {
                    ticks: {
                        maxRotation: 0,
                        autoSkip: true
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        };

        // Estilo de barras compacto
        const barDataset = (values) => ({
            data: values,
            maxBarThickness: 28,
            barPercentage: 0.9,
            categoryPercentage: 0.9,
            backgroundColor: '#60a5fa'
        });

        new Chart(document.getElementById('chartRevenue'), {
            type: 'bar',
            data: {
                labels: L(dataRevenue),
                datasets: [{
                    ...barDataset(V(dataRevenue)),
                    backgroundColor: '#22c55e'
                }]
            },
            options: baseOptions
        });

        new Chart(document.getElementById('chartUsersNormal'), {
            type: 'bar',
            data: {
                labels: L(dataUsersNormal),
                datasets: [barDataset(V(dataUsersNormal))]
            },
            options: baseOptions
        });

        new Chart(document.getElementById('chartUsersDyn'), {
            type: 'bar',
            data: {
                labels: L(dataUsersDyn),
                datasets: [{
                    ...barDataset(V(dataUsersDyn)),
                    backgroundColor: '#f59e0b'
                }]
            },
            options: baseOptions
        });
    </script>
</x-layouts.app>
