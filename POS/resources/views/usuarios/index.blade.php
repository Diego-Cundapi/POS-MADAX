@extends('adminlte::page')

@section('title', 'Gestión de Usuarios')

@section('content_header')
    <h1>Lista de Usuarios</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Usuario
            </a>

            @role('Admin')
            <form action="{{ route('usuarios.reset') }}" method="POST" class="d-inline float-right">
                @csrf
                <button type="button" class="btn btn-danger" onclick="confirmAction(event, 'ATENCIÓN: ¿Estás seguro de que deseas eliminar TODOS los datos de negocio (Ventas, Productos, Clientes)? Esta acción no se puede deshacer.', function() { this.closest('form').submit(); }.bind(this))">
                    <i class="fas fa-dumpster-fire"></i> Restaurar Fábrica
                </button>
            </form>
            @endrole
        </div>
        <div class="card-body">
            <table id="tabla-usuarios" class="table table-bordered table-striped dt-responsive nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if(!empty($user->getRoleNames()))
                                    @foreach($user->getRoleNames() as $role)
                                        <span class="badge badge-info">{{ $role }}</span>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('usuarios.edit', $user->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmAction(event, '¿Estás seguro de eliminar este usuario?', function() { this.closest('form').submit(); }.bind(this))" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables-plugins/responsive/css/responsive.bootstrap4.min.css') }}">
@stop

@section('js')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#tabla-usuarios').DataTable({
                "responsive": true,
                "autoWidth": false,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                }
            });

            // SweetAlert Logic
            const successMsg = @json(session('success'));
            const errorMsg = @json(session('error'));

            if (successMsg) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
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
