<!-- resources/views/admin/suppliers/_form.blade.php -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Nombre -->
    <div>
        <x-input-label for="name" value="Nombre" />
        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $supplier->name ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <!-- Nombre de la Empresa -->
    <div>
        <x-input-label for="company_name" value="Nombre de la Empresa" />
        <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name', $supplier->company_name ?? '')" />
        <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
    </div>

    <!-- Persona de Contacto -->
    <div>
        <x-input-label for="contact_person" value="Persona de Contacto" />
        <x-text-input id="contact_person" class="block mt-1 w-full" type="text" name="contact_person" :value="old('contact_person', $supplier->contact_person ?? '')" />
        <x-input-error :messages="$errors->get('contact_person')" class="mt-2" />
    </div>

    <!-- Email -->
    <div>
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $supplier->email ?? '')" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <!-- Teléfono -->
    <div>
        <x-input-label for="phone" value="Teléfono" />
        <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $supplier->phone ?? '')" />
        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>

    <!-- Dirección -->
    <div>
        <x-input-label for="address" value="Dirección" />
        <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $supplier->address ?? '')" />
        <x-input-error :messages="$errors->get('address')" class="mt-2" />
    </div>

    <!-- Ciudad -->
    <div>
        <x-input-label for="city" value="Ciudad" />
        <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city', $supplier->city ?? '')" />
        <x-input-error :messages="$errors->get('city')" class="mt-2" />
    </div>

    <!-- Estado/Provincia -->
    <div>
        <x-input-label for="state" value="Estado/Provincia" />
        <x-text-input id="state" class="block mt-1 w-full" type="text" name="state" :value="old('state', $supplier->state ?? '')" />
        <x-input-error :messages="$errors->get('state')" class="mt-2" />
    </div>

    <!-- Código Postal -->
    <div>
        <x-input-label for="zip_code" value="Código Postal" />
        <x-text-input id="zip_code" class="block mt-1 w-full" type="text" name="zip_code" :value="old('zip_code', $supplier->zip_code ?? '')" />
        <x-input-error :messages="$errors->get('zip_code')" class="mt-2" />
    </div>

    <!-- País -->
    <div>
        <x-input-label for="country" value="País" />
        <x-text-input id="country" class="block mt-1 w-full" type="text" name="country" :value="old('country', $supplier->country ?? '')" />
        <x-input-error :messages="$errors->get('country')" class="mt-2" />
    </div>

    <!-- Estado (Activo/Inactivo) -->
    <div class="md:col-span-2">
        <div class="flex items-center">
            <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ old('is_active', $supplier->is_active ?? true) ? 'checked' : '' }}>
            <label for="is_active" class="ml-2 block text-sm text-gray-600">Proveedor Activo</label>
        </div>
        <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
    </div>

    <!-- Notas -->
    <div class="md:col-span-2">
        <x-input-label for="notes" value="Notas" />
        <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('notes', $supplier->notes ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
    </div>
</div>