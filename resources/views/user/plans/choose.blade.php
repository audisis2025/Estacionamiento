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

    <script>
        (function ()
            {
                let selectedPlanId = null;
                let paypalRendered = false;

                const cards  = document.querySelectorAll('.plan-card');
                const radios = document.querySelectorAll('input[name="plan_id"]');

                const SELECTED_CLASSES = [
                    'border-2',
                    'border-custom-blue-dark',
                    'ring-4',
                    'ring-custom-blue-dark/40',
                    'shadow-lg',
                    'scale-[1.01]'
                ];

                function clearSelectedStyles()
                {
                    cards.forEach(c => c.classList.remove(...SELECTED_CLASSES));
                }

                function setActiveCard()
                {
                    clearSelectedStyles();

                    const checked = document.querySelector('input[name="plan_id"]:checked');

                    if (checked)
                    {
                        selectedPlanId = checked.value;
                        const card = checked.closest('.plan-card');

                        if (card)
                        {
                            card.classList.add(...SELECTED_CLASSES);
                        }
                    }
                }

                function fastHighlight(card)
                {
                    clearSelectedStyles();
                    card.classList.add(...SELECTED_CLASSES);
                }

                cards.forEach(card =>
                {
                    card.addEventListener('pointerdown', () =>
                    {
                        fastHighlight(card);
                    }, { passive: true });

                    card.addEventListener('click', () =>
                    {
                        const radio = card.querySelector('input[name="plan_id"]');
                        radio.checked = true;

                        setActiveCard();
                        ensurePayPalButtons();
                    });
                });

                radios.forEach(r =>
                {
                    r.addEventListener('change', () =>
                    {
                        setActiveCard();
                        ensurePayPalButtons();
                    });
                });

                function ensurePayPalButtons()
                {
                    if (paypalRendered) return;
                    if (!window.paypal || typeof window.paypal.Buttons !== 'function') return;

                    paypal.Buttons(
                    {
                        style:
                        {
                            layout: 'vertical',
                            shape: 'rect'
                        },

                        onClick: function (data, actions)
                        {
                            const checked = document.querySelector('input[name="plan_id"]:checked');
                            const termsCheckbox = document.getElementById('terms');

                            if (!checked)
                            {
                                Swal.fire(
                                {
                                    icon: 'warning',
                                    title: 'Plan no seleccionado',
                                    text: 'Por favor selecciona un plan antes de continuar',
                                });

                                return actions.reject();
                            }

                            if (!termsCheckbox || !termsCheckbox.checked)
                            {
                                Swal.fire(
                                {
                                    icon: 'warning',
                                    title: 'Términos y Condiciones',
                                    text: 'Debes aceptar los Términos y Condiciones antes de continuar con el pago.',
                                });

                                return actions.reject();
                            }

                            selectedPlanId = checked.value;

                            return actions.resolve();
                        },

                        createOrder: function (data, actions)
                        {
                            if (!selectedPlanId)
                            {
                                return actions.reject();
                            }

                            return fetch('{{ route('paypal.order.create') }}',
                            {
                                method: 'POST',
                                headers:
                                {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ plan_id: selectedPlanId })
                            })
                            .then(res =>
                            {
                                if (!res.ok)
                                {
                                    return res.json().then(err =>
                                    {
                                        throw new Error(err.message || 'Error al crear la orden');
                                    });
                                }

                                return res.json();
                            })
                            .then(json => json.id)
                            .catch(err =>
                            {
                                Swal.fire(
                                {
                                    icon: 'error',
                                    title: 'Error',
                                    text: err.message || 'No se pudo crear la orden',
                                });

                                throw err;
                            });
                        },

                        onApprove: function (data, actions)
                        {
                            Swal.fire(
                            {
                                title: 'Procesando pago...',
                                text: 'Por favor espera',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () =>
                                {
                                    Swal.showLoading();
                                }
                            });

                            return fetch(`{{ url('/paypal/order') }}/${data.orderID}/capture`,
                            {
                                method: 'POST',
                                headers:
                                {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(async res =>
                            {
                                const json = await res.json().catch(() => ({}));

                                if (!res.ok)
                                {
                                    Swal.fire(
                                    {
                                        icon: 'error',
                                        title: 'Error al procesar el pago',
                                        text: json.message || 'Ocurrió un error inesperado',
                                        footer: json.debug_id ? `ID de seguimiento: ${json.debug_id}` : '',
                                    });

                                    return;
                                }

                                Swal.fire(
                                {
                                    icon: 'success',
                                    title: '¡Pago completado!',
                                    text: json.message || '¡Tu plan ha sido activado exitosamente!',
                                    timer: 3000,
                                    timerProgressBar: true
                                })
                                .then(() =>
                                {
                                    if (json.redirect)
                                    {
                                        window.location.href = json.redirect;
                                    }
                                });
                            })
                            .catch(() =>
                            {
                                Swal.fire(
                                {
                                    icon: 'error',
                                    title: 'Error de conexión',
                                    text: 'No se pudo conectar con el servidor. Por favor intenta nuevamente.',
                                });
                            });
                        },

                        onCancel: function ()
                        {
                            Swal.fire(
                            {
                                icon: 'error',
                                title: 'Pago cancelado',
                                text: 'Has cancelado el proceso de pago',
                            });
                        },

                        onError: function ()
                        {
                            Swal.fire(
                            {
                                icon: 'error',
                                title: 'Error con PayPal',
                                text: 'Ocurrió un error con el servicio de PayPal. Por favor intenta nuevamente.'
                            });
                        }
                    })
                    .render('#paypal-button-container');

                    paypalRendered = true;
                }

                function whenSdkReady()
                {
                    if (window.paypal && typeof window.paypal.Buttons === 'function')
                    {
                        ensurePayPalButtons();
                    }
                    else
                    {
                        setTimeout(whenSdkReady, 150);
                    }
                }

                whenSdkReady();
            })();
    </script>

</x-layouts.plans>
