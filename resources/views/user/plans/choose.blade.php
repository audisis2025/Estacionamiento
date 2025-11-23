{{--
* Nombre de la vista           : choose.blade.php
* Descripción de la vista      : Página para que el usuario elija un plan de suscripción.
* Fecha de creación            : 03/11/2025
* Elaboró                      : Elian Pérez
* Fecha de liberación          : 04/11/2025
* Autorizó                     : Angel Davila
* Version                      : 1.0
* Fecha de mantenimiento       : 
* Folio de mantenimiento       : 
* Tipo de mantenimiento        :
* Descripción del mantenimiento: 
* Responsable                  : 
* Revisor                      : 
--}}
<x-layouts.plans :title="__('Elige tu plan')">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="container mx-auto px-6 py-10 text-black dark:text-white">
        <h1 class="text-3xl font-extrabold mb-6 text-center">
            Elige tu plan
        </h1>

        <div id="plans-grid" class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach ($plans as $plan)
                <label
                    class="plan-card bg-white dark:bg-zinc-800 rounded-xl shadow-md p-6 flex flex-col cursor-pointer 
                           border-2 border-transparent transition-all duration-150 ease-out hover:border-zinc-200 dark:hover:border-zinc-700"
                    data-plan-id="{{ $plan->id }}"
                >
                    <input type="radio" name="plan_id" value="{{ $plan->id }}" class="sr-only">

                    <h2 class="text-xl font-bold text-custom-blue mb-2">
                        {{ $plan->name }}
                    </h2>

                    <p class="text-sm text-black/70 dark:text-white/70 flex-grow">
                        {{ $plan->description }}
                    </p>

                    <div class="mt-4 text-3xl font-bold text-custom-blue">
                        ${{ number_format($plan->price, 2) }}
                        <span class="text-base text-black/60 dark:text-white/60">
                            / {{ $plan->duration_days }} días
                        </span>
                    </div>
                </label>
            @endforeach
        </div>

        <div class="mt-6 max-w-lg mx-auto">
            <div id="paypal-button-container" class="flex justify-center"></div>
        </div>
        
        <div class="mt-8 max-w-lg mx-auto">
            <div class="flex items-start gap-3">
                <flux:checkbox id="terms" name="terms" class="mt-0.5" />
                
                <label for="terms" class="text-sm text-black/70 dark:text-white/70 cursor-pointer">
                    He leído y acepto los 
                    <a href="{{ route('terms') }}" target="_blank" rel="noopener noreferrer" class="text-custom-blue hover:text-custom-blue-dark underline underline-offset-4" onclick="setTimeout(() => window.open(this.href, '_blank'), 100)">
                        Términos y Condiciones
                    </a>
                </label>
            </div>
        </div>
    </div>

    <script
        src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.client_id') }}&currency={{ config('paypal.currency', 'MXN') }}&intent=capture">
    </script>

    @vite(['resources/js/plans-checkout.js'])

    <script>
        document.addEventListener('DOMContentLoaded', function()
        {
            window.initPlansCheckout(
            {
                csrfToken: '{{ csrf_token() }}',
                createOrderUrl: '{{ route('paypal.order.create') }}',
                captureOrderUrl: '{{ url('/paypal/order') }}'
            });
        });
    </script>

</x-layouts.plans>
