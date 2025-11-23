{{--
* Nombre de la vista           : dashboard.blade.php
* Descripción de la vista      : Panel de estadísticas del estacionamiento del usuario.
* Fecha de creación            : 04/11/2025
* Elaboró                      : Elian Pérez
* Fecha de liberación          : 05/11/2025
* Autorizó                     : Angel Davila
* Version                      : 1.0
* Fecha de mantenimiento       : 
* Folio de mantenimiento       : 
* Tipo de mantenimiento        :
* Descripción del mantenimiento: 
* Responsable                  : 
* Revisor                      : 
--}}
<x-layouts.app :title="__('Estadísticas del Estacionamiento')">

    @if (! $hasParking)
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5 mb-6">
            <h3 class="font-semibold text-black dark:text-white">
                Configura tu estacionamiento
            </h3>

            <p class="text-sm text-black/70 dark:text-white/70 mt-1">
                Aún no has registrado los datos de tu estacionamiento. Para ver estadísticas y comenzar a operar,
                primero crea tu estacionamiento.
            </p>

            <div class="mt-3">
                <flux:button icon="cog-6-tooth" :href="route('parking.create')" variant="filled">
                    Configuración
                </flux:button>
            </div>
        </div>
    @elseif ($readersCount === 0)
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5 mb-6">
            <h3 class="font-semibold text-black dark:text-white">
                Agrega tus lectores QR
            </h3>

            <p class="text-sm text-black/70 dark:text-white/70 mt-1">
                Ya tienes un estacionamiento configurado, pero no has creado lectores QR. Crea al menos un lector
                para registrar entradas/salidas y habilitar tus estadísticas.
            </p>

            <div class="mt-3 flex flex-wrap gap-2">
                <flux:button icon="plus" icon-variant="outline" :href="route('parking.qr-readers.create')" variant="primary" class="bg-blue-600 hover:bg-blue-700 text-white text-sm">
                    Crear lector
                </flux:button>

                <flux:button icon="eye" icon-variant="outline" :href="route('parking.qr-readers.index')" variant="filled" class="text-sm text-black dark:text-white">
                    Ver mis lectores
                </flux:button>
            </div>
        </div>
    @endif

    <div class="max-w-6xl mx-auto p-6 space-y-8">

         <form method="GET" class="flex items-center">
            <flux:field variant="inline" class="items-center gap-3">
                <flux:label
                    for="range"
                    class="text-sm text-black/70 dark:text-white/70"
                >
                    Rango:
                </flux:label>

                <flux:select
                    id="range"
                    name="range"
                    class="text-sm w-48"
                    onchange="this.form.submit()"
                >
                    <option value="day"   {{ $range === 'day' ? 'selected' : '' }}>Día (hoy)</option>
                    <option value="week"  {{ $range === 'week' ? 'selected' : '' }}>Semana actual</option>
                    <option value="month" {{ $range === 'month' ? 'selected' : '' }}>Mes actual</option>
                </flux:select>
            </flux:field>
        </form>

        <div class="grid md:grid-cols-3 gap-4">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900">
                <flux:heading size="sm" class="mb-2 text-black dark:text-white">
                        Ingresos
                </flux:heading>

                <div class="text-3xl font-bold mt-1 text-custom-green">
                    ${{ number_format($kpis['revenue'], 2, '.', '.') }}
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900">
                <flux:heading size="sm" class="mb-2 text-black dark:text-white">
                        Usuarios normales
                </flux:heading>

                <div class="text-3xl font-bold mt-1 text-custom-blue">
                    {{ $kpis['users_normal'] }}
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900">
                <flux:heading size="sm" class="mb-2 text-black dark:text-white">
                        Usuarios dinámicos
                </flux:heading>

                <div class="text-3xl font-bold mt-1 text-custom-orange">
                    {{ $kpis['users_dynamic'] }}
                </div>
            </div>
        </div>

        {{-- Gráficas --}}
        <div class="grid md:grid-cols-3 gap-4">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900">
                <flux:heading size="sm" class="mb-2 text-black dark:text-white">
                        Ingresos
                </flux:heading>

                <div style="height:220px">
                    <canvas id="chartRevenue"></canvas>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900">
                <flux:heading size="sm" class="mb-2 text-black dark:text-white">
                        Usuarios normales
                </flux:heading>

                <div style="height:220px">
                    <canvas id="chartUsersNormal"></canvas>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900">
                <flux:heading size="sm" class="mb-2 text-black dark:text-white">
                        Usuarios dinámicos
                </flux:heading>

                <div style="height:220px">
                    <canvas id="chartUsersDyn"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function renderDashboardCharts() 
            {
                const dataRevenue     = @json($revenue);
                const dataUsersNormal = @json($usersNormal);
                const dataUsersDyn    = @json($usersDynamic);

                const L = (arr) => arr.map(i => i.label);
                const V = (arr) => arr.map(i => Number(i.total || 0));

                const baseOptions = 
                {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: 
                    {
                        legend: 
                        { 
                            display: false 
                        },
                        tooltip: 
                        { 
                            enabled: true 
                        }
                    },
                    scales: 
                    {
                        x: 
                        {
                            ticks: 
                            {
                                maxRotation: 0,
                                autoSkip: true
                            }
                        },
                        y: 
                        {
                            beginAtZero: true,
                            ticks: 
                            { 
                                precision: 0 
                            }
                        }
                    }
                };

                const barDataset = (values) => (
                {
                    data: values,
                    maxBarThickness: 28,
                    barPercentage: 0.9,
                    categoryPercentage: 0.9,
                    backgroundColor: '#241178'
                });

                if (window.dashboardCharts) 
                {
                    window.dashboardCharts.forEach(c => c.destroy());
                }
                window.dashboardCharts = [];

                const revenueCtx = document.getElementById('chartRevenue');
                const usersNormCtx = document.getElementById('chartUsersNormal');
                const usersDynCtx = document.getElementById('chartUsersDyn');

                if (revenueCtx) 
                {
                    window.dashboardCharts.push(new Chart(revenueCtx, 
                    {
                        type: 'bar',
                        data: 
                        {
                            labels: L(dataRevenue),
                            datasets: [
                            {
                                ...barDataset(V(dataRevenue)),
                                backgroundColor: '#42A958'
                            }]
                        },
                        options: baseOptions
                    }));
                }

                if (usersNormCtx) 
                {
                    window.dashboardCharts.push(new Chart(usersNormCtx, 
                    {
                        type: 'bar',
                        data: 
                        {
                            labels: L(dataUsersNormal),
                            datasets: [
                            {
                                ...barDataset(V(dataUsersNormal)),
                                backgroundColor: '#241178'
                            }]
                        },
                        options: baseOptions
                    }));
                }

                if (usersDynCtx) 
                {
                    window.dashboardCharts.push(new Chart(usersDynCtx, 
                    {
                        type: 'bar',
                        data: 
                        {
                            labels: L(dataUsersDyn),
                            datasets: [
                            {
                                ...barDataset(V(dataUsersDyn)),
                                backgroundColor: '#DE6601'
                            }]
                        },
                        options: baseOptions
                    }));
                }
            }

            document.addEventListener('DOMContentLoaded', renderDashboardCharts);

            document.addEventListener('livewire:navigated', renderDashboardCharts);
        </script>
    @endpush
</x-layouts.app>
