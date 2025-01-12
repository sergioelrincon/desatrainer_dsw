@extends('adminlte::page')

@section('title', 'Detalles del Escenario')

@section('content_header')
    <h1 class="m-0 text-dark">Detalles del Escenario</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información del Escenario</h3>
                <div class="card-tools">
                    <a href="{{ route('scenarios.index') }}" class="btn btn-sm btn-info">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <a href="{{ route('scenarios.edit', $scenario->id) }}" class="btn btn-sm btn-warning">
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
                                        <th style="width: 30%">ID</th>
                                        <td>{{ $scenario->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Título</th>
                                        <td>{{ $scenario->title }}</td>
                                    </tr>
                                    <tr>
                                        <th>Categoría</th>
                                        <td>{{ $scenario->category->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Descripción</th>
                                        <td>{{ $scenario->description ?? 'Sin descripción' }}</td>
                                    </tr>
                                    @if($scenario->desaTrainer)
                                        <tr>
                                            <th>DESA Trainer</th>
                                            <td>{{ $scenario->desaTrainer->name }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>Fecha de Creación</th>
                                        <td>{{ $scenario->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Última Actualización</th>
                                        <td>{{ $scenario->updated_at->format('d/m/Y H:i') }}</td>
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