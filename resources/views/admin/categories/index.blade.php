@section('plugins.select2', true)

@extends('adminlte::page')


@section('title', 'Listado de Categorías')

@section('content_header')
    <h1>Categorías</h1>
@endsection



@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="bg-default p-4 d-flex align-items-center justify-content-between">
                    <div class="buttons">
                        <a href="{{ route('categories.create') }}" class="btn btn-info btn-sm">
                            <i class="fa fa-plus fa-fw"></i> Crear nueva categoría
                        </a>
                    </div>
                </div>

                <div class="card-body">

                    <!-- Tabla de categorías -->
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>
                                    ID

                                </th>
                                <th>
                                    Nombre
                                </th>
                                <th width="50%">Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $category->id }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->description }}</td>
                                    <td>
                                        <a href="{{ route('categories.show', $category->id) }}" class="btn btn-sm btn-info"
                                            title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('categories.edit', $category->id) }}"
                                            class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger delete-category"
                                                title="Eliminar" data-id="{{ $category->id }}">
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
        </div>
    </div>
@endsection

@section('js')
    <script>
        // Código utilizado para mostrar un mensaje de confirmación al eliminar una categoría
        document.addEventListener('DOMContentLoaded', function() {  // Solo se ejecuta cuando el documento se ha cargado completamente
            document.querySelectorAll('.delete-category').forEach(button => {   // Para cada elemento de la clase "delete-category" añade un evento al hacer click
                button.addEventListener('click', function(e) {
                    e.preventDefault(); // Previene la acción por defecto del botón
                    const form = this.closest('form');  // Busca el formulario más cercano al botón
                    Swal.fire({ // Muestra un mensaje de confirmación
                        title: '¿Estás seguro?',
                        text: "No podrás revertir esta acción",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',  
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',  
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {   // Si el usuario confirma, se envía el formulario
                            form.submit();
                        }
                    });
                });
            });

            // Mostrar mensaje de éxito con SweetAlert2
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '',
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
