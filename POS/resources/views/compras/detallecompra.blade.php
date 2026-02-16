<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refacciones ALA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header class="bg-gray-900 text-white py-4 px-6 md:px-8 flex items-center justify-between">
        <div class="text-2xl font-bold">
            <a href="">Autopartes ALA</a>
        </div>
            <!-- <div class="">
                <img src="{{ asset('imagenes/logo.jpg') }}" class="h-10" alt="Logo">
            </div> -->
            <div class="flex items-center space-x-4">
                <span class="text-white">¡Bienvenido, {{ Auth::user()->name }}!</span>

                <a class="hover:text-white/40" href="{{'/'}}">Home</a>
                @can('dashboard')
                    <a href="{{ route('dashboard') }}" class="bg-black py-2 px-4 text-base rounded-md hover:text-blue-500">
                    <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                @endcan
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

    <main class="bg-gray-900 h-screen flex items-center justify-center">
    <section class="flex flex-col items-center justify-center bg-white rounded-lg shadow-lg w-1/2 p-8">
            <div class="col-sm-8">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <h1 class="text-2xl font-bold">Pedido N°: {{$pedido->id}} {{strtoupper($pedido->estado)}}</h1>
                            </div>
                        </div>
                    </div>

                    <table class="table-auto w-full text-black mb-8 text-center">
                        <tbody>
                            <tr>
                                <td class="border px-4 py-2 font-semibold">Estado:</td>
                                <td class="border px-4 py-2">{{$pedido->user->estado}}</td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2 font-semibold">Ciudad:</td>
                                <td class="border px-4 py-2">{{$pedido->user->ciudad}}</td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2 font-semibold">Dirección:</td>
                                <td class="border px-4 py-2">{{$pedido->user->direccion}}</td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2 font-semibold">Código Postal:</td>
                                <td class="border px-4 py-2">{{$pedido->user->codigo_postal}}</td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2 font-semibold">Fecha Pedido:</td>
                                <td class="border px-4 py-2">{{$pedido->fechapedido}}</td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="table-auto w-full text-black text-center">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">Producto</th>
                                <th class="border px-4 py-2">Cantidad</th>
                                <th class="border px-4 py-2">Precio</th>
                                <th class="border px-4 py-2">Importe</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pedido->detalles as $item)
                            <tr class="border-b">
                                <td class="border px-4 py-2">{{$item->producto->nombre}}</td>
                                <td class="border px-4 py-2">{{$item->cantidad}}</td>
                                <td class="border px-4 py-2">{{$item->precio}}</td>
                                <td class="border px-4 py-2">{{$item->importe}}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center border px-4 py-2">No hay detalles del pedido</td>
                            </tr>
                            @endforelse

                            <tr>
                                <td colspan="3" class="text-right border px-4 py-2">SubTotal: </td>
                                <td class="border px-4 py-2">{{$pedido->subtotal}}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right border px-4 py-2">Impuesto: </td>
                                <td class="border px-4 py-2">{{$pedido->impuesto}}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right border px-4 py-2">Total</th>
                                <th class="border px-4 py-2">{{$pedido->total}}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <a href="{{ route('vercompras') }}" class="w-64 text-center mt-6 px-6 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75">
                        Regresar
                    </a>
        </section>
    </main>


    <footer class="bg-gray-900 text-white py-6 px-6 md:px-8">
        <div class="container mx-auto flex flex-col md:flex-row items-center justify-between">
            <p>&copy; 2024 Autopartes ALA. Todos los derechos reservados.</p>
            <nav class="flex items-center space-x-6 mt-4 md:mt-0">
                <a href="#" class="hover:text-gray-700">Terminos de servicio</a>
                <a href="#" class="hover:text-gray-700">Politicas de privacidad</a>
                <a href="#" class="hover:text-gray-700">Contactanos</a>
            </nav>
        </div>
    </footer>
</body>
</html>