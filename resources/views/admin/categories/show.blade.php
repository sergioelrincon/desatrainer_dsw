@extends('adminlte::page')

@section('title', 'Detalles de la Categoría')

@section('content_header')
    <h1 class="m-0 text-dark">Detalles de la Categoría</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información de la Categoría</h3>
                <div class="card-tools">
                    <a href="{{ route('categories.index') }}" class="btn btn-sm btn-info">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-warning">
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
                                        <td>{{ $category->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nombre</th>
                                        <td>{{ $category->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Descripción</th>
                                        <td>{{ $category->description ?? 'Sin descripción' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Fecha de Creación</th>
                                        <td>{{ $category->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Última Actualización</th>
                                        <td>{{ $category->updated_at->format('d/m/Y H:i') }}</td>
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