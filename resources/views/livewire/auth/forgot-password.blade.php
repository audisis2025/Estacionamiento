<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::sendResetLink($this->only('email'));

        session()->flash('status', __('Contraseña restablecida enviada correctamente! Por favor, revisa tu correo electrónico.'));
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Recuperar contraseña')" :description="__('Ingresa tu correo electrónico para recibir un enlace de restablecimiento de contraseña')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="sendPasswordResetLink" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Correo electrónico')"
            type="email"
            required
            autofocus
            placeholder="email@gmail.com"
        />

        <flux:button variant="primary" type="submit" class="w-full" data-test="email-password-reset-link-button">
            {{ __('Enviar enlace') }}
        </flux:button>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
        <span>{{ __('O, regresa a') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('Iniciar sesión') }}</flux:link>
    </div>
</div>
