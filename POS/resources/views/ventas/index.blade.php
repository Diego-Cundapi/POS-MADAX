@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<h1>Panel de Ventas</h1>
@stop

@section('content')

<div class="row justify-content-center">
    <div class="col-sm-10">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h3 class="card-title">Listado de Ventas</h3>
                <a href="{{ route('ventas.create') }}" class="btn btn-success btn-sm border shadow-sm">
                    <i class="fas fa-plus"></i> Nueva Venta
                </a>
            </div>
            <div class="card-body table-responsive p-0">
                <table id="tabla-ventas" class="table table-bordered table-striped table-hover dt-responsive nowrap">
                    <thead>
                        <tr>
                            <th>ID <i class="fas fa-sort text-muted float-right"></i></th>
                            <th>Cliente <i class="fas fa-sort text-muted float-right"></i></th>
                            <th>Vendedor <i class="fas fa-sort text-muted float-right"></i></th> {{-- NUEVA COLUMNA --}}
                            <th>Fecha Pedido <i class="fas fa-sort text-muted float-right"></i></th>
                            <th>Total <i class="fas fa-sort text-muted float-right"></i></th>
                            <th>Estado <i class="fas fa-sort text-muted float-right"></i></th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pedidos as $pedido)
                        <tr>
                            <td>{{$pedido->id}}</td>

                            {{-- Columna Cliente --}}
                            <td>
                                <i class="fas fa-user text-muted mr-1"></i> {{ $pedido->user->name ?? 'Usuario Eliminado' }}
                            </td>

                            {{-- Columna Vendedor (NUEVA LÓGICA) --}}
                            <td>
                                @if($pedido->vendedor)
                                <span class="badge badge-light border">
                                    <i class="fas fa-user-tie text-primary mr-1"></i> 
                                    {{ $pedido->vendedor->name }}
                                    @if($pedido->vendedor->trashed())
                                        <small class="text-danger">(Eliminado)</small>
                                    @endif
                                </span>
                                @else
                                <span class="text-muted small font-italic">Sistema/Web</span>
                                @endif
                            </td>

                            <td>{{$pedido->fechapedido}}</td>
                            <td>${{ number_format($pedido->total, 2) }}</td>
                            <td>
                                {{-- Pequeño detalle visual para los estados --}}
                                @if($pedido->estado == 'Nuevo')
                                <span class="badge badge-info">{{$pedido->estado}}</span>
                                @elseif($pedido->estado == 'Proceso')
                                <span class="badge badge-warning">{{$pedido->estado}}</span>
                                @elseif($pedido->estado == 'Entregado')
                                <span class="badge badge-success">{{$pedido->estado}}</span>
                                @else
                                {{$pedido->estado}}
                                @endif
                            </td>
                            <td>
                                <a href="{{route('ventas.edit', $pedido->id)}}" class="btn btn-sm btn-success">
                                    <i class="fas fa-eye"></i> Ver Detalle
                                </a>
                                {{-- Botón Ticket --}}
                                <button onclick="imprimirTicket('{{ route('ticket.imprimir', ['tipo' => 'venta', 'id' => $pedido->id]) }}')" class="btn btn-dark btn-sm" title="Imprimir Ticket">
                                    <i class="fas fa-receipt"></i>
                                </button>
                                <a href="{{ route('ventas.pdf', $pedido->id) }}" class="btn btn-danger btn-sm">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            {{-- IMPORTANTE: Aumentamos el colspan a 7 por la nueva columna --}}
                            <td colspan="7" class="text-center text-muted">
                                <i class="fas fa-inbox fa-2x mb-3 mt-3"></i><br>
                                No hay ventas registradas en el sistema.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@stop

@section('preloader')
<i class="fas fa-4x fa-spin fa-spinner text-secondary"></i>
<h4 class="mt-4 text-dark">Cargando</h4>
@stop

@section('css')
    {{-- Cargamos los estilos y scripts compilados de Laravel --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
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
        // Función global para imprimir ticket
        function imprimirTicket(url) {
            const width = 400;
            const height = 600;
            const left = (window.screen.width / 2) - (width / 2);
            const top = (window.screen.height / 2) - (height / 2);
            window.open(url, 'Ticket', `width=${width},height=${height},top=${top},left=${left},scrollbars=yes`);
        }

        $(document).ready(function() {
            $('#tabla-ventas').DataTable({
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

    <script>
        console.log("Panel de ventas cargado.");
    
        // --- PASO 1: Capturamos los datos de PHP al inicio ---
        const mensajeError = @json(session('error_modal'));
        const mensajeExito = @json(session('success'));
    
        const mostrarAlerta = () => {
            // Esperar a que Swal cargue
            if (typeof Swal === 'undefined') {
                setTimeout(mostrarAlerta, 100);
                return;
            }
    
            // --- PASO 2: Usamos las variables JS limpias ---
    
            // ALERTA DE ERROR (MODAL)
            if (mensajeError) {
                Swal.fire({
                    icon: 'error',
                    title: '¡Venta no encontrada!',
                    text: mensajeError,
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#d33',
                    backdrop: `rgba(0,0,123,0.4)`
                });
            }
    
            // ALERTA DE ÉXITO (TOAST)
            if (mensajeExito) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
    
                Toast.fire({
                    icon: 'success',
                    title: mensajeExito
                });
            }
        };
    
        mostrarAlerta();
    </script>
@stop