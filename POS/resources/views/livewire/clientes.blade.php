<div>
    @extends('adminlte::page')

    @section('title', 'Dashboard')

    @section('content_header')
        <h1 class="">Lista de clientes</h1>
    @stop

    @section('content')
        <div>
            <table id="tabla-clientes" class="table table-bordered table-striped table-hover dt-responsive nowrap">
                <thead>
                    <tr>
                        <th class="py-2">Nombre <i class="fas fa-sort text-muted float-right"></i></th>
                        <th class="py-2">Email <i class="fas fa-sort text-muted float-right"></i></th>
                        <th class="py-2">Teléfono <i class="fas fa-sort text-muted float-right"></i></th>
                        <th class="py-2">Dirección <i class="fas fa-sort text-muted float-right"></i></th>
                        <th class="py-2">Última Compra <i class="fas fa-sort text-muted float-right"></i></th>
                        <th class="py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientes as $cliente)
                        <tr>
                            <td class="border px-4 py-2">{{ $cliente['name'] }}</td>
                            <td class="border px-4 py-2">{{ $cliente['email'] }}</td>
                            <td class="border px-4 py-2">{{ $cliente['telefono'] }}</td>
                            <td class="border px-4 py-2">{{ $cliente['direccion'] }}</td>
                            <td class="border px-4 py-2">
                                @if($cliente['ultima_compra'])
                                    {{ $cliente['ultima_compra'] }}
                                @else
                                    <span class="text-muted small">Sin compras</span>
                                @endif
                            </td>
                            <td class="border px-4 py-2 text-center">
                                @if($cliente['ultimo_pedido_id'])
                                    <a href="{{ route('ventas.edit', $cliente['ultimo_pedido_id']) }}" class="btn btn-primary btn-sm" title="Ver detalle de la venta">
                                        <i class="fas fa-shopping-cart"></i> Ver Última Compra
                                    </a>
                                @else
                                    <button class="btn btn-secondary btn-sm" disabled>Sin Compras</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @stop

    @section('preloader')
        <i class="fas fa-4x fa-spin fa-spinner text-secondary"></i>
        <h4 class="mt-4 text-dark">Cargando</h4>
    @stop

    @section('css')
        {{-- DataTables Core & BS4 --}}
        <link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}">
        {{-- DataTables Responsive --}}
        <link rel="stylesheet" href="{{ asset('vendor/datatables-plugins/responsive/css/responsive.bootstrap4.min.css') }}">
    @stop

    @section('js')
        {{-- DataTables JS --}}
        <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
        {{-- DataTables Responsive JS --}}
        <script src="{{ asset('vendor/datatables-plugins/responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('vendor/datatables-plugins/responsive/js/responsive.bootstrap4.min.js') }}"></script>
    
        <script>
            $(document).ready(function() {
                $('#tabla-clientes').DataTable({
                    "ordering": true,
                    "responsive": true,
                    "autoWidth": false,
                    "order": [
                        [0, "desc"]
                    ],
                    "language": {
                        "lengthMenu": "Mostrar _MENU_ registros por página",
                        "zeroRecords": "No se encontraron resultados - lo siento",
                        "info": "Mostrando página _PAGE_ de _PAGES_",
                        "infoEmpty": "No hay registros disponibles",
                        "infoFiltered": "(filtrado de _MAX_ registros totales)",
                        "search": "Buscar:",
                        "paginate": {
                            "first": "Primero",
                            "last": "Último",
                            "next": "Siguiente",
                            "previous": "Anterior"
                        },
                        "loadingRecords": "Cargando...",
                        "processing": "Procesando...",
                        "emptyTable": "No hay datos disponibles en la tabla"
                    }
                });
            });
        </script>
    @stop
</div>
