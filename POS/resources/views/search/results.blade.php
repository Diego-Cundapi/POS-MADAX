@extends('adminlte::page')

@section('title', 'Resultados de Búsqueda')

@section('content_header')
<h1>Resultados para: <small class="text-muted">"{{ $query }}"</small></h1>
@stop

@section('content')

<div class="row">

    {{-- RESULTADOS DE PRODUCTOS --}}
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-box-open mr-1"></i> Productos Encontrados ({{ $productos->count() }})</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body p-0">
                @if($productos->count() > 0)
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Marca/Modelo</th>
                            <th>Precio</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productos as $producto)
                        <tr>
                            <td>
                                @if($producto->imagen)
                                <img src="{{ asset($producto->imagen) }}" width="40" class="img-circle">
                                @else
                                <i class="fas fa-image text-muted"></i>
                                @endif
                            </td>
                            <td>{{ $producto->nombre }} <br> <small class="text-muted">SKU: {{ $producto->clave }}</small></td>
                            <td>{{ $producto->marca }} - {{ $producto->modelo }}</td>
                            <td class="text-success font-weight-bold">${{ number_format($producto->precio, 2) }}</td>
                            <td>
                                <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="p-3 text-center text-muted">No se encontraron productos.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- RESULTADOS DE VENTAS --}}
    <div class="col-md-6">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-shopping-cart mr-1"></i> Ventas/Pedidos ({{ $pedidos->count() }})</h3>
            </div>
            <div class="card-body p-0">
                @if($pedidos->count() > 0)
                <ul class="list-group list-group-flush">
                    @foreach($pedidos as $pedido)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Pedido #{{ $pedido->id }}</strong><br>
                            <small class="text-muted">Cliente: {{ $pedido->user->name }}</small>
                        </div>
                        <div>
                            <span class="badge badge-primary">${{ number_format($pedido->total, 2) }}</span>
                            <a href="{{ route('ventas.edit', $pedido->id) }}" class="btn btn-xs btn-default ml-2"><i class="fas fa-search"></i></a>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="p-3 text-center text-muted">No se encontraron ventas.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- RESULTADOS DE CATEGORÍAS --}}
    <div class="col-md-6">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tags mr-1"></i> Categorías ({{ $categorias->count() }})</h3>
            </div>
            <div class="card-body">
                @if($categorias->count() > 0)
                @foreach($categorias as $cat)
                <a href="{{ route('categoria.index') }}" class="btn btn-app">
                    <i class="fas fa-tag"></i> {{ $cat->name }}
                </a>
                @endforeach
                @else
                <div class="text-center text-muted">No se encontraron categorías.</div>
                @endif
            </div>
        </div>
    </div>

</div>
@stop