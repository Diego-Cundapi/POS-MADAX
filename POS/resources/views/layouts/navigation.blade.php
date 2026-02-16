<div>
@if (auth()->check())
    <header class="bg-gray-900 text-white py-4 px-6 md:px-8 flex items-center justify-between">
        <div class="text-2xl font-bold">
            <a href="{{ route('index') }}">Autopartes Madax</a>
        </div>
            <div class="flex items-center space-x-4">
                <span class="text-white">¡Bienvenido, {{ Auth::user()->name }}!</span>

                <a class="hover:text-white/40" href="{{'/'}}">
                <i class="fas fa-home text-2xl"></i> Home
                </a>
                @hasanyrole('Admin|Empleado')
                    <a href="{{ route('dashboard') }}" class="bg-black py-2 px-4 text-base rounded-md hover:text-blue-500">
                    <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                @endhasanyrole
                @role('cliente')
                    <a href="{{route('carrito.index')}}" class="hover:text-white/40">
                    <i class="fa-solid fa-cart-shopping text-2xl"></i></a>
                    <a href="{{route('vercompras')}}" class="hover:text-white/40 cursor-pointer ml-4">
                        <i class="fa-solid fa-shopping-bag text-2xl"></i>
                    </a>
                @endrole
                <a href="{{route('logout')}}" class="hover:text-white/90">Cerrar sesión</a>
            </div>
    </header>
@else
    <header class="bg-gray-900 text-white py-4 px-6 md:px-8 flex items-center justify-between">

        <div class="text-2xl font-bold">
            <a href="{{ route('index') }}">Autopartes Madax</a>
        </div>
            <!-- <div class="">
                <img src="{{ asset('imagenes/logo.jpg') }}" class="h-10" alt="Logo">
            </div> -->
                <div class="flex items-center space-x-4">
                    <a class="hover:text-white/40" href="{{'/'}}">Home</a>
                    <a href="{{ route('login') }}" class="bg-black py-2 px-4 text-base rounded-md hover:text-blue-500">
                    <i class="fa-solid fa-user" style="margin-right: 0.5rem;"></i>Iniciar sesión
                    </a>

                    <a href="{{route('carrito.index')}}" class="hover:text-white/40">
                    <i class="fa-solid fa-cart-shopping text-2xl"></i></a>
                </div>
    </header>
@endif
</div>