@extends('adminlte::page')

@section('title', 'Editar Venta')

@section('content_header')
    <h1><i class="fas fa-edit mr-2"></i> Administrar Pedido #{{ $pedido->id }}</h1>
@stop

@section('content')

{{-- Alertas --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong><i class="fas fa-check-circle"></i></strong> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif

<div class="row">
    {{-- COLUMNA IZQUIERDA: DETALLES VENTA --}}
    <div class="col-md-8">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title">Detalles de los Productos</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Cant.</th>
                            <th class="text-right">Precio</th>
                            <th class="text-right">Importe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pedido->detalles as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->producto->nombre ?? 'Producto Eliminado' }}</strong><br>
                                <small class="text-muted">{{ $item->producto->clave ?? 'S/C' }}</small>
                            </td>
                            <td class="text-center">{{ $item->cantidad }}</td>
                            <td class="text-right">${{ number_format($item->precio, 2) }}</td>
                            <td class="text-right">${{ number_format($item->importe, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No hay productos en este pedido.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                            <td class="text-right">${{ number_format($pedido->subtotal, 2) }}</td>
                        </tr>
                        @if($pedido->descuento > 0)
                        <tr>
                            <td colspan="3" class="text-right"><strong>Descuento:</strong></td>
                            <td class="text-right text-danger">- ${{ number_format($pedido->descuento, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="bg-light">
                            <td colspan="3" class="text-right"><h5 class="mb-0">Total:</h5></td>
                            <td class="text-right"><h5 class="mb-0 text-primary">${{ number_format($pedido->total, 2) }}</h5></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
                {{-- Sin target="_blank" para evitar ventana blanca en NativePHP --}}
                <a href="{{ route('ventas.pdf', $pedido->id) }}" class="btn btn-info btn-sm" download>
                    <i class="fas fa-print"></i> Descargar PDF
                </a>
            </div>
        </div>
    </div>

    {{-- COLUMNA DERECHA: INFO CLIENTE Y ESTADO --}}
    <div class="col-md-4">
        
        {{-- CARD CLIENTE --}}
        <div class="card card-info card-outline shadow-sm mb-3">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user mr-1"></i> Cliente</h3>
            </div>
            <div class="card-body">
                <h5 class="text-info">{{ $pedido->user->name ?? 'Usuario Eliminado' }}</h5>
                <ul class="list-unstyled mt-3">
                    <li class="mb-2"><i class="fas fa-envelope w-25 text-center text-muted"></i> {{ $pedido->user->email ?? 'N/A' }}</li>
                    <li class="mb-2"><i class="fas fa-phone w-25 text-center text-muted"></i> {{ $pedido->user->telefono ?? 'N/A' }}</li>
                    <li class="mb-2"><i class="fas fa-map-marker-alt w-25 text-center text-muted"></i> {{ $pedido->user->ciudad ?? 'N/A' }}</li>
                </ul>
            </div>
        </div>

        {{-- CARD ESTADO --}}
        <div class="card card-warning card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-sync-alt mr-1"></i> Actualizar Estado</h3>
            </div>
            <div class="card-body">
                {!! Form::open(['route'=>['ventas.update', $pedido], 'method'=>'PUT']) !!}
                    
                    <div class="form-group">
                        <label>Estado del Pedido:</label>
                        @php
                            $colorClass = match($pedido->estado) {
                                'Nuevo' => 'badge-info',
                                'Proceso' => 'badge-warning',
                                'Entregado' => 'badge-success',
                                default => 'badge-secondary'
                            };
                        @endphp
                        <div class="mb-2">
                            Actual: <span class="badge {{ $colorClass }}">{{ $pedido->estado }}</span>
                        </div>

                        {!! Form::select('estado', 
                            ["Nuevo"=>"Nuevo", "Proceso"=>"En Proceso", "Entregado"=>"Entregado / Finalizado"], 
                            $pedido->estado, 
                            ['class'=>'form-control custom-select']) 
                        !!}
                    </div>

                    <button type="submit" class="btn btn-success btn-block font-weight-bold">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    
                {!! Form::close() !!}
            </div>
        </div>
        
        <div class="mt-3 text-center">
            <a href="{{ route('ventas.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
        </div>

    </div>
</div>

@stop

@section('css')
@stop

@section('js')
@stop
