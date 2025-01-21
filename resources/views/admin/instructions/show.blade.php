@extends('adminlte::page')

@section('title', 'Detalles de la Instrucción')

@section('content_header')
    <h1 class="m-0 text-dark">Detalles de la Instrucción</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información de la Instrucción</h3>
                <div class="card-tools">
                    <a href="{{ route('instructions.index') }}" class="btn btn-sm btn-info">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <a href="{{ route('instructions.edit', $instruction->id) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width: 20%">ID</th>
                                        <td>{{ $instruction->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Escenario</th>
                                        <td>
                                            <a href="{{ route('scenarios.show', $instruction->scenario->id) }}">
                                                {{ $instruction->scenario->title }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Título</th>
                                        <td>{{ $instruction->title }}</td>
                                    </tr>
                                    <tr>
                                        <th>Contenido</th>
                                        <td>
                                            <div class="content-preview custom-content">
                                                {!! $instruction->content !!}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Duración</th>
                                        <td>
                                            @if($instruction->duration)
                                                {{ $instruction->duration }} segundos
                                            @else
                                                No definida
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Audio</th>
                                        <td>
                                            @if($instruction->audio_file)
                                                <audio controls class="w-100 mb-2">
                                                    <source src="{{ Storage::url($instruction->audio_file) }}" type="audio/mpeg">
                                                    Tu navegador no soporta el elemento de audio.
                                                </audio>
                                                <small class="text-muted">
                                                    Archivo: {{ basename($instruction->audio_file) }}
                                                </small>
                                            @else
                                                <span class="text-muted">Sin archivo de audio</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Fecha de Creación</th>
                                        <td>{{ $instruction->created_at->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Última Actualización</th>
                                        <td>{{ $instruction->updated_at->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
</style>
@stop

@section('js')
<script>
</script>
@stop