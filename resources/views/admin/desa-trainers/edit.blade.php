
@extends('adminlte::page')

@section('title', 'Editar DESA Trainer')

@section('content_header')
    <h1 class="m-0 text-dark">Editar DESA Trainer</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form action="{{ route('desa-trainers.update', $desaTrainer->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="card-header">
                    <h3 class="card-title">Información del DESA Trainer</h3>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nombre</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $desaTrainer->name) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="model">Modelo</label>
                                <input type="text" 
                                       class="form-control @error('model') is-invalid @enderror" 
                                       id="model" 
                                       name="model" 
                                       value="{{ old('model', $desaTrainer->model) }}" 
                                       required>
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="description">Descripción</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="4">{{ old('description', $desaTrainer->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="image">Imagen del DESA</label>
                                @if($desaTrainer->image)
                                    <div class="current-image mb-3">
                                        <label>Imagen Actual:</label>
                                        <img src="{{ Storage::url($desaTrainer->image) }}" 
                                             alt="Imagen actual" 
                                             class="img-fluid img-thumbnail"
                                             style="max-height: 200px;">
                                    </div>
                                @endif
                                <div class="custom-file">
                                    <input type="file" 
                                           class="custom-file-input @error('image') is-invalid @enderror" 
                                           id="image" 
                                           name="image"
                                           accept="image/*">
                                    <label class="custom-file-label" for="image">
                                        {{ $desaTrainer->image ? 'Cambiar imagen' : 'Seleccionar imagen' }}
                                    </label>
                                </div>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Dejar vacío para mantener la imagen actual. La nueva imagen reemplazará a la anterior.
                                </small>
                            </div>

                            <div class="image-preview mt-3" style="display: none;">
                                <p class="font-weight-bold">Vista previa de la nueva imagen:</p>
                                <img id="preview" src="#" alt="Vista previa" class="img-fluid">
                            </div>

                            {{--<div class="form-group mt-3">
                                <label for="settings">Configuración adicional (JSON)</label>
                                <textarea class="form-control @error('settings') is-invalid @enderror" 
                                          id="settings" 
                                          name="settings" 
                                          rows="4"
                                          placeholder="{}"
                                >{{ old('settings', json_encode($desaTrainer->settings, JSON_PRETTY_PRINT)) }}</textarea>
                                @error('settings')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Configuración en formato JSON para opciones adicionales del DESA.
                                </small>
                            </div> --}}
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    <a href="{{ route('desa-trainers.index') }}" class="btn btn-secondary">
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
    .image-preview img {
        max-height: 300px;
        object-fit: contain;
    }
    .current-image {
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 4px;
        background-color: #f8f9fa;
    }
    .current-image label {
        display: block;
        font-weight: bold;
        margin-bottom: 10px;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Mostrar nombre del archivo seleccionado y preview
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
            
            // Mostrar vista previa de la nueva imagen
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview').attr('src', e.target.result);
                    $('.image-preview').show();
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Formatear JSON en el campo settings al cargar
        try {
            const settings = $('#settings').val();
            if (settings) {
                const formattedJson = JSON.stringify(JSON.parse(settings), null, 2);
                $('#settings').val(formattedJson);
            }
        } catch (e) {
            console.error('Error al formatear JSON:', e);
        }

        // Validar JSON en el campo settings
        $('form').on('submit', function(e) {
            const settingsValue = $('#settings').val();
            try {
                if (settingsValue) {
                    JSON.parse(settingsValue);
                }
            } catch (error) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error de formato',
                    text: 'El campo de configuración debe ser un JSON válido',
                });
            }
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
@stop
