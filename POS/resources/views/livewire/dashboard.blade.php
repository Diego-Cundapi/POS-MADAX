@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<h1>Panel de inventario</h1>
@stop

@section('content')

{{-- 2. SECCIÓN SUPERIOR: Importador de Excel --}}
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-success text-white">
        <h3 class="card-title"><i class="fas fa-file-excel"></i> Importar Productos Masivamente</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('productos.importar') }}" method="POST" enctype="multipart/form-data" class="row align-items-center">
            @csrf
            <div class="col-md-4 mb-2">
                <label for="archivo_excel" class="form-label mb-0">Seleccionar archivo Excel (.xlsx):</label>
            </div>
            <div class="col-md-5 mb-2">
                <input type="file" name="archivo_excel" class="form-control" required>
            </div>
            <div class="col-md-3 mb-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-upload"></i> Subir y Procesar
                </button>
            </div>
        </form>

        {{-- Mostrar errores de validación del Excel (si hay) --}}
        @if($errors->any())
        <div class="alert alert-danger mt-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <small class="text-muted mt-2 d-block">
            <i class="fas fa-info-circle"></i> El Excel debe tener encabezados en la primera fila: <strong>nombre y clave</strong> (obligatorios), <em>precio,marca, stock, modelo, anio, descripcion, ubicacion, clave_proveedor, categoria_id</em> (opcionales).
        </small>
    </div>
</div>

{{-- 3. SECCIÓN MEDIA: Botones de Acción --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    {{-- Botón Izquierda: Categoría --}}
    <div>
        <a href="{{route('categoria.create')}}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-tags"></i> Nueva categoría
        </a>
    </div>

    {{-- Botones Derecha --}}
    <div class="d-flex">
        {{-- Exportar Excel (Solo Admin) --}}
        @role('Admin')
        <div class="mr-2">
            {{-- Sin target="_blank" para evitar ventana blanca en NativePHP/Electron --}}
            <a href="{{ route('productos.exportar') }}" class="btn btn-success shadow-sm" download>
                <i class="fas fa-file-excel"></i> Exportar CSV
            </a>
        </div>
        @endrole

        {{-- Nuevo Producto --}}
        <div>
            <a href="{{route('productos.create')}}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus-circle"></i> Nuevo Producto
            </a>
        </div>
    </div>
</div>

{{-- 4. SECCIÓN INFERIOR: Tabla de Productos --}}
<div class="card">
    <div class="card-body table-responsive p-0">
        <table id="tabla-inventario" class="table table-bordered table-striped table-hover dt-responsive nowrap" style="width:100%">
            <thead class="bg-light">
                <tr>
                    <th style="width: 10px;"></th> {{-- Columna de control --}}
                    <th class="text-center">Clave <i class="fas fa-sort text-muted float-right"></i></th>
                    <th class="text-center" style="width: 250px;">Nombre <i class="fas fa-sort text-muted float-right"></i></th>
                    <th class="text-center">Año <i class="fas fa-sort text-muted float-right"></i></th>
                    <th class="text-center" style="width: 150px;">Modelo <i class="fas fa-sort text-muted float-right"></i></th>
                    <th class="none">Marca</th>
                    <th class="text-center" style="width: 100px;">Ubicación <i class="fas fa-sort text-muted float-right"></i></th>
                    <th class="text-center all" style="width: 80px;">Stock <i class="fas fa-sort text-muted float-right"></i></th>
                    <th class="text-center all">Precio <i class="fas fa-sort text-muted float-right"></i></th>
                    
                    {{-- CAMPOS OCULTOS (CHILD ROW) --}}
                    <th class="none">Categoría</th>
                    <th class="none">Clave Proveedor</th>
                    <th class="none">Descripción</th>
                    <th class="none">Imagen</th>
                    <th class="none">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $producto)
                <tr>
                    <td></td> {{-- Columna de control vacía --}}
                    
                    {{-- 1. Clave --}}
                    {{-- 1. Clave --}}
                    <td class="align-middle" style="max-width: 120px;">
                        <div style="overflow-x: auto; white-space: nowrap; padding-bottom: 4px;">
                            {{ $producto->clave }}
                        </div>
                    </td>

                    {{-- 2. Nombre --}}
                    <td class="align-middle font-weight-bold" style="max-width: 250px;">
                        <div style="overflow-x: auto; white-space: nowrap; padding-bottom: 4px;">
                            {{ $producto->nombre }}
                        </div>
                    </td>

                    {{-- 3. Año --}}
                    {{-- 3. Año --}}
                    <td class="align-middle" style="max-width: 80px;">
                        <div style="overflow-x: auto; white-space: nowrap; padding-bottom: 4px;">
                            {{ $producto->anio ?? 'N/A' }}
                        </div>
                    </td>

                    {{-- 4. Modelo --}}
                    <td class="align-middle" style="max-width: 150px;">
                        <div style="overflow-x: auto; white-space: nowrap; padding-bottom: 4px;">
                            {{ $producto->modelo }}
                        </div>
                    </td>

                    {{-- 5. Marca --}}
                    <td class="align-middle">{{ $producto->marca }}</td>

                    {{-- 6. Ubicación --}}
                    {{-- 6. Ubicación --}}
                    <td class="align-middle" style="max-width: 100px;">
                        <div style="overflow-x: auto; white-space: nowrap; padding-bottom: 4px;">
                            {{ $producto->ubicacion ?? 'N/A' }}
                        </div>
                    </td>

                    {{-- 7. Stock --}}
                    <td class="align-middle text-center" style="max-width: 80px;">
                        <div style="overflow-x: auto; white-space: nowrap; padding-bottom: 4px;">
                            <span class="badge {{ $producto->disponible > 0 ? 'badge-success' : 'badge-danger' }}" style="font-size: 1rem;">
                                {{ $producto->disponible }}
                            </span>
                        </div>
                    </td>

                    {{-- 8. Precio --}}
                    <td class="align-middle text-success font-weight-bold" style="max-width: 120px;">
                        <div style="overflow-x: auto; white-space: nowrap; padding-bottom: 4px;">
                            ${{ number_format($producto->precio, 2) }}
                        </div>
                    </td>

                    {{-- CHILD: Categoría --}}
                    <td class="align-middle text-muted">{{ $producto->categoria ? $producto->categoria->name : 'Sin categoría' }}</td>

                    {{-- CHILD: Clave Proveedor --}}
                    <td class="align-middle text-info">{{ $producto->clave_proveedor ?? 'Sin clave proveedor' }}</td>

                    {{-- CHILD: Descripción --}}
                    <td class="align-middle">{{ $producto->descripcion ?? 'Sin descripción' }}</td>

                    {{-- CHILD: Imagen --}}
                    <td class="align-middle">
                        @if($producto->imagen)
                        <img src="{{ asset($producto->imagen) }}" alt="Imagen" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                        <span class="text-muted">Sin imagen</span>
                        @endif
                    </td>

                    {{-- CHILD: Acciones --}}
                    <td class="align-middle">
                        <div class="btn-group">
                            <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-info" title="Editar">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" class="d-inline ml-2">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger" onclick="confirmAction(event, '¿Desea eliminar este producto?', function() { this.closest('form').submit(); }.bind(this))" title="Eliminar">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">
                        <i class="fas fa-box-open fa-3x mb-3"></i><br>
                        No hay productos registrados aún.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- AQUÍ AGREGAMOS LA PAGINACIÓN --}}

