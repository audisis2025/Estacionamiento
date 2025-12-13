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

    @if (! $has_parking)
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5 mb-6">
            <flux:heading level="3" size="md" class="font-semibold text-black dark:text-white">
                    Configura tu estacionamiento
            </flux:heading>

            <flux:text class="text-sm text-black/70 dark:text-white/70 mt-1">
                    Aún no has registrado los datos de tu estacionamiento. Para ver estadísticas y comenzar a operar,
                    primero crea tu estacionamiento.
            </flux:text>

            <div class="mt-3 flex justify-end">
                <flux:button icon="cog-6-tooth" :href="route('parking.create')" variant="filled">
                    Configuración
                </flux:button>
            </div>
        </div>
    @elseif ($readers_count === 0)
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5 mb-6">
            <flux:heading level="3" size="md" class="font-semibold text-black dark:text-white">
                    Agrega tus lectores QR
            </flux:heading>

            <flux:text class="text-sm text-black/70 dark:text-white/70 mt-1">
                    Ya tienes un estacionamiento configurado, pero no has creado lectores QR. Crea al menos un lector
                    para registrar entradas/salidas y habilitar tus estadísticas.
            </flux:text>

            <div class="mt-3 flex flex-wrap gap-2 justify-end">
                <flux:button icon="plus" icon-variant="outline" :href="route('parking.qr-readers.create')" variant="primary" class="bg-green-600 hover:bg-green-700 text-white text-sm">
                    Crear lector
                </flux:button>

                <flux:button icon="eye" icon-variant="outline" :href="route('parking.qr-readers.index')" variant="primary" class="bg-gray-500 hover:bg-gray-600text-sm text-white dark:text-white">
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

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 bg-white dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4 text-black dark:text-white">
                    Ingresos
            </flux:heading>

            <div style="height:300px">
                <canvas id="chartRevenue"></canvas>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 bg-white dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4 text-black dark:text-white">
                    Usuarios normales
            </flux:heading>

            <div style="height:300px">
                <canvas id="chartUsersNormal"></canvas>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 bg-white dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4 text-black dark:text-white">
                    Usuarios dinámicos
            </flux:heading>

            <div style="height:300px">
                <canvas id="chartUsersDyn"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function renderDashboardCharts() 
            {
                const range = @json($range);
                const dataRevenue = @json($revenue);
                const dataUsersNormal = @json($users_normal);
                const dataUsersDyn = @json($users_dynamic);

                function generateLabels(range, data) 
                {
                    const labels = [];
                    
                    if (range === 'day') 
                    {
                        for (let i = 0; i < 24; i++) 
                        {
                            labels.push(`${i}:00`);
                        }
                    } else if (range === 'week') 
                    {
                        const days = [
                            'Lun', 
                            'Mar', 
                            'Mié', 
                            'Jue', 
                            'Vie', 
                            'Sáb', 
                            'Dom'
                        ];
                        return days;
                    } else if (range === 'month') 
                    {
                        const today = new Date();
                        const daysInMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate();
                        for (let i = 1; i <= daysInMonth; i++) 
                        {
                            labels.push(i.toString());
                        }
                    }
                    
                    return labels;
                }

                function mapDataToLabels(range, data, allLabels) 
                {
                    const values = new Array(allLabels.length).fill(0);
                    
                    data.forEach(item => 
                    {
                        let index = -1;
                        
                        if (range === 'day') 
                        {
                            index = parseInt(item.label);
                        } else if (range === 'week') 
                        {
                            const dateParts = item.label.split('-');
                            const date = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
                            const dayOfWeek = date.getDay();
                            index = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
                        } else if (range === 'month') 
                        {
                            const dateParts = item.label.split('-');
                            const day = parseInt(dateParts[2], 10);
                            index = day - 1;
                        }
                        
                        if (index >= 0 && index < values.length) 
                        {
                            values[index] = Number(item.total || 0);
                        }
                    });
                    
                    return values;
                }

                const allLabels = generateLabels(range, dataRevenue);
                const revenueValues = mapDataToLabels(range, dataRevenue, allLabels);
                const usersNormalValues = mapDataToLabels(range, dataUsersNormal, allLabels);
                const usersDynValues = mapDataToLabels(range, dataUsersDyn, allLabels);

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
                                maxRotation: 45,
                                minRotation: 0,
                                autoSkip: range === 'month',
                                maxTicksLimit: range === 'month' ? 15 : undefined
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

                const barDataset = (values, color) => (
                {
                    data: values,
                    maxBarThickness: 28,
                    barPercentage: 0.9,
                    categoryPercentage: 0.9,
                    backgroundColor: color
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
                            labels: allLabels,
                            datasets: [barDataset(revenueValues, '#42A958')]
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
                            labels: allLabels,
                            datasets: [barDataset(usersNormalValues, '#241178')]
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
                            labels: allLabels,
                            datasets: [barDataset(usersDynValues, '#DE6601')]
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