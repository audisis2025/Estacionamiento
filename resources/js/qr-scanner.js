/*
* Nombre de la clase         : qr-escanner.js
* Descripción de la clase    : Archivo de JavaScript para gestionar la lectura de códigos QR.
* Fecha de creación          : 
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 
* Autorizó                   : Angel Davila
* Versión                    : 1.0 
* Fecha de mantenimiento     : 
* Folio de mantenimiento     : 
* Tipo de mantenimiento      : 
* Descripción del mantenimiento : 
* Responsable                : 
* Revisor                    : 
*/
export default function initQrScanner(routeUrl, csrfTok)
{
    (function ()
    {
        if (window.__qrScannerInit)
        {
            return;
        }

        function initScannerOnce()
        {
            if (window.__qrScannerBound)
            {
                return;
            }

            window.__qrScannerBound = true;

            const input   = document.getElementById('scan-input');
            const focusB  = document.getElementById('focus-btn');
            const result  = document.getElementById('result');

            if (! input || ! focusB)
            {
                window.__qrScannerBound = false;
                return;
            }

            let busy     = false,
                lastText = '',
                lastWhen = 0,
                buf      = '',
                last     = 0,
                timer    = null;

            const DUP_WINDOW_MS = 2500;
            const BUSY_COOLDOWN = 2500;

            function sanitize(s)
            {
                return (s || '')
                    .replace(/\r?\n/g, '')
                    .replace(/\r/g, '')
                    .replace(/[\u201C\u201D]/g, '"')
                    .trim();
            }

            function sameAsLastNow(txt)
            {
                const now = Date.now();
                return txt === lastText && (now - lastWhen) < DUP_WINDOW_MS;
            }

            function remember(txt)
            {
                lastText = txt;
                lastWhen = Date.now();
            }

            async function submitScan(text)
            {
                const clean = sanitize(text);

                if (! clean || sameAsLastNow(clean) || busy)
                {
                    return;
                }

                busy = true;
                remember(clean);

                if (result)
                {
                    result.textContent = 'Procesando...';
                }

                try
                {
                    const fd = new FormData();
                    fd.append('qr', clean);

                    const res = await fetch(routeUrl,
                    {
                        method      : 'POST',
                        headers     : { 'X-CSRF-TOKEN': csrfTok },
                        body        : fd,
                        credentials : 'same-origin'
                    });

                    const json = await res.json().catch(() => ({}));

                    if (result)
                    {
                        result.textContent = JSON.stringify(json, null, 2);
                    }

                    if (res.ok && json.ok && ! json.silent) 
                    {
                        const evt = json?.data?.event;

                        if (evt === 'entry' || evt === 'exit') 
                        {
                            Swal.fire(
                            {
                                icon : 'success',
                                title : json.message || (evt === 'entry' ? 'Entrada registrada' : 'Salida registrada'),
                                timer : 2000,
                                showConfirmButton: false
                            });
                        } else if (evt === 'entry_pending') 
                        {
                            Swal.fire(
                            {
                                icon: 'info',
                                title: 'Entrada pendiente',
                                text: 'El usuario debe confirmar en la app si pagará por hora o por tiempo libre.',
                                timer: 4000,
                                showConfirmButton: false
                            });
                        }
                    }

                    if (! res.ok || (json && json.ok === false))
                    {
                        Swal.fire(
                        {
                            icon : 'error',
                            title : (json && json.message) ? json.message : `Error ${res.status}`,
                            timer : 3000,
                            showConfirmButton: false
                        });
                    }
                } catch (e)
                {
                    if (result)
                    {
                        result.textContent = e?.message || String(e);
                    }

                    Swal.fire(
                    {
                        icon  : 'error',
                        title : 'Error de red',
                        text  : 'No se pudo enviar el escaneo. Intenta nuevamente.',
                        confirmButtonColor: '#494949'
                    });
                } finally
                {
                    setTimeout(() => busy = false, BUSY_COOLDOWN);
                }
            }

            function flush()
            {
                if (! buf)
                {
                    return;
                }

                const payload = buf.trim();
                buf = '';

                if (payload.startsWith('{') && payload.endsWith('}'))
                {
                    submitScan(payload);
                }
            }

            function onKeydown(e)
            {
                const ae  = document.activeElement;
                const tag = (ae?.tagName || '').toLowerCase();

                const typingElsewhere =
                    (tag === 'input' || tag === 'textarea') && ae !== input;

                if (typingElsewhere)
                {
                    return;
                }

                const now = Date.now();

                if (now - last > 150)
                {
                    buf = '';
                }

                last = now;

                if (e.key === 'Enter')
                {
                    e.preventDefault();
                    clearTimeout(timer);
                    flush();
                    return;
                }

                if (e.key.length === 1)
                {
                    buf += e.key;
                    clearTimeout(timer);
                    timer = setTimeout(flush, 220);
                }
            }

            if (! window.__qrKeydown)
            {
                window.__qrKeydown = onKeydown;
                document.addEventListener('keydown', window.__qrKeydown);
            }

            input.addEventListener('input', (e) =>
            {
                const v = (e.target.value || '');

                if (/\r?\n$/.test(v) || /\r$/.test(v))
                {
                    submitScan(v);
                    e.target.value = '';
                }
            });

            input.style.position = 'fixed';
            input.style.left     = '-9999px';
            input.style.top      = '0';
            input.setAttribute('tabindex', '-1');

            setTimeout(() => input.focus(), 50);

            focusB.addEventListener('click', () =>
            {
                setTimeout(() => input.focus(), 10);
            });

            document.addEventListener('visibilitychange', () =>
            {
                if (! document.hidden)
                {
                    setTimeout(() => input.focus(), 10);
                }
            });
        }

        function mountScanner()
        {
            if (! window.__qrScannerInit)
            {
                window.__qrScannerInit = true;

                document.addEventListener('livewire:navigated', () =>
                {
                    setTimeout(initScannerOnce, 0);
                });
            }

            setTimeout(initScannerOnce, 0);
        }

        if (document.readyState === 'loading')
        {
            document.addEventListener('DOMContentLoaded', mountScanner);
        } else
        {
            mountScanner();
        }
    })();
}