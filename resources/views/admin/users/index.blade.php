@extends('adminlte::page')

@section('title', 'Listado de Usuarios')

@section('content_header')
    <h1>Usuarios</h1>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="bg-default p-4 d-flex align-items-center justify-content-between">
                    <div class="buttons">
                        <a href="{{ route('users.create') }}" class="btn btn-info btn-sm">
                            <i class="fa fa-plus fa-fw"></i> Crear nuevo usuario
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Formulario de búsqueda y filtros -->
                    <form method="GET" action="{{ route('users.index') }}" class="mb-4 d-flex flex-wrap">
                        <!-- Selector de cantidad de registros por página -->
                        <select name="per_page" class="form-control mb-2 mr-2">
                            <option value="2" {{ request('per_page') == 2 ? 'selected' : '' }}>Mostrar 2 registros</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>Mostrar 25 registros</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>Mostrar 50 registros</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>Mostrar 100 registros</option>
                        </select> 
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o email" class="form-control mb-2 mr-2">
                        <button type="submit" class="btn btn-primary mb-2 mr-2">Buscar</button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary mb-2">Limpiar</a>
                    </form>
                

                    <!-- Tabla de usuarios -->
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>
                                    Nombre 
                                </th>
                                <th>
                                    Email 
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-info"
                                            title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning"
                                            title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger delete-user"
                                                title="Eliminar" data-id="{{ $user->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $users->links("vendor.pagination.bootstrap-4") }}
                    </div>
                    <div class="mt-4">
                        {{ __('Showing') }} {{ $users->firstItem() }} {{ __('to') }} {{ $users->lastItem() }}
                        {{ __('of') }} {{ $users->total() }} {{ __('results') }}
                    </div>                    
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- SweetAlert2 para confirmación de eliminación y mensajes de éxito -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejo de eliminación con SweetAlert2
            document.querySelectorAll('.delete-user').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡No podrás revertir esta acción!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Mostrar mensaje de éxito con SweetAlert2
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif
        });
    </script>
@endsection
