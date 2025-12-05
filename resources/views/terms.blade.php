{{--
* Nombre de la vista           : terms.blade.php
* Descripción de la vista      : Página de términos y condiciones de la aplicación Parking+.
* Fecha de creación            : 03/11/2025
* Elaboró                      : Elian Pérez
* Fecha de liberación          : 03/11/2025
* Autorizó                     : Angel Davila
* Version                      : 1.0
* Fecha de mantenimiento       : 27/11/2025
* Folio de mantenimiento       : 
* Tipo de mantenimiento        :
* Descripción del mantenimiento: Se actualizaron los terminos y condiciones
* Responsable                  : Elian Pérez
* Revisor                      : Angel Davila
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
                        <x-heroicon-o-document-text class="h-6 w-6 text-custom-blue" />
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
                    Al acceder o utilizar la aplicación <strong>Parking+</strong> (web o móvil), el usuario acepta estos
                    términos y condiciones, así como las políticas de privacidad, facturación y uso de los servicios
                    incluidos en la plataforma.
                </flux:text>
            </section>

            <section class="space-y-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5">
                <flux:heading level="2" size="lg" class="text-xl font-semibold">
                    2. Privacidad y manejo de datos
                </flux:heading>

                <flux:text class="text-sm leading-relaxed text-black/80 dark:text-white/80">
                    <strong>Parking+</strong> recopila datos necesarios para operar correctamente, tales como nombre,
                    correo electrónico, número de teléfono, información de estacionamientos y ubicación del dispositivo.
                </flux:text>

                <flux:text class="text-sm leading-relaxed text-black/80 dark:text-white/80">
                    La ubicación obtenida desde la app móvil o desde la versión web se utiliza únicamente para funciones
                    internas de la aplicación (por ejemplo: validar cercanía, navegación o funciones vinculadas al
                    estacionamiento) y no es compartida con terceros ni con otras plataformas externas.
                </flux:text>

                <flux:text class="text-sm leading-relaxed text-black/80 dark:text-white/80">
                    Los datos se almacenan de forma segura y no serán transferidos sin consentimiento, salvo obligación legal.
                </flux:text>
            </section>

            <section class="space-y-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5">
                <flux:heading level="2" size="lg" class="text-xl font-semibold">
                    3. Planes y suscripciones
                </flux:heading>

                <flux:text class="text-sm leading-relaxed text-black/80 dark:text-white/80">
                    Parking+ ofrece distintos planes tanto para estacionamientos como para usuarios finales y cada plan
                    incluye diferentes funciones.
                </flux:text>

                <flux:heading level="3" size="md" class="text-sm font-semibold mt-3">
                    Duración y características
                </flux:heading>

                <ul class="list-disc pl-6 text-sm text-black/80 dark:text-white/80">
                    <li>Todos los planes de pago tienen una duración fija de 30 días.</li>
                    <li>El plan gratuito para usuarios de la app móvill tiene acceso limitado a funciones.</li>
                    <li>Los planes no se renuevan automáticamente.</li>
                    <li>No existen reembolsos una vez realizada la compra del plan.</li>
                </ul>

                <flux:heading level="3" size="md" class="text-sm font-semibold mt-3">
                    Pagos
                </flux:heading>

                <flux:text class="text-sm leading-relaxed text-black/80 dark:text-white/80">
                    Los pagos son procesados mediante plataformas seguras como PayPal. La confirmación
                    del pago activa el plan seleccionado de manera inmediata.
                </flux:text>
            </section>

            <section class="space-y-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5">
                <flux:heading level="2" size="lg" class="text-xl font-semibold">
                    4. Responsabilidad del usuario
                </flux:heading>

                <flux:text class="text-sm leading-relaxed text-black/80 dark:text-white/80">
                    El usuario se compromete a utilizar la plataforma de forma responsable, mantener sus datos
                    actualizados y evitar cualquier uso indebido. Los administradores de estacionamientos son responsables
                    de los registros e información generada bajo su cuenta.
                </flux:text>
            </section>

            <section class="space-y-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5">
                <flux:heading level="2" size="lg" class="text-xl font-semibold">
                    5. Contacto
                </flux:heading>

                <flux:text class="text-sm text-black/80 dark:text-white/80">
                    Para soporte o aclaraciones:
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
            </div>
        </main>


    </body>

</html>