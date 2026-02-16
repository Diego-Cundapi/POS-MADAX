<div>
    @extends('adminlte::page')

    @section('title', 'Tablero')

    @section('content_header')
        <h1 class="">Panel de Administración</h1>
    @stop

    @section('content')

    <!-- seccion para mostrar las ventas, nuevos clientes y productos vendidos -->
    <section>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="shadow-md rounded-lg p-6" style="background-color: #DBEAFF;">
                <div class="flex justify-between items-center mb-2">
                    <h1 class="text-lg text-bold">Ventas Totales</h1>
                    <i class="fas fa-dollar-sign text-xl text-bold"></i>
                </div>
                <p class="text-4xl text-black text-bold">${{$ventas}}</p>
            </div>
            <div class="shadow-md rounded-lg p-6" style="background-color: #DCFCE7">
                <div class="flex justify-between items-center mb-2">
                    <h1 class="text-lg text-bold">Nuevos Clientes</h1>
                    <i class="fas fa-user-plus text-xl text-bold"></i>
                </div>
                <p class="text-4xl text-black text-bold mb-1">{{$nuevosClientes}}</p>
                <div class="flex items-center gap-2">
                    <p class="text-sm text-green-900 text-bold">%{{$crecimientoClientes}}</p>
                    <p class="text-sm text-green-900"> de crecimiento de Clientes</p>
                </div>
            </div>
            <div class="shadow-md rounded-lg p-6" style="background-color: #FEF9C2">
                <div class="flex justify-between items-center mb-2">
                    <h1 class="text-lg text-bold">Productos Vendidos</h1>
                    <i class="fas fa-box text-xl text-bold"></i>
                </div>
                <p class="text-4xl text-black text-bold mb-1">{{$productosVendidos}}</p>
                <div class="flex items-center gap-2">
                    <p class="text-sm text-yellow-900 text-bold">%{{$crecimientoProductos}}</p>
                    <p class="text-sm text-yellow-900"> de crecimiento de Productos</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Seccion para mostrar el grafico de barras -->
    <section>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white shadow-md rounded-lg p-6 mt-6">
                <h1 class="text-lg text-bold mb-4 text-center">Ventas de los ultimos 6 meses</h1>
                <canvas id="ventasChart"></canvas>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6 mt-6">
                <div class="overflow-y-auto" style="max-height: 300px;">
                    <h1 class="text-lg text-bold mb-4 text-center">productos mas vendidos</h1>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black text-bold uppercase tracking-wider font-bold">Producto</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black text-bold uppercase tracking-wider font-bold text-center">Ventas</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black text-bold uppercase tracking-wider font-bold text-center">Ingresos</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($productosMasVendidos as $index => $producto)
                                <tr class="{{ $index % 2 == 0 ? 'bg-gray-100' : 'bg-white' }}">
                                    <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-black">{{$producto->nombre}}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-black text-center">{{$producto->cantidad}}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-black text-center">{{$producto->total_generado}}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-3 whitespace-nowrap text-sm font-medium text-black text-center">No hay productos vendidos</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    @stop

    @section('preloader')
        <i class="fas fa-4x fa-spin fa-spinner text-secondary"></i>
        <h4 class="mt-4 text-dark">Cargando</h4>
    @stop

    @section('css')
        {{-- Add here extra stylesheets --}}
        {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    @stop

    @section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('ventasChart').getContext('2d');
            var ventasChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_keys($ventasUltimos6Meses)) !!},
                    datasets: [{
                        label: 'Ventas',
                        data: {!! json_encode(array_values($ventasUltimos6Meses)) !!},
                        backgroundColor: 'rgba(48, 220, 224, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
    @stop
</div>
