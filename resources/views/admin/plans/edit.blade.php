{{--
* Nombre de la vista           : edit.blade.php
* Descripción de la vista      : Panel de edición de planes.
* Fecha de creación            : 03/11/2025
* Elaboró                      : Elian Pérez
* Fecha de liberación          : 04/11/2025
* Autorizó                     : Angel Davila
* Version                      : 1.1
* Fecha de mantenimiento       : 17/11/2025
* Folio de mantenimiento       :
* Tipo de mantenimiento        : Correctivo
* Descripción del mantenimiento: Actualización de la interfaz
* Responsable                  : Elian Pérez
* Revisor                      : Angel Davila
--}}
<x-layouts.admin :title="__('Editar plan')">
    <div class="mx-auto w-full max-w-xl p-6">

        <h2 class="text-2xl font-bold mb-5 text-black dark:text-white">
            Editar plan
        </h2>

        <form method="POST" action="{{ route('admin.plans.update', $plan) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="space-y-1">
                <flux:select 
                    name="type_view"
                    label="Tipo de plan"
                    class="w-full text-black dark:text-white"
                    disabled
                >
                    <option value="parking" @selected(old('type', $plan->type)==='parking')>
                        Para estacionamientos
                    </option>

                    <option value="user" @selected(old('type', $plan->type)==='user')>
                        Para usuarios
                    </option>
                </flux:select>
            </div>

            <div class="space-y-1">
                <flux:input
                    name="name"
                    label="Nombre del plan"
                    type="text"
                    required
                    value="{{ old('name', $plan->name) }}"
                />
            </div>

            <div class="space-y-1">
                <flux:input
                    name="price"
                    label="Precio (MXN)"
                    type="number"
                    step="0.01"
                    min="0"
                    required
                    value="{{ old('price', $plan->price) }}"
                />
            </div>

            <div class="space-y-1">
                <flux:input
                    name="duration_days"
                    label="Duración en días"
                    type="number"
                    min="1"
                    required
                    value="{{ old('duration_days', $plan->duration_days) }}"
                />
            </div>

            <div class="space-y-1">
                <flux:textarea
                    name="description"
                    label="Descripción del plan"
                    rows="3"
                    required
                >{{ old('description', $plan->description) }}</flux:textarea>
            </div>

            <div class="flex justify-center pt-2">
                <flux:button
                    type="submit"
                    variant="primary"
                    icon="check-circle"
                    icon-variant="outline"
                    class="bg-blue-600 hover:bg-blue-700 text-white"
                >
                    Guardar
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts.admin>
