<div>
    <div class="card">
        <div class="card-header bg-success text-white">
            <h1><i class="fas fa-cash-register mr-2"></i> Nueva Venta</h1>
        </div>
        <div class="card-body">
     
            <div class="row mb-4">
                {{-- SELECCIONAR CLIENTE --}}
                <div class="col-md-6">
                    <label>Cliente (Opcional):</label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               placeholder="Buscar cliente... (Dejar vacío para Público en General)"
                               wire:model.debounce.500ms="buscarCliente"
                               @if($cliente_id) disabled @endif>
                        
                        @if($cliente_id)
                            <div class="input-group-append">
                                <button class="btn btn-danger" wire:click="resetClienteId">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @else
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-users"></i></span>
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
                    <small class="text-muted">Si no selecciona ninguno, se registrará como "Cliente General".</small>
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
                    <thead class="bg-success text-white">
                        <tr>
                            <th>Clave</th>
                            <th>Descripción</th>
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
                                <td>
                                    <input type="number" 
                                           class="form-control form-control-sm" 
                                           min="1" 
                                           value="{{ $item['cantidad'] }}"
                                           wire:change="actualizarCantidad({{ $index }}, $event.target.value)">
                                </td>
                                <td>${{ number_format($item['precio'], 2) }}</td>
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
                                <td colspan="6" class="text-center text-muted">Aún no se han escaneado productos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                            <td colspan="2"><strong>${{ number_format($subtotal, 2) }}</strong></td>
                        </tr>
                        {{-- IVA REMOVIDO --}}
                        <tr>
                            <td colspan="4" class="text-right"><strong>Descuento (%):</strong></td>
                            <td colspan="2">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <input type="number" step="1" min="0" max="100" class="form-control" wire:model.lazy="descuentoPorcentaje">
                                </div>
                                <small class="text-muted d-block text-right mt-1">
                                    - ${{ number_format($descuento, 2) }}
                                </small>
                            </td>
                        </tr>
                        <tr class="bg-light">
                            <td colspan="4" class="text-right"><h4>Total a Pagar:</h4></td>
                            <td colspan="2"><h4 class="text-success">${{ number_format($total, 2) }}</h4></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
    
            <div class="row mt-4">
                <div class="col-12 text-right">
                    <a href="{{ route('ventas.index') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button class="btn btn-success btn-lg" wire:click="guardarVenta">
                        <i class="fas fa-check-circle"></i> Completar Venta
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

    window.addEventListener('abrir-modal-exito', event => {
        const id = event.detail.id;
        const baseUrl = "{{ url('/dashboard/pedidos') }}"; 
        
        const urlPDF = "{{ route('ventas.pdf', '__ID__') }}".replace('__ID__', id);
        const urlTicket = "{{ route('ticket.imprimir', ['tipo' => 'venta', 'id' => '__ID__']) }}".replace('__ID__', id);
        const urlIndex = "{{ route('ventas.index') }}";
        const urlNew = "{{ route('ventas.create') }}";

        // Verificar que Swal esté disponible (cargado via Vite)
        if (typeof Swal === 'undefined') {
            alert('¡Venta Registrada! ID: ' + id);
            window.location.href = urlIndex;
            return;
        }

        Swal.fire({
            icon: 'success',
            title: '¡Venta Registrada!',
            html: `
                <h3 class="text-success">Venta #${id}</h3>
                <p>El pedido se ha generado correctamente.</p>
                <div class="d-flex justify-content-center flex-wrap mt-4" style="gap: 10px;">
                    <button onclick="imprimirTicket('${urlTicket}')" class="btn btn-dark">
                        <i class="fas fa-receipt"></i> Imprimir Ticket
                    </button>
                    <a href="${urlPDF}" class="btn btn-info" download>
                        <i class="fas fa-print"></i> PDF
                    </a>
                </div>
                <div class="mt-3">
                    <a href="${urlNew}" class="btn btn-primary btn-sm" onclick="window.location.reload(); return false;">
                        <i class="fas fa-plus"></i> Nueva Venta
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
