<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos y Condiciones | Parking+</title>

    {{-- Si usas Vite/Tailwind --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 dark:bg-zinc-900 text-gray-800 dark:text-gray-100">

    <main class="max-w-5xl mx-auto p-6 space-y-8">

        {{-- Encabezado --}}
        <div class="flex items-center justify-between border-b border-neutral-200 dark:border-neutral-700 pb-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-700 dark:text-blue-300"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 6v6l4 2m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Términos y Condiciones</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        Última actualización: {{ now()->format('d/m/Y') }}
                    </p>
                </div>
            </div>

            <a href="/"
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-neutral-300 dark:border-neutral-700
                    px-3 py-1.5 text-sm font-medium text-zinc-800 dark:text-zinc-100
                    hover:bg-neutral-50 dark:hover:bg-zinc-800 transition">
                ← Volver al inicio
            </a>
        </div>

        {{-- Sección 1 --}}
        <section
            class="space-y-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-5">
            <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">1. Aceptación de los términos</h2>
            <p class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">
                Al acceder o utilizar la aplicación <strong>Parking+</strong>, el usuario acepta los presentes términos
                y
                condiciones, incluyendo privacidad de datos, administración de estacionamientos, emisión de facturas y
                uso
                de planes de suscripción.
            </p>
        </section>

        {{-- Sección 2 --}}
        <section
            class="space-y-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-5">
            <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">2. Privacidad y manejo de datos</h2>
            <p class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">
                <strong>Parking+</strong> recopila los datos necesarios para operar (nombre, correo, teléfono, ubicación
                de
                estacionamientos). Los datos se almacenan de forma segura y se manejan conforme a la normatividad
                aplicable.
            </p>
            <p class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">
                No se venderán ni transferirán a terceros sin consentimiento, salvo requerimiento legal.
            </p>
        </section>

        {{-- Sección 3 --}}
        <section
            class="space-y-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-5">
            <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">3. Planes y suscripciones</h2>
            <p class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">
                Los administradores pueden contratar planes con diferentes niveles de funciones (lectores QR, reportes,
                clientes dinámicos y soporte).
            </p>
            <ul class="list-disc pl-6 text-sm text-zinc-700 dark:text-zinc-300">
                <li>Pagos procesados por plataformas seguras (p. ej., PayPal).</li>
                <li>Las suscripciones no se renuevan automáticamente.</li>
            </ul>
        </section>

        {{-- Sección 4 --}}
        <section
            class="space-y-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-5">
            <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">4. Responsabilidad del usuario</h2>
            <p class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">
                El usuario debe usar responsablemente las herramientas, mantener datos actualizados y evitar usos
                indebidos.
                El administrador del estacionamiento es responsable de los registros bajo su cuenta.
            </p>
        </section>

        {{-- Sección 5 --}}
        <section
            class="space-y-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-5">
            <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">5. Contacto</h2>
            <p class="text-sm text-zinc-700 dark:text-zinc-300">
                Soporte: <a href="mailto:soporte@parkingplus.test"
                    class="text-blue-600 dark:text-blue-400 underline hover:text-blue-700 dark:hover:text-blue-300">
                    admgenineral@gmail.com
                </a>
            </p>
        </section>

        {{-- Pie --}}
        <div
            class="text-center text-xs text-zinc-500 dark:text-zinc-400 pt-6 border-t border-neutral-200 dark:border-neutral-700">
            <p>&copy; {{ now()->year }} Parking+. Todos los derechos reservados.</p>
            <p>El logotipo y nombre "SAT" pertenecen a la SHCP; se incluyen con fines ilustrativos.</p>
        </div>

    </main>

</body>

</html>
