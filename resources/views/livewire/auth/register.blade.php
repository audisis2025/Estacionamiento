<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $phone_number = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'phone_number' => ['required', 'string', 'size:10', 'unique:users,phone_number'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['amount'] = 0;
        $validated['id_role'] = 2;

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);
        Session::regenerate();

        if ($user->isAdmin()) {
            $this->redirect(route('admin.dashboard'), navigate: true);
        } else {
            $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
        }
        return;
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Crear cuenta')" :description="__('Ingresa tus datos a continuación para crear tu cuenta')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input wire:model="name" :label="__('Nombre')" type="text" required autofocus autocomplete="name"
            :placeholder="__('Pedro Filomeno')" />

        <!-- Email Address -->
        <flux:input wire:model="email" :label="__('Correo electronico')" type="email" required autocomplete="email"
            placeholder="email@gmail.com" />

        <flux:input wire:model="phone_number" :label="__('Numero de telefono')" type="text" required
            autocomplete="tel" placeholder="5551234567" />

        <!-- Password -->
        <flux:input wire:model="password" :label="__('Contraseña')" type="password" required autocomplete="new-password"
            :placeholder="__('Contraseña')" viewable />

        <!-- Confirm Password -->
        <flux:input wire:model="password_confirmation" :label="__('Confirmar contraseña')" type="password" required
            autocomplete="new-password" :placeholder="__('Confirmar contraseña')" viewable />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                {{ __('Crear cuenta') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('¿Ya tienes una cuenta?') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('Iniciar sesión') }}</flux:link>
    </div>
</div>
