{{--
* Nombre de la vista           : welcome.blade.php
* Descripción de la vista      : Página principal de la web donde se muestran los planes de estacionamiento disponibles.
                                 y los enlaces para iniciar sesión o registrarse.
* Fecha de creación            : 03/11/2025
* Elaboró                      : Elian Pérez
* Fecha de liberación          : 03/11/2025
* Autorizó                     : Angel Davila
* Version                      : 1.0
* Fecha de mantenimiento       : 
* Folio de mantenimiento       : 
* Tipo de mantenimiento        :
* Descripción del mantenimiento: 
* Responsable                  : 
* Revisor                      : 
--}}

<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bienvenido</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans bg-zinc-50 dark:bg-zinc-900 text-black dark:text-white flex flex-col min-h-screen">
        <!-- Encabezado -->
        <header class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 py-4 shadow-sm">
            <div class="container mx-auto px-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-custom-blue">Parking+</h1>

                <nav class="space-x-4">
                    <flux:button icon="user-circle" icon-variant="outline" variant="primary" :href="route('login')"
                        class="px-4 py-2 bg-custom-blue text-white rounded-lg hover:bg-custom-blue-dark">
                        Iniciar sesión
                    </flux:button>

                    <flux:button icon="user-plus" icon-variant="outline" variant="primary" :href="route('register')"
                        class="px-4 py-2 bg-custom-blue text-white rounded-lg hover:bg-custom-blue-dark">
                        Registrarse
                    </flux:button>
                </nav>
            </div>
        </header>

        <!-- Contenido principal -->
        <main class="flex-grow container mx-auto px-6 py-16 text-center">
            <h2 class="text-4xl font-extrabold mb-4">Bienvenido a Parking+</h2>
            <p class="text-lg text-black/70 dark:text-white/70 mb-12">
                La plataforma inteligente para gestionar estacionamientos, usuarios y accesos mediante códigos QR.
            </p>

            <h3 class="text-2xl font-semibold mb-8">Elige el plan que mejor se adapte a ti</h3>

            <!-- Tarjetas de planes -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse ($plans as $plan)
                    @php
                        $isFeatured = str_contains(Str::lower($plan->name), 'pro') || $loop->index === 1;
                        $periodLabel = $plan->duration_days === 30 ? 'mes' : $plan->duration_days . ' días';
                    @endphp

                    <div
                        class="bg-white dark:bg-zinc-800 rounded-xl shadow-md p-6 flex flex-col {{ $isFeatured ? 'border-2 border-custom-blue' : 'border border-zinc-200 dark:border-zinc-700' }}">
                        <h4 class="text-xl font-bold mb-2 text-custom-blue">{{ $plan->name }}</h4>

                        <p class="text-black/70 dark:text-white/70 flex-grow">
                            {{ $plan->description ?: 'Plan de estacionamiento.' }}
                        </p>

                        <p class="mt-4 text-3xl font-bold text-custom-blue">
                            ${{ number_format($plan->price, 2) }}
                            <span class="text-base text-black/60 dark:text-white/60">/{{ $periodLabel }}</span>
                        </p>
                    </div>

                @empty
                    <div class="col-span-1 md:col-span-3">
                        <div class="text-center text-black/60 dark:text-white/60 py-8">
                            Próximamente planes de estacionamiento disponibles.
                        </div>
                    </div>
                @endforelse
            </div>
        </main>

        <!-- Pie de página -->
        <footer class="bg-white dark:bg-zinc-800 border-t border-zinc-200 dark:border-zinc-700 text-center py-6 mt-auto">
            <p class="text-black/70 dark:text-white/70 text-sm">
                © {{ date('Y') }} Parking+. Todos los derechos reservados.
            </p>

            <p class="mt-2">
                <a href="{{ route('terms') }}" target="_blank" rel="noopener noreferrer"
                    class="text-custom-blue hover:text-custom-blue-dark text-sm underline-offset-4 hover:underline">
                    Términos y Condiciones
                </a>
            </p>
        </footer>

    </body>

</html>
