@extends('adminlte::page')

@section('title', 'Crear Producto')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Nuevo Producto</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
                <li class="breadcrumb-item active">Crear</li>
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
                    <i class="fas fa-box-open mr-2"></i> Información del Producto
                </h3>
            </div>

            {{-- IMPORTANTE: enctype es necesario para subir imágenes --}}
            <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">

                        {{-- COLUMNA IZQUIERDA --}}
                        <div class="col-md-6">

                            {{-- Nombre --}}
                            <div class="form-group">
                                <label for="nombre">Nombre del Producto <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                    </div>
                                    <input type="text" name="nombre" id="nombre"
                                        class="form-control @error('nombre') is-invalid @enderror"
                                        value="{{ old('nombre') }}" placeholder="Ej: Filtro de Aceite">
                                    @error('nombre') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Categoría --}}
                            <div class="form-group">
                                <label for="categories_id">Categoría <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-tags"></i></span>
                                    </div>
                                    <select name="categories_id" id="categories_id" class="form-control @error('categories_id') is-invalid @enderror">
                                        <option value="">-- Seleccione una categoría --</option>
                                        @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" {{ old('categories_id') == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('categories_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Marca --}}
                            <div class="form-group">
                                <label for="marca">Marca</label>
                                <input type="text" name="marca" class="form-control @error('marca') is-invalid @enderror"
                                    value="{{ old('marca') }}" placeholder="Ej: Brembo">
                                @error('marca') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            {{-- Modelo --}}
                            <div class="form-group">
                                <label for="modelo">Modelo</label>
                                <input type="text" name="modelo" class="form-control @error('modelo') is-invalid @enderror"
                                    value="{{ old('modelo') }}" placeholder="Ej: Aveo">
                                @error('modelo') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            {{-- Año --}}
                            <div class="form-group">
                                <label for="anio">Año</label>
                                <input type="text" name="anio" class="form-control @error('anio') is-invalid @enderror"
                                    value="{{ old('anio') }}" placeholder="Ej: 2024">
                                @error('anio') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        {{-- COLUMNA DERECHA --}}
                        <div class="col-md-6">

                            {{-- Precio y Disponible (En una misma fila) --}}
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="precio">Precio ($) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" step="0.01" name="precio" class="form-control @error('precio') is-invalid @enderror"
                                                value="{{ old('precio') }}" placeholder="0.00">
                                            @error('precio') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="disponible">Stock Disponible</label>
                                        <input type="number" name="disponible" class="form-control @error('disponible') is-invalid @enderror"
                                            value="{{ old('disponible') }}" placeholder="0">
                                        @error('disponible') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Clave --}}
                            <div class="form-group">
                                <label for="clave">Clave / SKU</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                    </div>
                                    <input type="text" name="clave" class="form-control @error('clave') is-invalid @enderror"
                                        value="{{ old('clave') }}" placeholder="Ej: REF-001">
                                    @error('clave') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Clave Proveedor --}}
                            <div class="form-group">
                                <label for="clave_proveedor">Clave Proveedor</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-truck"></i></span>
                                    </div>
                                    <input type="text" name="clave_proveedor" class="form-control @error('clave_proveedor') is-invalid @enderror"
                                        value="{{ old('clave_proveedor') }}" placeholder="Ej: PROV-001">
                                    @error('clave_proveedor') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Ubicación --}}
                            <div class="form-group">
                                <label for="ubicacion">Ubicación en Almacén</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    </div>
                                    <input type="text" name="ubicacion" class="form-control @error('ubicacion') is-invalid @enderror"
                                        value="{{ old('ubicacion') }}" placeholder="Ej: Pasillo 3, Estante B">
                                    @error('ubicacion') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Imagen --}}
                            <div class="form-group">
                                <label for="imagen">Imagen del Producto</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('imagen') is-invalid @enderror" id="imagen" name="imagen">
                                        <label class="custom-file-label" for="imagen">Elegir archivo...</label>
                                    </div>
                                </div>
                                @error('imagen') <span class="text-danger small mt-1">{{ $message }}</span> @enderror
                                <small class="form-text text-muted">Formatos: jpg, jpeg, png.</small>
                            </div>

                        </div>
                    </div>

                    {{-- Descripción (Fila completa abajo) --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="3" placeholder="Detalles del producto...">{{ old('descripcion') }}</textarea>
                                @error('descripcion') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                </div>
                {{-- /.card-body --}}

                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('dashboard') }}" class="btn btn-default">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Guardar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    // Pequeño script para que al subir imagen aparezca el nombre del archivo
    $(document).ready(function() {
        // Enfocar siempre en el campo Clave al cargar (útil para escáner)
        // Aunque pongamos 'autofocus' en HTML, esto refuerza por si acaso
        $('input[name="clave"]').focus();

        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // Prevenir envío del formulario al dar Enter en el campo Clave (comportamiento típico de escáner)
        $('input[name="clave"]').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                // Opcional: mover el foco al siguiente campo, ej: Nombre
                $('input[name="nombre"]').focus();
                return false;
            }
        });
    });
</script>
@stop