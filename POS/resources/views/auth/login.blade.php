<x-guest-layout>
<div class="mx-auto max-w-md space-y-6">
    <div class="space-y-2 text-center">
        <h1 class="text-3xl font-bold">Iniciar sesión</h1>
        <p class="text-gray-500 dark:text-gray-400">Ingresa tus credenciales para comenzar.</p>
    </div>
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div class="space-y-2">
            <label for="email" class="block">Correo Electrónico</label>
            <input id="email" name="email" type="email" placeholder="m@example.com" required class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="space-y-2">
            <label for="password" class="block">Contraseña</label>
            <x-toggle-password id="password" name="password" required class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ml-2 text-sm text-gray-600">{{ __('Recordarme') }}</span>
            </label>
        </div>



        <Button type="submit" class="w-full mb-3 justify-center text-white bg-black py-2 px-4 rounded-md hover:bg-slate-900">
            Iniciar sesión
        </Button>
    </form>

    <div class="text-center text-sm">
        ¿Aún no tienes una cuenta?
        <a href="{{ route('register') }}" class="font-medium underline underline-offset-2">Regístrate</a>
    </div>
</div>

</x-guest-layout>
