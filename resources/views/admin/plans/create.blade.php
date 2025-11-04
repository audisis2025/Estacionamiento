<x-layouts.admin :title="__('Crear plan')">
    <div class="mx-auto w-full max-w-xl p-6"> {{-- antes: max-w-3xl --}}
        <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-100">
            {{ __('Agregar nuevo plan') }}
        </h2>

        <form method="POST" action="{{ route('admin.plans.store') }}" class="flex flex-col gap-5">
            @csrf

            <div class="space-y-1">
                <flux:select name="type" label="Tipo de plan" required class="w-full">
                    <option value="" disabled selected>Selecciona un tipo</option>
                    <option value="parking" @selected(old('type')==='parking')>Para estacionamientos</option>
                    <option value="user" @selected(old('type')==='user')>Para usuarios</option>
                </flux:select>
            </div>

            <div class="space-y-1">
                <flux:input name="name" label="Nombre del plan" type="text" required
                            placeholder="Ej. Plan Premium" value="{{ old('name') }}" class="w-full" />
            </div>

            <div class="space-y-1">
                <flux:input name="price" label="Precio (MXN)" type="number" step="0.01" min="0" required
                            placeholder="Ej. 199.99" value="{{ old('price') }}" class="w-full" />
            </div>

            <div class="space-y-1">
                <flux:input name="duration_days" label="Duración en días" type="number" min="1" required
                            placeholder="Ej. 30" value="{{ old('duration_days') }}" class="w-full" />
            </div>

            <div class="space-y-1">
                <flux:textarea name="description" label="Descripción del plan" rows="3" required
                               placeholder="Breve descripción del plan..." class="w-full">{{ old('description') }}</flux:textarea>
            </div>

            <div class="flex justify-center gap-3 mt-2">
                <flux:button type="submit" variant="primary" class="bg-blue-600 hover:bg-blue-700" >Guardar</flux:button>
            </div>
        </form>
    </div>
</x-layouts.admin>
