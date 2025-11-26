{{--
* Nombre de la vista           : terms.blade.php
* Descripción de la vista      : Página de términos y condiciones de la aplicación Parking+.
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
        <title>Términos y Condiciones</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans bg-zinc-50 dark:bg-zinc-900 text-black dark:text-white min-h-screen">

        <main class="max-w-5xl mx-auto p-6 space-y-8">

            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-700 pb-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-neutral-200 dark:bg-neutral-700 flex items-center justify-center">
                        <x-heroicon-o-clock class="h-6 w-6 text-custom-blue" />
                    </div>

                    <div>
                        <flux:heading level="1" size="xl" class="text-2xl font-bold">
                            Términos y Condiciones
                        </flux:heading>

                        <flux:text class="text-sm text-black/60 dark:text-white/60">
                            Última actualización: {{ now()->format('d/m/Y') }}
                        </flux:text>
                    </div>
                </div>
            </div>

            <section class="space-y-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5">
                <flux:heading level="2" size="lg" class="text-xl font-semibold">
                    1. Aceptación de los términos
                </flux:heading>

                <flux:text class="text-sm leading-relaxed text-black/80 dark:text-white/80">
                    Al acceder o utilizar la aplicación <strong>Parking+</strong>, el usuario acepta los presentes
                    términos y condiciones, incluyendo privacidad de datos, administración de estacionamientos, emisión
                    de facturas y uso de planes de suscripción.
                </flux:text>
            </section>

            <section class="space-y-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5">
                <flux:heading level="2" size="lg" class="text-xl font-semibold">
                    2. Privacidad y manejo de datos
                </flux:heading>

                <flux:text class="text-sm leading-relaxed text-black/80 dark:text-white/80">
                    <strong>Parking+</strong> recopila los datos necesarios para operar (nombre, correo, teléfono,
                    ubicación de estacionamientos). Los datos se almacenan de forma segura y se manejan conforme a la
                    normatividad aplicable.
                </flux:text>

                <flux:text class="text-sm leading-relaxed text-black/80 dark:text-white/80">
                    No se venderán ni transferirán a terceros sin consentimiento, salvo requerimiento legal.
                </flux:text>
            </section>

            <section class="space-y-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5">
                <flux:heading level="2" size="lg" class="text-xl font-semibold">
                    3. Planes y suscripciones
                </flux:heading>

                <flux:text class="text-sm leading-relaxed text-black/80 dark:text-white/80">
                    Los administradores pueden contratar planes con diferentes niveles de funciones (lectores QR,
                    reportes, clientes dinámicos y soporte).
                </flux:text>

                <ul class="list-disc pl-6 text-sm text-black/80 dark:text-white/80">
                    <li>Pagos procesados por plataformas seguras (p. ej., PayPal).</li>
                    <li>Las suscripciones no se renuevan automáticamente.</li>
                </ul>
            </section>

            <section class="space-y-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5">
                <flux:heading level="2" size="lg" class="text-xl font-semibold">
                    4. Responsabilidad del usuario
                </flux:heading>

                <flux:text class="text-sm leading-relaxed text-black/80 dark:text-white/80">
                    El usuario debe usar responsablemente las herramientas, mantener datos actualizados y evitar usos
                    indebidos. El administrador del estacionamiento es responsable de los registros bajo su cuenta.
                </flux:text>
            </section>

            <section class="space-y-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5">
                <flux:heading level="2" size="lg" class="text-xl font-semibold">
                    5. Contacto
                </flux:heading>

                <flux:text class="text-sm text-black/80 dark:text-white/80">
                    Soporte:
                    <flux:link
                        href="mailto:admgenineral@gmail.com"
                        class="text-custom-blue hover:text-custom-blue-dark underline underline-offset-4"
                    >
                        admgenineral@gmail.com
                    </flux:link>
                </flux:text>
            </section>

            <div class="text-center text-xs text-black/60 dark:text-white/60 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                <flux:text class="text-xs text-black/60 dark:text-white/60">
                    &copy; {{ now()->year }} Parking+. Todos los derechos reservados.
                </flux:text>

                <flux:text class="text-xs text-black/60 dark:text-white/60">
                    El logotipo y nombre "SAT" pertenecen a la SHCP; se incluyen con fines ilustrativos.
                </flux:text>
            </div>

        </main>

    </body>

</html>