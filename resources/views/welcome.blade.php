<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 dark:bg-zinc-900 text-gray-800 dark:text-gray-100 flex flex-col min-h-screen">
    <header class="bg-white dark:bg-zinc-800 shadow-md py-4">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-custom-blue">Parking+</h1>
            <nav class="space-x-4">
                <flux:button 
                    variant="filled" 
                    :href="route('login')" 
                    class="px-4 py-2 text-custom-blue hover:underline font-semibold">
                    Iniciar sesión
                </flux:button>
                
                <flux:button 
                    variant="primary" 
                    :href="route('register')"
                    class="px-4 py-2 bg-custom-blue text-white rounded-lg hover:bg-custom-blue-dark">
                    Registrarse
                </flux:button>
            </nav>
        </div>
    </header>

    {{-- Contenido principal --}}
    <main class="flex-grow container mx-auto px-6 py-16 text-center">
        <h2 class="text-4xl font-extrabold mb-4">Bienvenido a Parking+</h2>
        <p class="text-lg text-gray-600 dark:text-gray-400 mb-12">
            La plataforma inteligente para gestionar estacionamientos, usuarios y accesos mediante códigos QR.
        </p>

        <h3 class="text-2xl font-semibold mb-8">Elige el plan que mejor se adapte a ti</h3>

        {{-- Tarjetas de planes dinámicas --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            @forelse ($plans as $plan)
                @php
                    $isFeatured = str_contains(Str::lower($plan->name), 'pro') || $loop->index === 1;
                    $periodLabel = $plan->duration_days === 30 ? 'mes' : $plan->duration_days . ' días';
                @endphp

                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-md p-6 flex flex-col {{ $isFeatured ? 'border-2 border-custom-blue' : '' }}">
                    {{-- Nombre --}}
                    <h4 class="text-xl font-bold mb-2 text-custom-blue">{{ $plan->name }}</h4>

                    {{-- Descripción --}}
                    <p class="text-gray-600 dark:text-gray-400 flex-grow">
                        {{ $plan->description ?: 'Plan de estacionamiento.' }}
                    </p>

                    {{-- Precio/Periodo --}}
                    <p class="mt-4 text-3xl font-bold text-custom-blue">
                        ${{ number_format($plan->price, 2) }}
                        <span class="text-base text-gray-500">/{{ $periodLabel }}</span>
                    </p>
                </div>

            @empty
                {{-- Placeholder si no hay planes --}}
                <div class="col-span-1 md:col-span-3">
                    <div class="text-center text-zinc-500 dark:text-zinc-400 py-8">
                        Próximamente planes de estacionamiento disponibles.
                    </div>
                </div>
            @endforelse

        </div>

    </main>

    {{-- Pie de página --}}
    <footer class="bg-white dark:bg-zinc-800 text-center py-6 mt-auto border-t border-gray-200 dark:border-zinc-700">
        <p class="text-gray-600 dark:text-gray-400">
            © {{ date('Y') }} Parking+. Todos los derechos reservados.
        </p>
    </footer>
</body>
</html>