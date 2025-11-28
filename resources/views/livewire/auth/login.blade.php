<?php
/*
* Nombre de la clase         : login.blade.php
* Descripción de la clase    : Vista de inicio de sesión para usuarios.
* Fecha de creación          : 03/10/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 04/10/2025
* Autorizó                   : Angel Dávila
* Versión                    : 1.1
* Fecha de mantenimiento     : 15/11/2025
* Folio de mantenimiento     :
* Tipo de mantenimiento      : Correctivo
* Descripción del mantenimiento : Implementación de mensajes de alerta con SweetAlert2.
* Responsable                : Elian Pérez
* Revisor                    : Angel Dávila
*/

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Features;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Carbon\Carbon;

new #[Layout('components.layouts.auth')] class extends Component 
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate();
        $this->ensureIsNotRateLimited();

        $now = Carbon::now();

        $affected = User::query()
            ->whereNotNull('id_plan')
            ->where('id_plan', '!=', 4)
            ->whereNotNull('end_date')
            ->where('end_date', '<', $now)
            ->update([
                'id_plan'  => null,
                'end_date' => null,
            ]);

        $user = $this->validateCredentials();

        if (Features::canManageTwoFactorAuthentication() && $user->hasEnabledTwoFactorAuthentication()) 
        {
            Session::put(['login.id' => $user->getKey(),'login.remember' => $this->remember]);

            $this->redirect(route('two-factor.login'), navigate: true);

            return;
        }

        Auth::login($user, $this->remember);

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        if ($user->isAdmin()) 
        {
            $this->redirectIntended(
                default: route('admin.dashboard', absolute: false), navigate: true);
        } else 
        {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        }
    }

    protected function validateCredentials(): User
    {
        $user = Auth::getProvider()->retrieveByCredentials(['email' => $this->email,'password' => $this->password,]);

        if (!$user || !Auth::getProvider()->validateCredentials($user, ['password' => $this->password])) 
        {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages(['email' => __('auth.failed'),]);
        }

        return $user;
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) 
        {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages(['email' => __('auth.throttle', ['seconds' => $seconds,'minutes' => ceil($seconds / 60),]),]);
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

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}; ?>


<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Ingresar a tu cuenta')" :description="__('Ingresa tu correo electrónico y contraseña a continuación para iniciar sesión')" />

    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="login" class="flex flex-col gap-6">
        <flux:input wire:model="email" :label="__('Correo electrónico')" type="email" required autofocus
            autocomplete="email" placeholder="email@gmail.com" />

        <div class="relative">
            <flux:input wire:model="password" :label="__('Contraseña')" type="password" required
                autocomplete="current-password" :placeholder="__('Contraseña')" viewable />

            @if (Route::has('password.request'))
                <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                    {{ __('¿Olvidaste tu contraseña?') }}
                </flux:link>
            @endif
        </div>

        <flux:checkbox wire:model="remember" :label="__('Recordarme')" />

        <div class="flex items-center justify-end">
            <flux:button icon="arrow-right-start-on-rectangle" icon-variant="outline" variant="primary" type="submit"
                class="w-full bg-custom-blue hover:bg-custom-blue-dark text-white" data-test="login-button">
                {{ __('Iniciar sesión') }}
            </flux:button>
        </div>
    </form>

    @if (Route::has('register'))
        <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-black/60 dark:text-white/60">
            <span>{{ __('¿No tienes una cuenta?') }}</span>
            <flux:link :href="route('register')" wire:navigate class="text-custom-blue hover:text-custom-blue-dark">
                {{ __('Regístrate') }}
            </flux:link>
        </div>
    @endif

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
