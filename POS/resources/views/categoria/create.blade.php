@extends('adminlte::page')

@section('title', 'Crear Categoría')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Nueva Categoría</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('categoria.index') }}">Categorías</a></li>
                <li class="breadcrumb-item active">Crear</li>
            </ol>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">

        {{-- Tarjeta estilo AdminLTE --}}
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit mr-1"></i> Detalles de la Categoría
                </h3>
            </div>

            <form method="POST" action="{{ route('categoria.store') }}">
                @csrf
                <div class="card-body">

                    {{-- Campo Nombre --}}
                    <div class="form-group">
                        <label for="name">Nombre de la Categoría</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                            </div>
                            <input type="text"
                                id="name"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Ej: Electrónica, Frenos, Suspensión..."
                                value="{{ old('name') }}"
                                autofocus>

                            {{-- Mensaje de Error --}}
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                </div>
                {{-- /.card-body --}}

                <div class="card-footer d-flex justify-content-between">
                    {{-- Botón Cancelar --}}
                    <a href="{{ route('categoria.index') }}" class="btn btn-default">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </a>

                    {{-- Botón Guardar --}}
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Guardar Categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
{{-- Si necesitas forzar algún estilo extra, va aquí --}}
@stop

@section('js')
<script>
    console.log('Vista crear categoría cargada');
</script>
@stop