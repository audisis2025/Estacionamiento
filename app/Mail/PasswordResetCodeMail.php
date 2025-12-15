<?php
/*
* Nombre de la clase         : PasswordResetCodeMail.php
* Descripción de la clase    : Mailable para enviar el código de recuperación de contraseña al usuario.
* Fecha de creación          : 14/12/2025
* Elaboró                    : Jonathan Diaz
* Fecha de liberación        : 14/12/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.0
* Fecha de mantenimiento     : 
* Folio de mantenimiento     : 
* Tipo de mantenimiento      : 
* Descripción del mantenimiento :
* Responsable                : 
* Revisor                    : 
*/
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code)
    {

    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Código de Recuperación de Contraseña');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.password-reset-code');
    }

    public function attachments(): array
    {
        return [];
    }
}
