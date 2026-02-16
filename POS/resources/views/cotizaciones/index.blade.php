@extends('adminlte::page')

@section('title', 'Cotizaciones')

@section('content_header')
<h1>Cotizaciones</h1>
@stop

@section('content')



<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title">Historial de Cotizaciones</h3>
        <div class="card-tools">
            <a href="{{ route('cotizaciones.create') }}" class="btn btn-light btn-sm text-primary font-weight-bold">
                <i class="fas fa-plus"></i> Nueva Cotización
            </a>
        </div>
    </div>

    <div class="card-body">
        <table id="tabla-cotizaciones" class="table table-hover table-striped dt-responsive nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Vendedor</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cotizaciones as $cotizacion)
                <tr>
                    <td>{{ $cotizacion->id }}</td>

                    {{-- Ordenamiento por timestamp oculto --}}
                    <td data-order="{{ $cotizacion->created_at->timestamp }}">
                        {{ $cotizacion->created_at->format('d/m/Y') }}
                    </td>

                    <td>
                        <span class="font-weight-bold">{{ $cotizacion->cliente_nombre }}</span><br>
                        <small class="text-muted">{{ $cotizacion->cliente_email }}</small>
                    </td>

                    <td data-order="{{ $cotizacion->total }}">
                        ${{ number_format($cotizacion->total, 2) }}
                    </td>

                    <td>
                        @if($cotizacion->estado == 'Pendiente')
                        <span class="badge badge-warning">Pendiente</span>
                        @elseif($cotizacion->estado == 'Aceptada')
                        <span class="badge badge-success">Venta Realizada</span>
                        @else
                        <span class="badge badge-danger">Rechazada</span>
                        @endif
                    </td>

                    <td>
                        <i class="fas fa-user-tie text-secondary mr-1"></i>
                        {{ $cotizacion->user->name ?? 'Usuario Eliminado' }}
                    </td>

                    <td class="text-center">
                        @if($cotizacion->estado == 'Pendiente')
                        <a href="{{ route('cotizaciones.edit', ['cotizacionId' => $cotizacion->id]) }}"
                            class="btn btn-sm btn-warning"
                            title="Editar Cotización">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endif

                        {{-- Sin target="_blank" para evitar ventana blanca en NativePHP --}}
                        <a href="{{ route('cotizaciones.pdf', $cotizacion->id) }}" class="btn btn-sm btn-info" title="Descargar PDF" download>
                            <i class="fas fa-file-pdf"></i>
                        </a>

                        {{-- Botón Ticket --}}
                        <button onclick="imprimirTicket('{{ route('ticket.imprimir', ['tipo' => 'cotizacion', 'id' => $cotizacion->id]) }}')" class="btn btn-dark btn-sm" title="Imprimir Ticket">
                            <i class="fas fa-receipt"></i>
                        </button>

                        @if($cotizacion->estado != 'Aceptada')
                        <form action="{{ route('cotizaciones.convertir', $cotizacion->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="button" class="btn btn-sm btn-success" onclick="confirmAction(event, '¿El cliente aceptó? Se creará una venta y se descontará del inventario.', function() { this.closest('form').submit(); }.bind(this))" title="Convertir en Venta">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>

                        <form action="{{ route('cotizaciones.destroy', $cotizacion->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmAction(event, '¿Borrar esta cotización?', function() { this.closest('form').submit(); }.bind(this))" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @else
                        <span class="badge badge-secondary"><i class="fas fa-lock"></i> Cerrada</span>
                        @endif
                    </td>
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop

@section('css')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@stop

@section('js')
    <script>
        // Función global para imprimir ticket
        function imprimirTicket(url) {
            const width = 400;
            const height = 600;
            const left = (window.screen.width / 2) - (width / 2);
            const top = (window.screen.height / 2) - (height / 2);
            window.open(url, 'Ticket', `width=${width},height=${height},top=${top},left=${left},scrollbars=yes`);
        }

        // Función confirmAction para manejar confirmaciones asíncronas
        window.confirmAction = function(event, message, callback) {
            // Prevenir el envío automático del formulario
            event.preventDefault();
            
            Swal.fire({
                title: 'Confirmar',
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: 'No',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed && callback) {
                    callback();
                }
            });
        };

        $(document).ready(function() {
            // Verificar si Swal está cargado
            if (typeof Swal === 'undefined') {
                console.warn('SweetAlert2 no se cargó correctamente. Usando fallback.');
                // Fallback básico si Swal falla
                window.Swal = {
                    fire: function(data) { alert(data.title + '\n' + data.text); return Promise.resolve({isConfirmed: true}); },
                    mixin: function() { return { fire: function(d) { console.log(d); } } }
                };
            }

            $('#tabla-cotizaciones').DataTable({
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

            // SweetAlert Logic
            const successMsg = @json(session('success'));
            const errorMsg = @json(session('error'));
            
            // Si pasas el ID en la sesión desde el controlador (ej. with('created_id', ...)) usarías esto:
            const createdId = @json(session('created_id')); 

            if (successMsg) {
                if(createdId) {
                     // Caso especial: Acabamos de crear/convertir una cotización
                     const urlTicket = "{{ url('/ticket/cotizacion') }}/" + createdId;
                     const urlPDF = "{{ url('/cotizaciones') }}/" + createdId + "/pdf";

                     Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: successMsg,
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-receipt"></i> Ticket',
                        denyButtonText: '<i class="fas fa-file-pdf"></i> PDF',
                        cancelButtonText: 'Cerrar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            imprimirTicket(urlTicket);
                        } else if (result.isDenied) {
                            // Usar location.href en lugar de window.open para evitar ventana blanca
                            window.location.href = urlPDF;
                        }
                    });
                } else {
                    // Mensaje normal
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: successMsg,
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            }

            if (errorMsg) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg,
                });
            }
        });
    </script>
@stop