</div>

@stop

@section('preloader')
<i class="fas fa-4x fa-spin fa-spinner text-secondary"></i>
<h4 class="mt-4 text-dark">Cargando</h4>
@stop

@section('css')
    @vite(['resources/css/app.css'])
    <style>
        table.dataTable > tbody > tr.child ul.dtr-details {
            display: block;
            width: 100%;
        }
        table.dataTable > tbody > tr.child span.dtr-title {
            font-weight: bold;
            min-width: 120px;
        }
    </style>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)

@section('js')
    <script>
        $(document).ready(function() {
            $('#tabla-inventario').DataTable({
                "ordering": true,
                "responsive": {
                    details: {
                        type: 'column',
                        target: 0
                    }
                },
                "serverSide": false,
                "autoWidth": false,
                "columnDefs": [ {
                    "className": 'dtr-control text-center',
                    "orderable": false,
                    "targets":   0
                } ],
                "order": [
                    [1, "desc"]
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
        console.log("Inventario cargado");
    
        const mensajeExito = @json(session('success'));
        const mensajeError = @json(session('error'));
        const erroresValidacion = @json($errors->any() ? 'Hay errores en el formulario o archivo.' : null);
    
        const mostrarAlerta = () => {
            if (typeof Swal === 'undefined') {
                setTimeout(mostrarAlerta, 100);
                return;
            }
    
            if (mensajeExito) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: mensajeExito
                });
            }
    
            if (mensajeError) {
                Swal.fire({
                    icon: 'error',
                    title: '¡Ocurrió un error!',
                    text: mensajeError,
                    confirmButtonText: 'Cerrar',
                    confirmButtonColor: '#d33'
                });
            }
    
            if (erroresValidacion) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',
                    text: 'Por favor revisa los errores mostrados en el formulario.',
                    confirmButtonText: 'Ok'
                });
            }
        };
    
        mostrarAlerta();
    </script>
@stop