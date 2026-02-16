@extends('adminlte::page')

@section('title', 'Editar Producto')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Editar Producto</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">

        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-edit mr-2"></i> Editando: {{ $producto->nombre }}
                </h3>
            </div>

            {{-- Formulario PUT para actualizar --}}
            <form action="{{ route('productos.update', $producto->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <div class="row">

                        {{-- COLUMNA IZQUIERDA --}}
                        <div class="col-md-6">

                            {{-- Nombre --}}
                            <div class="form-group">
                                <label for="nombre">Nombre del Producto <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="nombre"
                                    class="form-control @error('nombre') is-invalid @enderror"
                                    value="{{ old('nombre', $producto->nombre) }}">
                                @error('nombre') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            {{-- Categoría --}}
                            <div class="form-group">
                                <label for="categories_id">Categoría <span class="text-danger">*</span></label>
                                <select name="categories_id" id="categories_id" class="form-control @error('categories_id') is-invalid @enderror">
                                    <option value="">-- Seleccione --</option>
                                    @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}"
                                        {{ old('categories_id', $producto->categories_id) == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('categories_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            {{-- Marca --}}
                            <div class="form-group">
                                <label for="marca">Marca</label>
                                <input type="text" name="marca" class="form-control @error('marca') is-invalid @enderror"
                                    value="{{ old('marca', $producto->marca) }}">
                                @error('marca') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            {{-- Modelo --}}
                            <div class="form-group">
                                <label for="modelo">Modelo</label>
                                <input type="text" name="modelo" class="form-control @error('modelo') is-invalid @enderror"
                                    value="{{ old('modelo', $producto->modelo) }}">
                                @error('modelo') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            {{-- Año --}}
                            <div class="form-group">
                                <label for="anio">Año</label>
                                <input type="text" name="anio" class="form-control @error('anio') is-invalid @enderror"
                                    value="{{ old('anio', $producto->anio) }}">
                                @error('anio') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            {{-- Clave --}}
                            <div class="form-group">
                                <label for="clave">Clave / SKU</label>
                                <input type="text" name="clave" class="form-control @error('clave') is-invalid @enderror"
                                    value="{{ old('clave', $producto->clave) }}">
                                @error('clave') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            {{-- Clave Proveedor --}}
                            <div class="form-group">
                                <label for="clave_proveedor">Clave Proveedor</label>
                                <input type="text" name="clave_proveedor" class="form-control @error('clave_proveedor') is-invalid @enderror"
                                    value="{{ old('clave_proveedor', $producto->clave_proveedor) }}">
                                @error('clave_proveedor') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            {{-- Ubicación --}}
                            <div class="form-group">
                                <label for="ubicacion">Ubicación en Almacén</label>
                                <input type="text" name="ubicacion" class="form-control @error('ubicacion') is-invalid @enderror"
                                    value="{{ old('ubicacion', $producto->ubicacion) }}">
                                @error('ubicacion') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        {{-- COLUMNA DERECHA --}}
                        <div class="col-md-6">

                            {{-- Precio y Disponible --}}
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="precio">Precio ($) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" step="0.01" name="precio" class="form-control @error('precio') is-invalid @enderror"
                                                value="{{ old('precio', $producto->precio) }}">
                                            @error('precio') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="disponible">Stock Disponible</label>
                                        <input type="number" name="disponible" class="form-control @error('disponible') is-invalid @enderror"
                                            value="{{ old('disponible', $producto->disponible) }}">
                                        @error('disponible') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Imagen Actual y Nueva --}}
                            <div class="form-group">
                                <label>Imagen del Producto</label>

                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        @if($producto->imagen)
                                        <label class="text-xs text-muted">Actual:</label>
                                        <div class="border rounded p-1 text-center">
                                            <img src="{{ asset($producto->imagen) }}" alt="Imagen actual" class="img-fluid" style="max-height: 100px;">
                                        </div>
                                        @else
                                        <span class="text-muted text-sm">Sin imagen actual</span>
                                        @endif
                                    </div>
                                    <div class="col-sm-8">
                                        <label for="imagen" class="text-xs text-muted">Cambiar Imagen (Opcional):</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input @error('imagen') is-invalid @enderror" id="imagen" name="imagen">
                                            <label class="custom-file-label" for="imagen">Elegir nueva...</label>
                                        </div>
                                        @error('imagen') <span class="text-danger small mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Descripción --}}
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="4">{{ old('descripcion', $producto->descripcion) }}</textarea>
                                @error('descripcion') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                        </div>
                    </div>
                </div>
                {{-- /.card-body --}}

                <div class="card-footer d-flex justify-content-between">
                    {{-- Botón Cancelar (Redirige al Dashboard) --}}
                    <a href="{{ route('dashboard') }}" class="btn btn-default">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </a>

                    {{-- Botón Actualizar --}}
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sync-alt mr-1"></i> Actualizar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    // Nombre del archivo en el input file
    $(document).ready(function() {
        // Enfocar siempre en el campo Clave al cargar la edición
        $('input[name="clave"]').focus();

        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // Prevenir envío del formulario al dar Enter en Clave
        $('input[name="clave"]').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
@stop