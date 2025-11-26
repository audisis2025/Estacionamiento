{{--
* Nombre de la vista           : plans.blade.php
* Descripción de la vista      : Layout principal para las páginas relacionadas con la selección de planes de suscripción.
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
@props(['title' => 'Seleccionar plan'])

<!DOCTYPE html>
<html lang="es" class="h-full">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title }} · Parking+</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @fluxAppearance
    </head>

    <body class="font-sans min-h-screen bg-zinc-50 dark:bg-zinc-900 text-black dark:text-white flex flex-col">
        <header class="bg-white dark:bg-zinc-800 shadow-sm border-b border-zinc-200 dark:border-zinc-700">
            <div class="container mx-auto px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex aspect-square size-12 items-center justify-center rounded-md text-accent-foreground">
                        <x-app-logo-icon class="size-9 fill-current text-white" />
                    </div>
                    <flux:heading level="1" size="xl" class="text-2xl !font-bold text-custom-blue">
                        Parking+
                    </flux:heading>
                </div>

                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <flux:button type="submit" variant="primary" icon="arrow-right-start-on-rectangle" icon-variant="outline"
                            class="bg-custom-blue hover:bg-custom-blue-dark text-white">
                            Cerrar sesión
                        </flux:button>
                    </form>
                @endauth

            </div>
        </header>

        <main class="flex-1">
            {{ $slot }}
        </main>

        <footer class="bg-white dark:bg-zinc-800 text-center py-6 mt-auto border-t border-zinc-200 dark:border-zinc-700">
            <flux:text class="text-sm text-black/70 dark:text-white/70">
                © {{ date('Y') }} Parking+. Todos los derechos reservados.
            </flux:text>
        </footer>
        @livewireScripts
        @fluxScripts
    </body>

</html>
