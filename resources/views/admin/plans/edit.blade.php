<x-layouts.admin :title="__('Editar plan')">
    <div class="mx-auto w-full max-w-xl p-6">
        <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-100">Editar plan</h2>

        <form method="POST" action="{{ route('admin.plans.update', $plan) }}" class="flex flex-col gap-5">
            @csrf
            @method('PUT')

            <div class="space-y-1">
                <flux:select name="type" label="Tipo de plan" required class="w-full">
                    <option value="parking" @selected(old('type', $plan->type)==='parking')>Para estacionamientos</option>
                    <option value="user" @selected(old('type', $plan->type)==='user')>Para usuarios</option>
                </flux:select>
            </div>

            <div class="space-y-1">
                <flux:input name="name" label="Nombre del plan" type="text" required
                    value="{{ old('name', $plan->name) }}" />
            </div>

            <div class="space-y-1">
                <flux:input name="price" label="Precio (MXN)" type="number" step="0.01" min="0" required
                    value="{{ old('price', $plan->price) }}" />
            </div>

            <div class="space-y-1">
                <flux:input name="duration_days" label="Duración en días" type="number" min="1" required
                    value="{{ old('duration_days', $plan->duration_days) }}" />
            </div>

            <div class="space-y-1">
                <flux:textarea name="description" label="Descripción del plan" rows="3" required>{{ old('description', $plan->description) }}</flux:textarea>
            </div>

            <div class="flex justify-center gap-3 mt-2">
                <flux:button type="submit" variant="primary" class="bg-blue-600 hover:bg-blue-700">Guardar</flux:button>
            </div>

        </form>
    </div>
</x-layouts.admin>
