@extends('adminlte::page')

@section('title', 'Editar Instrucción')

@section('content_header')
    <h1 class="m-0 text-dark">Editar Instrucción</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('instructions.update', $instruction->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

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
                                    <select name="scenario_id"
                                        class="form-control select2 @error('scenario_id') is-invalid @enderror" required>
                                        <option value="">Seleccione un escenario</option>
                                        @foreach ($scenarios as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ old('scenario_id', $instruction->scenario_id) == $id ? 'selected' : '' }}>
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
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        id="title" name="title" value="{{ old('title', $instruction->title) }}"
                                        required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="content">Contenido</label>
                                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="5">{{ old('content', $instruction->content) }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{--<div class="form-group">
                                    <label for="duration">Duración (segundos)</label>
                                    <input type="number" class="form-control @error('duration') is-invalid @enderror"
                                        id="duration" name="duration"
                                        value="{{ old('duration', $instruction->duration) }}" min="0">
                                    @error('duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Dejar en blanco si no desea avance automático</small>
                                </div> --}}

                                <div class="form-group">
                                    <label for="audio_file">Archivo de Audio</label>
                                    @if ($instruction->audio_file)
                                        <div class="mb-2">
                                            <div class="d-flex align-items-center">
                                                <audio controls class="mr-2">
                                                    <source src="{{ Storage::url($instruction->audio_file) }}"
                                                        type="audio/mpeg">
                                                    Tu navegador no soporta el elemento de audio.
                                                </audio>
                                                <button type="button" class="btn btn-danger btn-sm delete-audio"
                                                    data-instruction-id="{{ $instruction->id }}" title="Eliminar audio">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file"
                                            class="custom-file-input @error('audio_file') is-invalid @enderror"
                                            id="audio_file" name="audio_file" accept="audio/mp3,audio/wav">
                                        <label class="custom-file-label" for="audio_file">
                                            {{ $instruction->audio_file ? 'Cambiar archivo' : 'Seleccionar archivo' }}
                                        </label>
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
                            <i class="fas fa-save"></i> Guardar Cambios
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
    <style>
        .custom-file-input:lang(en)~.custom-file-label::after {
            content: "Buscar";
        }

        audio {
            max-width: 100%;
            margin-bottom: 10px;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {

            // Mostrar nombre del archivo seleccionado
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });

            // Mostrar mensajes de éxito/error
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



            // Manejo de eliminación de audio
            $('.delete-audio').click(function() {
                const instructionId = $(this).data('instruction-id');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Se eliminará el archivo de audio",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: `/admin/instructions/${instructionId}/delete-audio`, // Usando backticks aquí
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '¡Éxito!',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: '¡Error!',
                                        text: response.message
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: '¡Error!',
                                    text: 'No se pudo eliminar el audio'
                                });
                            }
                        });
                    }
                });
            });

        });

        // Manejo de eliminación de audio
    </script>
@stop
