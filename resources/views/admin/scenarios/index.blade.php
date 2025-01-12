@extends('adminlte::page')

@section('title', 'Listado de Escenarios')

@section('content_header')
    <h1>Escenarios</h1>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="bg-default p-4 d-flex align-items-center justify-content-between">
                <div class="buttons">
                    <a href="{{ route('scenarios.create') }}" class="btn btn-info btn-sm">
                        <i class="fa fa-plus fa-fw"></i> Crear nuevo escenario
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                
                <!-- Tabla de escenarios -->
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>
                                Título
                            </th>
                            <th>
                                Categoría
                            </th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scenarios as $scenario)
                        <tr>
                            <td>{{ $scenario->title }}</td>
                            <td>{{ $scenario->category->name }}</td>
                            <td>{{ Str::limit($scenario->description, 50) }}</td>
                            <td>
                                <a href="{{ route('scenarios.show', $scenario->id) }}" 
                                    class="btn btn-sm btn-info" 
                                    data-toggle="tooltip" 
                                    data-placement="top" 
                                    title="Ver detalles">
                                     <i class="fas fa-eye"></i>
                                 </a>
                                <a href="{{ route('scenarios.edit', $scenario->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('scenarios.destroy', $scenario->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger delete-scenario" title="Eliminar" data-id="{{ $scenario->id }}">
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
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-scenario').forEach(button => {
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
        
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 3000
        });
        @endif
        
        @if(session('error'))
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