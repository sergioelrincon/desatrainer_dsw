@section('plugins.Select2', true)

@extends('adminlte::page')

@section('title', 'Editar Escenario')

@section('content_header')
    <h1 class="m-0 text-dark">Editar Escenario</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('scenarios.update', $scenario->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-header bg-olive">
                        <h3 class="card-title">Información del Escenario</h3>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="category_id">Categoría</label>
                                    <select name="category_id"
                                        class="form-control select2 @error('category_id') is-invalid @enderror" required>
                                        <option value="">Seleccione una categoría</option>
                                        @foreach ($categories as $id => $name)
                                            <option value="{{ $id }}"
                                                {{ old('category_id', $scenario->category_id) == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="title">Título</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        id="title" name="title" value="{{ old('title', $scenario->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="description">Descripción</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        rows="3">{{ old('description', $scenario->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="desa_trainer_id">Desa Trainer</label>
                                    <select name="desa_trainer_id" 
                                            class="form-control select2 @error('desa_trainer_id') is-invalid @enderror">
                                        <option value="">Seleccione un trainer</option>
                                         @foreach($desaTrainers as $id => $name)
                                            <option value="{{ $id }}" 
                                                {{ old('desa_trainer_id', $scenario->desa_trainer_id) == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach 
                                    </select>
                                    @error('desa_trainer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('scenarios.show', $scenario->id) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="mt-4">
                        @livewire('admin.scenario-instructions', ['scenario' => $scenario])
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="mt-4">
                        @livewire('admin.instruction-transitions', ['scenario' => $scenario])
                    </div>
                </div>
            </div>
            
        </div>
    </div>

@stop

@section('css')
@stop

@section('js')
@stop
