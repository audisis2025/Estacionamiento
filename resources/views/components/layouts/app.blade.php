@props(['title' => 'Parking'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
@stack('styles')
@stack('css')

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>{{ $title }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance

</head>
@stack('scripts')
@stack('js')

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Menu')" class="grid">
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    Inicio
                </flux:navlist.item>

                <flux:navlist.item icon="plus" :href="route('parking.edit')"
                    :current="request()->routeIs('parking.create')">
                    Mi estacionamiento
                </flux:navlist.item>

                <flux:navlist.item icon="qr-code" :href="route('parking.qr-readers.index')"
                    :current="request()->routeIs('parking.qr-readers.*')" wire:navigate>
                    Lectores QR
                </flux:navlist.item>

                <flux:navlist.item icon="users" :href="route('parking.client-types.index')"
                    :current="request()->routeIs('parking.client-types.*')" wire:navigate>
                    Tipos de cliente
                </flux:navlist.item>

                <flux:navlist.item icon="envelope" :href="route('parking.client-approvals.index')"
                    :current="request()->routeIs('parking.client-approvals.*')" wire:navigate>
                    Solicitudes de clientes
                </flux:navlist.item>

                <flux:navlist.item icon="lock-open" :href="route('parking.entries.index')"
                    :current="request()->routeIs('parking.entries.*')" wire:navigate>
                    Liberar entradas
                </flux:navlist.item>

                @php
                    $u = auth()->user();
                @endphp

                @if ($u && (int) ($u->id_role ?? 0) === 2 && (int) ($u->id_plan ?? 0) === 3)
                    <flux:navlist.item icon="document-text" :href="route('billing.index')"
                        :current="request()->routeIs('billing.*')" wire:navigate>
                        Facturación
                    </flux:navlist.item>
                @endif

            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        <!-- Desktop User Menu -->
        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                icon:trailing="chevrons-up-down" data-test="sidebar-menu-button" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>Configuración
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full"
                        data-test="logout-button">
                        Cerrar sesión
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>Configuración
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full"
                        data-test="logout-button">
                        Cerrar sesión
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <flux:main>
        {{ $slot }}
    </flux:main>

    @fluxScripts

    @php($swal = session()->pull('swal'))
    @if ($swal)
        <script>
            Swal.fire(@json($swal));
        </script>
    @endif

    @if ($errors->any())
        <script>
            const errs = @json($errors->all());
            const list = '<ul style="text-align:left;margin:0;padding-left:18px;">' +
                errs.slice(0, 5).map(e => `<li>${e}</li>`).join('') +
                (errs.length > 5 ? `<li>… (${errs.length - 5} más)</li>` : '') +
                '</ul>';

            Swal.fire({
                icon: 'error',
                title: 'Revisa la información',
                html: list,
            });
        </script>
    @endif

    @stack('js')
</body>

</html>
