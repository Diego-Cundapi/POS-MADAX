<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refacciones ALA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-screen">
    <header class="bg-gray-900 text-white py-4 px-6 md:px-8 flex items-center justify-between">
        <div class="text-2xl font-bold">
            <a href="">Autopartes ALA</a>
        </div>
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
                    <i class="fa-solid fa-cart-shopping text-2xl"></i>
                </a>
                <a href="{{route('vercompras')}}" class="hover:text-white/40 cursor-pointer ml-4">
                    <i class="fa-solid fa-shopping-bag text-2xl"></i>
                </a>
            @endrole
            <a href="{{route('logout')}}" class="hover:text-white/90">Cerrar sesión</a>
        </div>
    </header>

    <main class="bg-gray-900 flex-grow flex justify-center">
        <section class="bg-white rounded-lg shadow-lg w-1/2 p-8 h-auto">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900">Tus Compras</h1>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Pedido</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pedidos as $pedido)
                        <tr class="hover:bg-gray-100">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $pedido->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $pedido->fechapedido }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $pedido->total }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $pedido->estado }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('detallecompra',$pedido->id) }}" class="bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 hover:text-white transition duration-300 ease-in-out">
                                    Ver Detalle
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-center" colspan="5">No hay compras</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
