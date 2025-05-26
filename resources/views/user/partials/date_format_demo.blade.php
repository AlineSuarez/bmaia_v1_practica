@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Demo: Selección de Formato de Fecha</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <form id="dateFormatForm"
        action="{{ route('preferences.updateDateFormat') }}"
        method="POST">
    @csrf

    <div class="form-group">
      <label for="date_format">Elige tu formato:</label>
      <select name="date_format"
              id="date_format"
              class="form-control"
              onchange="document.getElementById('dateFormatForm').submit()">
        @foreach($formats as $fmt)
          <option value="{{ $fmt }}"
            {{ $currentFormat === $fmt ? 'selected' : '' }}>
            {{ $fmt }}
          </option>
        @endforeach
      </select>
    </div>
  </form>

  <hr>

  <h2>Previsualización</h2>
  <p><strong>Formato actual (config):</strong> {{ config('app.date_format') }}</p>
  <p><strong>Hoy es:</strong> @date(now())</p>
</div>
@endsection
