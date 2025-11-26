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

    <div class="container mx-auto px-6 py-10 text-black dark:text-white space-y-8">

        <div class="text-center">
            <flux:heading level="1" size="xl" class="text-3xl !font-black text-black dark:text-white">
                Elige tu plan
            </flux:heading>

            <flux:subheading class="mt-2 text-sm text-black/60 dark:text-white/60">
                Selecciona un plan para activar todas las funciones de Parking+.
            </flux:subheading>
        </div>

        <div id="plans-grid" class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach ($plans as $plan)
                <label
                    class="plan-card bg-white dark:bg-zinc-800 rounded-xl shadow-md p-6 flex flex-col cursor-pointer 
                           border-2 border-transparent transition-all duration-150 ease-out hover:border-zinc-200 dark:hover:border-zinc-700"
                    data-plan-id="{{ $plan->id }}"
                >
                    <input type="radio" name="plan_id" value="{{ $plan->id }}" class="sr-only">

                    <flux:heading level="2" size="lg" class="text-xl font-bold text-custom-blue mb-2">
                        {{ $plan->name }}
                    </flux:heading>

                    <flux:text class="text-sm text-black/70 dark:text-white/70 flex-grow">
                        {{ $plan->description }}
                    </flux:text>

                    <flux:text variant="strong" class="mt-4 text-3xl font-bold text-custom-blue">
                        ${{ number_format($plan->price, 2) }}
                        <span class="text-base text-black/60 dark:text-white/60">
                            / {{ $plan->duration_days }} días
                        </span>
                    </flux:text>
                </label>
            @endforeach
        </div>

        <div class="mt-6 max-w-lg mx-auto">
            <div id="paypal-button-container" class="flex justify-center"></div>
        </div>
    </div>

    <script
        src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.client_id') }}&currency={{ config('paypal.currency', 'MXN') }}&intent=capture"
        data-sdk-integration-source="button-factory">
    </script>

    <script>
        function initPayPalWhenReady() 
        {
            console.log('Checking...', typeof window.initPlansCheckout);
            
            if (typeof window.initPlansCheckout === 'function' && window.paypal && typeof window.paypal.Buttons === 'function') 
            {
                console.log('Initializing PayPal checkout...');
                window.initPlansCheckout(
                {
                    csrfToken: '{{ csrf_token() }}',
                    createOrderUrl: '{{ route('paypal.order.create') }}',
                    captureOrderUrl: '{{ url('/paypal/order') }}'
                });
            } else 
            {
                console.log('Retrying in 100ms...');
                setTimeout(initPayPalWhenReady, 100);
            }
        }

        if (document.readyState === 'loading') 
        {
            document.addEventListener('DOMContentLoaded', initPayPalWhenReady);
        } else 
        {
            initPayPalWhenReady();
        }
    </script>
</x-layouts.plans>
