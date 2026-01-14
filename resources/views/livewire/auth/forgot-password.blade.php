<?php
/*
* Nombre de la clase         : forgot-password.blade.php
* Descripción de la clase    : Vista de recuperación de contraseña para usuarios.
* Fecha de creación          : 03/10/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 04/10/2025
* Autorizó                   : Angel Dávila
* Versión                    : 1.1
* Fecha de mantenimiento     : 16/11/2025
* Folio de mantenimiento     : L0007
* Tipo de mantenimiento      : Perfectivo
* Descripción del mantenimiento : Implementación de mensajes de alerta con SweetAlert2.
* Responsable                : Elian Pérez
* Revisor                    : Angel Dávila
*/

use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component
{
    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate(['email' => [
                'required', 
                'string', 
                'email'
            ],
        ]);
    
        $status = Password::sendResetLink($this->only('email'));

        if ($status === Password::RESET_LINK_SENT)
        {
            $this->dispatch(
                'show-swal',
                icon: 'success',
                title: 'Enlace enviado',
                text: __($status)
            );
        } else
        {
            $this->dispatch(
                'show-swal',
                icon: 'error',
                title: 'Error',
                text: __($status),
                confirmButtonColor: '#494949'
            );
        }
    }

    public function exception($e, $stopPropagation): void
    {
        if ($e instanceof ValidationException)
        {
            $first = collect($e->errors())->flatten()->first();

            $this->dispatch(
                'show-swal',
                icon: 'error',
                title: 'Error',
                text: $first,
                confirmButtonColor: '#494949'
            );

            $this->resetErrorBag();
            $stopPropagation();
        }
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('Recuperar contraseña')"
        :description="__('Ingresa tu correo electrónico para recibir un enlace de restablecimiento de contraseña')"
    />


    <form method="POST" wire:submit="sendPasswordResetLink" class="flex flex-col gap-6">
        <flux:input
            wire:model="email"
            :label="__('Correo electrónico')"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="email@gmail.com"
        />

        <flux:button
            type="submit"
            variant="primary"
            icon="paper-airplane"
            icon-variant="outline"
            class="w-full bg-black hover:bg-custom-gray text-white"
            data-test="email-password-reset-link-button"
        >
            {{ __('Enviar enlace') }}
        </flux:button>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-black/60 dark:text-white/60">
        <span>{{ __('O, regresa a') }}</span>
        <flux:link
            :href="route('login')"
            wire:navigate
            class="text-custom-blue hover:text-custom-blue-dark"
        >
            {{ __('Iniciar sesión') }}
        </flux:link>
    </div>

    @script
        <script>
            $wire.on('show-swal', (data) =>
            {
                Swal.fire(
                {
                    icon: data.icon,
                    title: data.title,
                    text: data.text,
                    confirmButtonColor: '#494949'
                });
            });
        </script>
    @endscript
</div>