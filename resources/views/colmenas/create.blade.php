@extends('layouts.app')

@section('title', 'Crear Colmena')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Registrar nueva colmena para el apiario: <strong>{{ $apiario->nombre }}</strong></h5>
                </div>
                <div class="card-body">
                    @include('colmenas._form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
