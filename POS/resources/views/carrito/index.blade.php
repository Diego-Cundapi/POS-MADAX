<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Carrito de Compras') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        @if (session('success'))
        <div class="bg-green-500 text-white p-4 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        {{-- Cambié el texto a gris oscuro para que se vea sobre fondo blanco, 
             si tu fondo es oscuro puedes regresarlo a text-white --}}
        <h1 class="text-4xl text-center text-gray-800 font-semibold mb-6">Resumen de tu Pedido</h1>

        <div class="flex justify-center">
            <table class="table-auto w-3/5 rounded bg-white shadow-md">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-4 border-b border-gray-300">Producto</th>
                        <th class="py-2 px-4 border-b border-gray-300">Nombre</th>
                        <th class="py-2 px-4 border-b border-gray-300">Precio</th>
                        <th class="py-2 px-4 border-b border-gray-300">Cantidad</th>
                        <th class="py-2 px-4 border-b border-gray-300">Importe</th>
                        <th class="py-2 px-4 border-b border-gray-300">Acción</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @forelse(Cart::content() as $item)
                    <tr class="hover:bg-gray-100">
                        <td class="py-2 px-4 border-b border-gray-300">
                            <img src="{{ asset($item->options->imagen) }}" alt="Imagen" class="mx-auto w-16 h-16 object-cover rounded">
                        </td>
                        <td class="py-2 px-4 border-b border-gray-300">{{ $item->name }}</td>
                        <td class="py-2 px-4 border-b border-gray-300">${{ number_format($item->price, 2) }}</td>
                        <td class="py-2 px-4 border-b border-gray-300">
                            <div class="inline-flex rounded-md shadow-sm" role="group">
                                <a href="{{ route('carrito.decrementarproducto', $item->rowId) }}" class="bg-red-500 text-white px-3 py-1 rounded-l hover:bg-red-700 transition">-</a>
                                <span class="bg-gray-100 px-3 py-1 border-t border-b border-gray-300">{{ $item->qty }}</span>
                                <a href="{{ route('carrito.incrementarproducto', $item->rowId) }}" class="bg-green-500 text-white px-3 py-1 rounded-r hover:bg-green-700 transition">+</a>
                            </div>
                        </td>
                        <td class="py-2 px-4 border-b border-gray-300 font-bold text-gray-700">
                            ${{ number_format($item->qty * $item->price, 2) }}
                        </td>
                        <td class="py-2 px-4 border-b border-gray-300">
                            <a href="{{ route('carrito.eliminaritem', $item->rowId) }}" class="text-red-500 hover:text-red-700 font-bold">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td class="py-4 px-4 text-center text-gray-500" colspan="6">
                            Tu carrito está vacío.
                        </td>
                    </tr>
                    @endforelse

                    @if(Cart::count() >= 1)
                    <tr class="bg-gray-50 font-bold text-gray-700">
                        <td colspan="4" class="text-right py-2 px-4">Subtotal:</td>
                        <td class="text-center py-2 px-4">${{ Cart::subtotal() }}</td>
                        <td></td>
                    </tr>
                    <tr class="bg-gray-50 font-bold text-gray-700">
                        <td colspan="4" class="text-right py-2 px-4">Impuesto:</td>
                        <td class="text-center py-2 px-4">${{ Cart::tax() }}</td>
                        <td></td>
                    </tr>
                    <tr class="bg-gray-200 text-xl font-bold text-gray-900">
                        <td colspan="4" class="text-right py-3 px-4">TOTAL:</td>
                        <td class="text-center py-3 px-4">${{ Cart::total() }}</td>
                        <td></td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="flex justify-center mt-8 space-x-4">
            <a href="{{ route('index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded transition">
                Seguir Comprando
            </a>

            @if(Cart::count() >= 1)
            @auth
            <a href="{{ route('carrito.confirmarcarrito') }}" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-6 rounded shadow transition">
                Realizar Compra
            </a>
            {{-- NUEVO: El botón de Cotizar solo aparece si tienes permiso de 'dashboard' (Admin) --}}
            @can('dashboard')
            <form action="{{ route('cotizaciones.store') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-gray-600 hover:bg-gray-800 text-white font-bold py-2 px-6 rounded shadow transition ml-2">
                    <i class="fas fa-save"></i> Guardar como Cotización
                </button>
            </form>
            @endcan
            <a href="{{ route('carrito.eliminarcarrito') }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-6 rounded shadow transition">
                Vaciar Carrito
            </a>
            @else
            <a href="{{ route('login') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-6 rounded shadow transition">
                Inicia sesión para pagar
            </a>
            @endauth
            @endif
        </div>
    </div>
</x-app-layout>