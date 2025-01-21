@extends('adminlte::page')

@section('title', 'Crear Instrucción')

@section('content_header')
    <h1 class="m-0 text-dark">Crear Instrucción</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form action="{{ route('instructions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="card-header">
                    <h3 class="card-title">Información de la Instrucción</h3>
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
                                <label for="scenario_id">Escenario</label>
                                <select name="scenario_id" class="form-control select2 @error('scenario_id') is-invalid @enderror" required>
                                    <option value="">Seleccione un escenario</option>
                                    @foreach($scenarios as $id => $title)
                                        <option value="{{ $id }}" {{ old('scenario_id', $selectedScenario) == $id ? 'selected' : '' }}>
                                            {{ $title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('scenario_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="title">Título</label>
                                <input type="text" 
                                       class="form-control @error('title') is-invalid @enderror" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title') }}" 
                                       required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="content">Contenido</label>
                                <textarea class="form-control @error('content') is-invalid @enderror" 
                                          id="content" 
                                          name="content" 
                                          rows="5">{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- <div class="form-group">
                                <label for="duration">Duración (segundos)</label>
                                <input type="number" 
                                       class="form-control @error('duration') is-invalid @enderror" 
                                       id="duration" 
                                       name="duration" 
                                       value="{{ old('duration') }}" 
                                       min="0">
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Dejar en blanco si no desea avance automático</small>
                            </div> --}}

                            <div class="form-group">
                                <label for="audio_file">Archivo de Audio</label>
                                <div class="custom-file">
                                    <input type="file" 
                                           class="custom-file-input @error('audio_file') is-invalid @enderror" 
                                           id="audio_file" 
                                           name="audio_file"
                                           accept="audio/mp3,audio/wav">
                                    <label class="custom-file-label" for="audio_file">Seleccionar archivo</label>
                                </div>
                                @error('audio_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Formatos permitidos: MP3, WAV. Tamaño máximo: 10MB</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="{{ route('instructions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
@stop

@section('js')
@stop