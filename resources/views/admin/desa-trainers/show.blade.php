@extends('adminlte::page')

@section('title', 'DESA Trainer')

@section('content')
    <livewire:desa-trainer-show :desaTrainer="$desaTrainer" />
@endsection