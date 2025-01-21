@extends('adminlte::page')

@section('title', 'Listado de Instrucciones')

@section('content_header')
    <h1>Instrucciones</h1>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="bg-default p-4 d-flex align-items-center justify-content-between">
                <div class="buttons">
                    <a href="{{ route('instructions.create') }}" class="btn btn-info btn-sm">
                        <i class="fa fa-plus fa-fw"></i> Crear nueva instrucción
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Formulario de búsqueda y filtros -->
                <form method="GET" action="{{ route('instructions.index') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-2">
                            <select name="per_page" class="form-control mb-2">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>Mostrar 10</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>Mostrar 25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>Mostrar 50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>Mostrar 100</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                placeholder="Buscar por título" class="form-control mb-2">
                        </div>
                        <div class="col-md-3">
                            <select name="scenario_id" class="form-control select2 mb-2">
                                <option value="">Todos los escenarios</option>
                                @foreach($scenarios as $id => $title)
                                    <option value="{{ $id }}" {{ request('scenario_id') == $id ? 'selected' : '' }}>
                                        {{ $title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="duration" value="{{ request('duration') }}" 
                                placeholder="Duración máx." class="form-control mb-2">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary mb-2">Buscar</button>
                            <a href="{{ route('instructions.index') }}" class="btn btn-secondary mb-2">Limpiar</a>
                        </div>
                    </div>
                </form>
                
                <!-- Tabla de instrucciones -->
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>
                                <a href="{{ route('instructions.index', ['sortField' => 'id', 'sortDirection' => request('sortDirection') == 'asc' ? 'desc' : 'asc'] + request()->all()) }}">
                                    ID @if(request('sortField') == 'id') <i class="fa fa-sort-{{ request('sortDirection') == 'asc' ? 'up' : 'down' }}"></i> @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('instructions.index', ['sortField' => 'title', 'sortDirection' => request('sortDirection') == 'asc' ? 'desc' : 'asc'] + request()->all()) }}">
                                    Título @if(request('sortField') == 'title') <i class="fa fa-sort-{{ request('sortDirection') == 'asc' ? 'up' : 'down' }}"></i> @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('instructions.index', ['sortField' => 'scenario_id', 'sortDirection' => request('sortDirection') == 'asc' ? 'desc' : 'asc'] + request()->all()) }}">
                                    Escenario @if(request('sortField') == 'scenario_id') <i class="fa fa-sort-{{ request('sortDirection') == 'asc' ? 'up' : 'down' }}"></i> @endif
                                </a>
                            </th>

                            <th>Audio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($instructions as $instruction)
                        <tr>
                            <td width="">
                                {{ $instruction->id }}</td>
                            <td width="20%">
                                {{ $instruction->title }}</td>
                            <td width="auto">
                                {{ $instruction->scenario->title }}</td>
                            <td width="auto">
                                @if($instruction->audio_file)
                                    <audio controls class="audio-player">
                                        <source src="{{ Storage::url($instruction->audio_file) }}" type="audio/mpeg">
                                        Tu navegador no soporta el elemento de audio.
                                    </audio>
                                @else
                                    Sin audio
                                @endif
                            </td>
                            <td width="25%">
                                <a href="{{ route('instructions.show', $instruction->id) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('instructions.edit', $instruction->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <!-- Add view transitions -->
                                <a href="{{ route('transitions.index', ['from_instruction_id' => $instruction->id]) }}" class="btn btn-sm btn-secondary" title="Ver transiciones">
                                    <i class="fas fa-eye
                                    "></i> Transiciones
                                </a>

                                <a href="{{ route('transitions.create', ['from_instruction_id' => $instruction->id]) }}" 
                                    class="btn btn-sm btn-success" 
                                    title="Añadir transición">
                                     <i class="fas fa-plus"></i> Transición
                                 </a>
                                  
                                <form action="{{ route('instructions.destroy', $instruction->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger delete-instruction" title="Eliminar" data-id="{{ $instruction->id }}">
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
                    {{ $instructions->links() }}
                </div>
                <div class="mt-4">
                    {{ __('Showing') }} {{ $instructions->firstItem() }} {{ __('to') }} {{ $instructions->lastItem() }}
                    {{ __('of') }} {{ $instructions->total() }} {{ __('results') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>

</style>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Manejo de eliminación con SweetAlert2
        document.querySelectorAll('.delete-instruction').forEach(button => {
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