@props(['title' => 'Seleccionar plan'])

<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} · Parking+</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 dark:bg-zinc-900 text-gray-800 dark:text-gray-100 flex flex-col">
    <header class="bg-white dark:bg-zinc-800 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-custom-blue">Parking+</a>

            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:button type="submit" variant="ghost" class="text-sm">Cerrar sesión</flux:button>
                </form>
            @endauth
        </div>
    </header>

    <main class="flex-1">
        {{ $slot }}
    </main>

    <footer class="bg-white dark:bg-zinc-800 text-center py-6 mt-auto border-t border-gray-200 dark:border-zinc-700">
        <p class="text-gray-600 dark:text-gray-400">
            © {{ date('Y') }} Parking+. Todos los derechos reservados.
        </p>
    </footer>
</body>
</html>
