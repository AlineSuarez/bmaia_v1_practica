@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>{{ __('apiarios.my_apiaries') }}</h1>

    <div class="card mt-4">
        <div class="card-body">
            <p><strong>{{ __('apiarios.name') }}:</strong> {{ __('apiarios.test_name', [], 'es_CL') }}</p>
            <p><strong>{{ __('apiarios.production_season') }}:</strong> 2025–2026</p>
            <p><strong>{{ __('apiarios.region') }}:</strong> Coquimbo</p>
            <p><strong>{{ __('apiarios.commune') }}:</strong> Ovalle</p>
            <p><strong>{{ __('apiarios.sag_code') }}:</strong> 123456</p>
        </div>
    </div>

    <a href="{{ route('preferences.index') }}" class="btn btn-primary mt-3">
        {{ __('apiarios.edit_apiary') }}
    </a>
</div>
@endsection
