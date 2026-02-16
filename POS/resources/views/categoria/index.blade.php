@extends('adminlte::page')

@section('title', 'Categorías')

@section('content_header')
<h1>Listado de Categorías</h1>
@stop

@section('content')



<div class="card">
    <div class="card-header">
        <h3 class="card-title">Categorías Registradas</h3>
        <div class="card-tools">
            <a href="{{ route('categoria.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nueva Categoría
            </a>
        </div>
    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categorias as $cat)
                <tr>
                    <td>{{ $cat->id }}</td>
                    <td>{{ $cat->name }}</td>
                    <td class="text-center">
                        {{-- Botón Editar (Si lo implementas luego) --}}
                        <a href="{{ route('categoria.edit', $cat->id) }}" class="btn btn-sm btn-info" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>

                        {{-- Botón Eliminar --}}
                        <form action="{{ route('categoria.destroy', $cat->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmAction(event, '¿Eliminar esta categoría?', function() { this.closest('form').submit(); }.bind(this))" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">No hay categorías registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop

@section('js')
<script>
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
        const successMsg = @json(session('success'));
        const errorMsg = @json(session('error'));

        if (successMsg) {
            Swal.fire({
                icon: 'success',
                title: '¡Operación Exitosa!',
                text: successMsg,
                timer: 3000,
                showConfirmButton: false
            });
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