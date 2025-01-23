@extends('adminlte::page')

@section('title', 'DESA Trainers')

@section('content_header')
    <h1>DESA Trainers</h1>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="bg-default p-4 d-flex align-items-center justify-content-between">
                <div class="buttons">
                    <a href="{{ route('desa-trainers.create') }}" class="btn btn-info btn-sm">
                        <i class="fa fa-plus fa-fw"></i> Crear nuevo DESA Trainer
                    </a>
                </div>
            </div>
            
            <div class="card-body">

                
                <!-- Tabla de DESA Trainers -->
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>
                                Nombre
                            </th>
                            <th>
                                Modelo
                            </th>
                            <th>Descripción</th>
                            <th>Imagen</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trainers as $trainer)
                        <tr>
                            <td>{{ $trainer->name }}</td>
                            <td>{{ $trainer->model }}</td>
                            <td>{{ Str::limit($trainer->description, 50) }}</td>
                            <td>
                                @if($trainer->image)
                                    <img src="{{ Storage::url($trainer->image) }}" 
                                         alt="{{ $trainer->name }}" 
                                         class="img-thumbnail"
                                         style="max-height: 50px;">
                                @else
                                    Sin imagen
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('desa-trainers.edit', $trainer->id) }}" 
                                    class="btn btn-sm btn-warning"
                                    data-toggle="tooltip" 
                                    data-placement="top" 
                                    title="Editar">
                                     <i class="fas fa-edit"></i>
                                 </a>
                                 <a href="{{ route('desa-trainers.show', $trainer->id) }}" 
                                    class="btn btn-sm btn-info" 
                                    data-toggle="tooltip" 
                                    data-placement="top" 
                                    title="Editar botones">
                                    <i class="fas fa-toggle-on"></i>
                                 </a>                                 
                                <form action="{{ route('desa-trainers.destroy', $trainer->id) }}" 
                                      method="POST" 
                                      style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-danger delete-trainer" 
                                            data-toggle="tooltip" 
                                            data-placement="top" 
                                            title="Eliminar"
                                            data-id="{{ $trainer->id }}">
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
    $(function () {
        // Inicializar tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Manejo de eliminación con SweetAlert2
        $('.delete-trainer').click(function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            
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
        
        // Mostrar mensaje de éxito/error
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
