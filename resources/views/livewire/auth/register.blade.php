<?php
/*
* Nombre de la clase         : register.blade.php
* Descripción de la clase    : Vista de registro para nuevos usuarios.
* Fecha de creación          : 03/10/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 04/10/2025
* Autorizó                   : Angel Dávila
* Versión                    : 1.1
* Fecha de mantenimiento     : 15/11/2025
* Folio de mantenimiento     : 
* Descripción del mantenimiento : Implementación de mensajes de alerta con SweetAlert2.
* Responsable                : Elian Pérez
* Revisor                    : Angel Dávila
*/

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component 
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $phone_number = '';
    public bool $terms = false;

    public function register(): void
    {
        $validated = $this->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password'      => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'phone_number' => ['required', 'numeric', 'digits:10', 'unique:users,phone_number'],
            'terms'         => ['accepted'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['amount']   = 0;
        $validated['id_role']  = 2;

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);
        Session::regenerate();

        if ($user->isAdmin()) 
        {
            $this->redirect(route('admin.dashboard'), navigate: true);
        } else 
        {
            $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
        }
    }

    public function exception($e, $stopPropagation): void
    {
        if ($e instanceof ValidationException) 
        {
            $first = collect($e->errors())->flatten()->first();

            $this->dispatch('show-swal', icon: 'error', title: 'Error', text: $first);

            $this->resetErrorBag();

            $stopPropagation();
        }
    }
}; ?>


<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('Crear cuenta')"
        :description="__('Ingresa tus datos a continuación para crear tu cuenta')"
    />

    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="register" class="flex flex-col gap-6">
        <flux:input
            wire:model="name"
            :label="__('Nombre')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('Juan Pérez')"
        />

        <flux:input
            wire:model="email"
            :label="__('Correo electrónico')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@gmail.com"
        />

        <flux:input
            wire:model="phone_number"
            :label="__('Número de teléfono')"
            type="text"
            required
            autocomplete="tel"
            placeholder="5551234567"
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

        <div class="flex items-start gap-3">
            <flux:checkbox
                id="terms"
                wire:model="terms"
                name="terms"
                class="mt-0.5"
            />

            <flux:text class="text-xs text-black/70 dark:text-white/70">
                He leído y acepto los
                <flux:link
                    href="{{ route('terms') }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-custom-blue hover:text-custom-blue-dark underline underline-offset-4"
                >
                    Términos y Condiciones
                </flux:link>
                de Parking+.
            </flux:text>
        </div>

        <div class="flex items-center justify-end">
            <flux:button icon="user-plus" icon-variant="outline" type="submit" variant="primary" class="w-full bg-black hover:bg-custom-gray text-white" data-test="register-user-button">
                {{ __('Registrarse') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-black/60 dark:text-white/60">
        <span>{{ __('¿Ya tienes una cuenta?') }}</span>
        <flux:link :href="route('login')" wire:navigate class="text-custom-blue hover:text-custom-blue-dark">
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
                    }
                );
            });
        </script>
    @endscript
</div>
