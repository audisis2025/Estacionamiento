// resources/js/plans-checkout.js

function initPlansCheckout(config)
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
        card.addEventListener(
            'pointerdown',
            () =>
            {
                fastHighlight(card);
            },
            {
                passive: true
            }
        );

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

                selectedPlanId = checked.value;

                return actions.resolve();
            },

            createOrder: function (data, actions)
            {
                if (!selectedPlanId)
                {
                    return actions.reject();
                }

                return fetch(config.createOrderUrl,
                {
                    method: 'POST',
                    headers:
                    {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
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
                        text: err.message || 'No se pudo crear la orden'
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

                return fetch(`${config.captureOrderUrl}/${data.orderID}/capture`,
                {
                    method: 'POST',
                    headers:
                    {
                        'X-CSRF-TOKEN': config.csrfToken,
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
                        text: 'No se pudo conectar con el servidor. Por favor intenta nuevamente.'
                    });
                });
            },

            onCancel: function ()
            {
                Swal.fire(
                {
                    icon: 'error',
                    title: 'Pago cancelado',
                    text: 'Has cancelado el proceso de pago'
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
        } else
        {
            setTimeout(whenSdkReady, 150);
        }
    }

    whenSdkReady();
}

window.initPlansCheckout = initPlansCheckout;

export default initPlansCheckout;