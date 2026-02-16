<x-guest-layout>
    <div class="mx-auto max-w-md space-y-6">
        <div class="space-y-2 text-center">
            <h1 class="text-3xl font-bold">Registro</h1>
            <p class="text-gray-500 dark:text-gray-400">Completa los siguientes campos para crear una cuenta.</p>
        </div>
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="name" class="block">Nombre</label>
                    <input id="name" name="name" type="text" placeholder="Ingresa tu nombre" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" required value="{{ old('name') }}" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div class="space-y-2">
                    <label for="telefono" class="block">Teléfono</label>
                    <input id="telefono" name="telefono" type="tel" placeholder="Ingresa tu teléfono" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" required value="{{ old('telefono') }}" />
                    <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                </div>
            </div>
            <div class="space-y-2">
                <label for="direccion" class="block">Dirección</label>
                <input id="direccion" name="direccion" type="text" placeholder="Ingresa tu dirección" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" required value="{{ old('direccion') }}" />
                <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div class="space-y-2">
                    <label for="ciudad" class="block">Ciudad</label>
                    <input id="ciudad" name="ciudad" type="text" placeholder="Ingresa tu ciudad" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" required value="{{ old('ciudad') }}" />
                    <x-input-error :messages="$errors->get('ciudad')" class="mt-2" />
                </div>
                <div class="space-y-2">
                    <label for="estado" class="block">Estado</label>
                    <input id="estado" name="estado" type="text" placeholder="Ingresa tu estado" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" required value="{{ old('estado') }}" />
                    <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                </div>
                <div class="space-y-2">
                    <label for="codigo_postal" class="block">Código Postal</label>
                    <input id="codigo_postal" name="codigo_postal" type="text" placeholder="Ingresa tu código postal" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" required value="{{ old('codigo_postal') }}" />
                    <x-input-error :messages="$errors->get('codigo_postal')" class="mt-2" />
                </div>
            </div>
            <div class="space-y-2">
                <label for="email" class="block">Correo Electrónico</label>
                <input id="email" name="email" type="email" placeholder="Ingresa tu correo electrónico" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" required value="{{ old('email') }}" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="password" class="block">Contraseña</label>
                    <x-toggle-password id="password" name="password" placeholder="Ingresa tu contraseña" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div class="space-y-2">
                    <label for="password_confirmation" class="block">Confirmar Contraseña</label>
                    <x-toggle-password id="password_confirmation" name="password_confirmation" placeholder="Confirma tu contraseña" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" required />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>
            <div class="flex flex-col items-center justify-end mt-4">
                <button type="submit" class="w-full mb-3 justify-center text-white bg-black py-2 px-4 rounded-md hover:bg-slate-900">
                    Registrarse
                </button>
                <div class="text-center text-sm">
                    ¿Ya tienes cuenta? 
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                        Inicia sesión aquí.
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-guest-layout>
