@extends('layouts.app')

@section('title', 'Editar Colmena')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    Editar Colmena #{{ $colmena->numero }}
                </div>
                <div class="card-body">
                    @include('colmenas._form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
