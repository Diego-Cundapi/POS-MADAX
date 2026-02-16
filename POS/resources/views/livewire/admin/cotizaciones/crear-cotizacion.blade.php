<div>
    <div class="card">
        <div class="card-header">
            <h1><i class="fas fa-file-invoice-dollar mr-2"></i> {{ $modoEdicion ? 'Editar Cotización' : 'Nueva Cotización' }}</h1>
        </div>
        <div class="card-body">
            
            {{-- ALERTA DE ERROR SERVER-SIDE (Funciona Offline/Sin JS) --}}
            @if(session('error_stock'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5><i class="icon fas fa-ban"></i> ¡No se pudo convertir la venta!</h5>
                    <div>{!! session('error_stock') !!}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row mb-4">
                {{-- SELECCIONAR CLIENTE --}}
                <div class="col-md-6">
                    <label>Cliente:</label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               placeholder="Buscar cliente (nombre o email)..."
                               wire:model.debounce.500ms="buscarCliente"
                               @if($cliente_id) disabled @endif>
                        
                        @if($cliente_id)
                            <div class="input-group-append">
                                <button class="btn btn-danger" wire:click="resetClienteId">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif
                    </div>
    
                    {{-- Lista de resultados clientes --}}
                    @if(strlen($buscarCliente) > 0 && !$cliente_id)
                        <div class="list-group position-absolute w-100" style="z-index: 999;">
                            @forelse($resultadosClientes as $cliente)
                                <a href="#" class="list-group-item list-group-item-action"
                                   wire:click.prevent="seleccionarCliente({{ $cliente->id }})">
                                    {{ $cliente->name }} <small class="text-muted">({{ $cliente->email }})</small>
                                </a>
                            @empty
                                <div class="list-group-item">No se encontraron clientes.</div>
                            @endforelse
                        </div>
                    @endif
                </div>
    
                <div class="col-md-6 text-right">
                    <h3>Fecha: <small>{{ $fecha }}</small></h3>
                </div>
            </div>
    
            <hr>
    
            {{-- BUSCADOR PRODUCTOS --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <label>Clave:</label>
                    <div class="position-relative">
                        <input type="text" 
                               class="form-control" 
                               wire:model.debounce.300ms="buscarClave" 
                               wire:keydown.enter="enterClave" 
                               placeholder="Buscar clave...">
                        
                        {{-- Dropdown Clave --}}
                        @if(count($resultadosClave) > 0)
                            <div class="list-group position-absolute w-100 shadow-sm list-group-scroll" style="z-index: 1050; top: 100%;">
                                @foreach($resultadosClave as $res)
                                    <a href="#" class="list-group-item list-group-item-action p-2"
                                       wire:click.prevent="seleccionarProducto({{ $res->id }})">
                                        <small class="font-weight-bold">{{ $res->clave }}</small> - <small>{{ $res->nombre }}</small>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
    
                <div class="col-md-6">
                    <label>Descripción / Nombre:</label>
                    <div class="position-relative">
                        <input type="text" 
                               class="form-control" 
                               wire:model.debounce.300ms="buscarNombre" 
                               wire:keydown.enter="enterNombre"
                               placeholder="Buscar producto...">
                        
                        {{-- Dropdown Nombre con Precio --}}
                        @if(count($resultadosNombre) > 0)
                            <div class="list-group position-absolute w-100 shadow-sm list-group-scroll" style="z-index: 1050; top: 100%;">
                                @foreach($resultadosNombre as $res)
                                    <a href="#" class="list-group-item list-group-item-action p-2 d-flex justify-content-between align-items-center"
                                       wire:click.prevent="seleccionarProducto({{ $res->id }})">
                                        <span>{{ $res->nombre }}</span>
                                        <span class="badge badge-primary badge-pill">${{ number_format($res->precio, 2) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
    
            {{-- TABLA DETALLES --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Clave</th>
                            <th>Descripción</th>
                            <th>Ubicación</th>
                            <th width="100px">Cant.</th>
                            <th width="120px">Precio</th>
                            <th width="120px">Importe</th>
                            <th width="50px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $index => $item)
                            <tr>
                                <td>{{ $item['clave'] }}</td>
                                <td>{{ $item['descripcion'] }}</td>
                                <td>{{ $item['ubicacion'] ?? 'N/A' }}</td>
                                <td>
                                    <input type="number" 
                                           class="form-control form-control-sm" 
                                           min="1" 
                                           value="{{ $item['cantidad'] }}"
                                           wire:change="actualizarCantidad({{ $index }}, $event.target.value)">
                                </td>
                                <td>
                                    <input type="number" 
                                           step="0.01"
                                           class="form-control form-control-sm" 
                                           value="{{ $item['precio'] }}"
                                           wire:change="actualizarPrecio({{ $index }}, $event.target.value)">
                                </td>
                                <td>${{ number_format($item['importe'], 2) }}</td>
                                <td>
                                    <button class="btn btn-danger btn-sm"
                                            wire:click="eliminarItem({{ $index }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Aún no has agregado productos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-right"><strong>Subtotal:</strong></td>
                            <td colspan="2"><strong>${{ number_format($subtotal, 2) }}</strong></td>
                        </tr>
                        {{-- IVA REMOVIDO --}}
                        <tr class="bg-light">
                            <td colspan="5" class="text-right"><h4>Total:</h4></td>
                            <td colspan="2"><h4 class="text-primary">${{ number_format($total, 2) }}</h4></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
    
            <div class="row mt-4">
                <div class="col-12 text-right">
                    <a href="{{ route('cotizaciones.index') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button class="btn btn-success btn-lg" wire:click="guardarCotizacion">
                        <i class="fas fa-save"></i> Guardar Cotización
                    </button>
                </div>
            </div>
    
        </div>
    </div>

    {{-- SCRIPTS DENTRO DEL COMPONENTE (SweetAlert2 se carga globalmente via Vite) --}}
    <script>
    // Función global para imprimir ticket en ventana popup
    function imprimirTicket(url) {
        const width = 400;
        const height = 600;
        const left = (window.screen.width / 2) - (width / 2);
        const top = (window.screen.height / 2) - (height / 2);
        window.open(url, 'Ticket', `width=${width},height=${height},top=${top},left=${left},scrollbars=yes`);
    }

    // ESCUCHAR EVENTO DE ÉXITO DESDE LIVEWIRE
    window.addEventListener('abrir-modal-exito', event => {
        const id = event.detail.id;
        
        // URLs dinámicas
        const baseUrl = "{{ url('/cotizaciones') }}";
        const urlPDF = `${baseUrl}/${id}/pdf`;
        const urlTicket = "{{ url('/ticket/cotizacion') }}/" + id;
        const urlIndex = "{{ route('cotizaciones.index') }}";
        const urlNew = "{{ route('cotizaciones.create') }}";

        // Verificar que Swal esté disponible (cargado via Vite)
        if (typeof Swal === 'undefined') {
            alert('¡Cotización Guardada! ID: ' + id);
            window.location.href = urlIndex;
            return;
        }

        Swal.fire({
            icon: 'success',
            title: '¡Cotización Guardada!',
            html: `
                <h3 class="text-primary">Cotización #${id}</h3>
                <p>La cotización se ha registrado correctamente.</p>
                <div class="d-flex justify-content-center flex-wrap mt-4" style="gap: 10px;">
                    <button onclick="imprimirTicket('${urlTicket}')" class="btn btn-dark">
                        <i class="fas fa-receipt"></i> Imprimir Ticket
                    </button>
                    <a href="${urlPDF}" class="btn btn-info" download>
                        <i class="fas fa-file-pdf"></i> Ver PDF
                    </a>
                </div>
                <div class="mt-3">
                    <a href="${urlNew}" class="btn btn-primary btn-sm" onclick="window.location.reload(); return false;">
                        <i class="fas fa-plus"></i> Nueva Cotización
                    </a>
                    <a href="${urlIndex}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-list"></i> Salir
                    </a>
                </div>
            `,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false
        });
    });

    // Actualizar URL sin recargar página
    window.addEventListener('history-push-state', event => {
        window.history.pushState({}, '', event.detail.url);
    });

    // VERIFICAR ERRORES DE STOCK AL CARGAR (Flash Session)
    document.addEventListener('DOMContentLoaded', function() {
        const errorStock = @json(session('error_stock'));
        
        if (errorStock && typeof Swal !== 'undefined') {
            setTimeout(() => {
                Swal.fire({
                    icon: 'warning',
                    title: '¡No se pudo convertir!',
                    html: errorStock,
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#d33',
                    backdrop: `rgba(0,0,123,0.4)`
                });
            }, 500);
        }
    });
    </script>

    <style>
        /* Estilos para las listas desplegables con scroll vertical */
        .list-group-scroll {
            max-height: 300px;
            overflow-y: auto;
        }
        
        /* Estilos para mejorar la apariencia del scroll */
        .list-group-scroll::-webkit-scrollbar {
            width: 8px;
        }
        
        .list-group-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .list-group-scroll::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        .list-group-scroll::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</div>