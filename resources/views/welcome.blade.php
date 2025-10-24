<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 dark:bg-zinc-900 text-gray-800 dark:text-gray-100 flex flex-col min-h-screen">
    

    {{-- Encabezado --}}
    <header class="bg-white dark:bg-zinc-800 shadow-md py-4">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-custom-blue">Parking+</h1>
            <nav class="space-x-4">
                <flux:button 
                    variant="ghost" 
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

        {{-- Tarjetas de planes --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Plan Básico --}}
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-md p-6 flex flex-col">
                <h4 class="text-xl font-bold mb-2 text-custom-blue">Plan Básico</h4>
                <p class="text-gray-600 dark:text-gray-400 flex-grow">
                    Ideal para estacionamientos pequeños.  
                    Incluye hasta 2 lectores QR y gestión básica de usuarios.
                </p>
                <p class="mt-4 text-3xl font-bold text-custom-blue">$99 <span class="text-base text-gray-500">/mes</span></p>
                <flux:button 
                    variant="primary" 
                    :href="route('register')"
                    class="mt-6 inline-block bg-custom-blue hover:bg-custom-blue-dark text-white font-semibold py-2 px-4 rounded-lg">
                    Comenzar
                </flux:button>
            </div>

            {{-- Plan Profesional --}}
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-md p-6 flex flex-col border-2 border-custom-blue">
                <h4 class="text-xl font-bold mb-2 text-custom-blue">Plan Profesional</h4>
                <p class="text-gray-600 dark:text-gray-400 flex-grow">
                    Perfecto para medianas empresas.  
                    Hasta 5 lectores QR, estadísticas y administración avanzada.
                </p>
                <p class="mt-4 text-3xl font-bold text-custom-blue">$249 <span class="text-base text-gray-500">/mes</span></p>
                <flux:button 
                    variant="primary" 
                    :href="route('register')"
                    class="mt-6 inline-block bg-custom-blue hover:bg-custom-blue-dark text-white font-semibold py-2 px-4 rounded-lg">
                    Comenzar
                </flux:button>
            </div>

            {{-- Plan Empresarial --}}
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-md p-6 flex flex-col">
                <h4 class="text-xl font-bold mb-2 text-custom-blue">Plan Empresarial</h4>
                <p class="text-gray-600 dark:text-gray-400 flex-grow">
                    Diseñado para corporativos o cadenas de estacionamientos.  
                    Integraciones ilimitadas, soporte prioritario y control multi-sucursal.
                </p>
                <p class="mt-4 text-3xl font-bold text-custom-blue">$499 <span class="text-base text-gray-500">/mes</span></p>
                <flux:button 
                    variant="primary" 
                    :href="route('register')"
                    class="mt-6 inline-block bg-custom-blue hover:bg-custom-blue-dark text-white font-semibold py-2 px-4 rounded-lg">
                    Comenzar
                </flux:button>
            </div>
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