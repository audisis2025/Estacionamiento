<?php
/*
* Nombre de la clase         : reset-password.blade.php
* Descripción de la clase    : Vista de restablecimiento de contraseña para usuarios.
* Fecha de creación          : 03/10/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 04/10/2025
* Autorizó                   : Angel Dávila
* Versión                    : 1.1
* Fecha de mantenimiento     : 16/11/2025
* Folio de mantenimiento     :
* Tipo de mantenimiento      : Correctivo
* Descripción del mantenimiento : Implementación de mensajes de alerta con SweetAlert2.
* Responsable                : Elian Pérez
* Revisor                    : Angel Dávila
*/

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    public function resetPassword(): void
    {
        $this->validate([
            'token'    => ['required'],
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password'       => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) 
        {
            $this->dispatch(
                'show-swal',
                icon: 'error',
                title: 'Error',
                text: __($status)
            );

            return;
        }

        $this->dispatch(
            'password-reset-success',
            text: __($status)
        );
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
                text: $first
            );

            $this->resetErrorBag();
            $stopPropagation();
        }
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('Restablecer contraseña')"
        :description="__('Por favor, ingresa tu nueva contraseña')"
    />


    <form method="POST" wire:submit="resetPassword" class="flex flex-col gap-6">
        <flux:input
            wire:model="email"
            :label="__('Correo electrónico')"
            type="email"
            required
            autocomplete="email"
        />

        <flux:input
            wire:model="password"
            :label="__('Contraseña')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Contraseña')"
            viewable
        />

        <flux:input
            wire:model="password_confirmation"
            :label="__('Confirmar contraseña')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirmar contraseña')"
            viewable
        />

        <div class="flex items-center justify-end">
            <flux:button
                type="submit"
                variant="primary"
                icon="key"
                icon-variant="outline"
                class="w-full bg-custom-blue hover:bg-custom-blue-dark text-white"
                data-test="reset-password-button"
            >
                {{ __('Restablecer contraseña') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-black/60 dark:text-white/60 mt-2">
        <span>{{ __('¿Ya recordaste tu contraseña?') }}</span>
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
                });
            });

            $wire.on('password-reset-success', (data) =>
            {
                Swal.fire(
                {
                    icon: 'success',
                    title: 'Contraseña actualizada',
                    text: data.text,
                })
                .then(() =>
                {
                    window.location.href = "{{ route('login') }}";
                });
            });
        </script>
    @endscript
</div>
