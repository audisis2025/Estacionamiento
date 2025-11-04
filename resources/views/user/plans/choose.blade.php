<x-layouts.plans :title="__('Elige tu plan')">
    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="container mx-auto px-6 py-10">
        <h1 class="text-3xl font-bold mb-6 text-center">Elige tu plan</h1>

        <div id="plans-grid" class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($plans as $plan)
                <label
                    class="plan-card bg-white dark:bg-zinc-800 rounded-xl shadow p-6 flex flex-col cursor-pointer 
                           border-2 border-transparent transition-all duration-150 ease-out hover:border-zinc-300"
                    data-plan-id="{{ $plan->id }}"
                >
                    <input
                        type="radio"
                        name="plan_id"
                        value="{{ $plan->id }}"
                        class="sr-only"
                    >
                    <h2 class="text-xl font-bold text-custom-blue mb-2">{{ $plan->name }}</h2>
                    <p class="text-zinc-600 dark:text-zinc-400 flex-grow">{{ $plan->description }}</p>

                    <div class="mt-4 text-3xl font-bold text-custom-blue">
                        ${{ number_format($plan->price, 2) }}
                        <span class="text-base text-zinc-500">/ {{ $plan->duration_days }} días</span>
                    </div>
                </label>
            @endforeach
        </div>

        <div class="mt-10 max-w-lg mx-auto">
            <div id="paypal-button-container" class="flex justify-center"></div>
            <p class="mt-3 text-center text-sm text-zinc-500">Sandbox: usa una cuenta de prueba de PayPal.</p>

            @if(!config('paypal.client_id'))
                <p class="mt-2 text-center text-sm text-red-600">
                    Falta PAYPAL_SANDBOX_CLIENT_ID en tu .env / config. Ejecuta: php artisan config:clear
                </p>
            @endif
        </div>
    </div>

    {{-- SDK PayPal (sandbox) --}}
    <script src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.client_id') }}&currency={{ config('paypal.currency','MXN') }}&intent=capture"></script>

    <script>
    (function () {
        let selectedPlanId = null;
        let paypalRendered = false;

        // === Estilos visuales de selección ===
        const cards = document.querySelectorAll('.plan-card');
        const radios = document.querySelectorAll('input[name="plan_id"]');

        const SELECTED_CLASSES = [
            'border-2',
            'border-custom-blue-dark',
            'ring-4', 'ring-custom-blue-dark/40', // aro semitransparente
            'shadow-lg',
            'scale-[1.01]'
        ];

        function clearSelectedStyles() {
            cards.forEach(c => c.classList.remove(...SELECTED_CLASSES));
        }

        function setActiveCard() {
            clearSelectedStyles();
            const checked = document.querySelector('input[name="plan_id"]:checked');
            if (checked) {
                selectedPlanId = checked.value;
                const card = checked.closest('.plan-card');
                if (card) card.classList.add(...SELECTED_CLASSES);
            }
        }

        function fastHighlight(card) {
            clearSelectedStyles();
            card.classList.add(...SELECTED_CLASSES);
        }

        cards.forEach(card => {
            // feedback inmediato
            card.addEventListener('pointerdown', () => fastHighlight(card), { passive: true });

            // selección formal
            card.addEventListener('click', () => {
                const radio = card.querySelector('input[name="plan_id"]');
                radio.checked = true;
                setActiveCard();
                ensurePayPalButtons();
            });
        });

        radios.forEach(r => r.addEventListener('change', () => {
            setActiveCard();
            ensurePayPalButtons();
        }));

        // === Render PayPal cuando el SDK esté listo ===
        function ensurePayPalButtons() {
            if (paypalRendered) return;
            if (!window.paypal || typeof window.paypal.Buttons !== 'function') return;

            paypal.Buttons({
                style: { layout: 'vertical', shape: 'rect' },

                onClick: function (data, actions) {
                    const checked = document.querySelector('input[name="plan_id"]:checked');
                    if (!checked) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Plan no seleccionado',
                            text: 'Por favor selecciona un plan antes de continuar',
                            confirmButtonColor: '#3085d6'
                        });
                        return actions.reject();
                    }
                    selectedPlanId = checked.value;
                    return actions.resolve();
                },

                createOrder: function (data, actions) {
                    if (!selectedPlanId) {
                        return actions.reject();
                    }

                    return fetch('{{ route('paypal.order.create') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ plan_id: selectedPlanId })
                    })
                    .then(res => {
                        if (!res.ok) {
                            return res.json().then(err => {
                                throw new Error(err.message || 'Error al crear la orden');
                            });
                        }
                        return res.json();
                    })
                    .then(json => json.id)
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: err.message || 'No se pudo crear la orden',
                            confirmButtonColor: '#d33'
                        });
                        throw err;
                    });
                },

                onApprove: function (data, actions) {
                    Swal.fire({
                        title: 'Procesando pago...',
                        text: 'Por favor espera',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    return fetch(`{{ url('/paypal/order') }}/${data.orderID}/capture`, {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                            'Accept': 'application/json'
                        }
                    })
                    .then(async res => {
                        const json = await res.json().catch(() => ({}));
                        
                        if (!res.ok) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error al procesar el pago',
                                text: json.message || 'Ocurrió un error inesperado',
                                footer: json.debug_id ? `ID de seguimiento: ${json.debug_id}` : '',
                                confirmButtonColor: '#d33'
                            });
                            return;
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: '¡Pago completado!',
                            text: json.message || '¡Tu plan ha sido activado exitosamente!',
                            confirmButtonColor: '#28a745',
                            timer: 3000,
                            timerProgressBar: true
                        }).then(() => {
                            if (json.redirect) {
                                window.location.href = json.redirect;
                            }
                        });
                    })
                    .catch(err => { 
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión',
                            text: 'No se pudo conectar con el servidor. Por favor intenta nuevamente.',
                            confirmButtonColor: '#d33'
                        });
                    });
                },

                onCancel: function (data) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Pago cancelado',
                        text: 'Has cancelado el proceso de pago',
                        confirmButtonColor: '#3085d6'
                    });
                },

                onError: function (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error con PayPal',
                        text: 'Ocurrió un error con el servicio de PayPal. Por favor intenta nuevamente.',
                        confirmButtonColor: '#d33'
                    });
                }
            }).render('#paypal-button-container');

            paypalRendered = true;
        }

        // === Espera a que el SDK cargue ===
        function whenSdkReady() {
            if (window.paypal && typeof window.paypal.Buttons === 'function') {
                ensurePayPalButtons();
            } else {
                setTimeout(whenSdkReady, 150);
            }
        }

        whenSdkReady();
    })();
    </script>
</x-layouts.plans>
