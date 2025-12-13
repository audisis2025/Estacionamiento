<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Recuperación</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f6f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #4a4a4a;
        }

        .wrapper {
            width: 100%;
            padding: 30px 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }

        .header {
            background-color: #eef3f8;
            padding: 24px;
            text-align: center;
            font-weight: 600;
            font-size: 18px;
            color: #1f2937;
        }

        .content {
            padding: 32px 40px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #1f2937;
        }

        .text {
            font-size: 14px;
            line-height: 1.7;
            color: #6b7280;
            margin-bottom: 24px;
        }

        .code-box {
            text-align: center;
            margin: 30px 0;
        }

        .code {
            display: inline-block;
            padding: 14px 24px;
            font-size: 28px;
            letter-spacing: 6px;
            font-weight: 600;
            color: #ffffff;
            background-color: #1f2937;
            border-radius: 6px;
        }

        .note {
            font-size: 13px;
            color: #6b7280;
            margin-top: 24px;
            line-height: 1.6;
        }

        .footer {
            border-top: 1px solid #e5e7eb;
            padding: 20px 40px;
            font-size: 12px;
            color: #9ca3af;
        }

        .link {
            word-break: break-all;
            color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                Parking+
            </div>

            <div class="content">
                <div class="greeting">¡Hola!</div>

                <p class="text">
                    Has recibido este mensaje porque se solicitó un código de recuperación
                    para tu cuenta.
                </p>

                <div class="code-box">
                    <div class="code">{{ $code }}</div>
                </div>

                <p class="text">
                    Este código expirará en <strong>15 minutos</strong>.
                </p>

                <p class="text">
                    Si no solicitaste este código, puedes ignorar este mensaje de correo electrónico.
                </p>

                <p class="text">
                    Saludos,<br>
                    Parking+
                </p>
            </div>

            <div class="footer">
                <p>
                    Este es un correo automático, por favor no respondas.
                </p>
                <p>
                    © {{ date('Y') }} Parking+. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
