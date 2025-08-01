@php
    $isEdit = isset($colmena);
@endphp

<form method="POST" action="{{ $isEdit 
    ? route('colmenas.update', [$apiario->id, $colmena->id]) 
    : route('colmenas.store', $apiario->id) }}">
    
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    {{-- NOMBRE --}}
    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control"
        value="{{ old('nombre', $colmena->nombre ?? '') }}" maxlength="255">
    </div>

    {{-- NUMERO --}}
    <div class="mb-3">
        <label for="numero" class="form-label">Número</label>
        <input type="number" name="numero" class="form-control" 
            value="{{ old('numero', $colmena->numero ?? '') }}" required>
    </div>

    {{-- BOTONES --}}
    <div class="d-flex justify-content-end">
        <a href="{{ route('colmenas.index', $apiario->id) }}" class="btn btn-secondary me-2">Cancelar</a>
        <button type="submit" class="btn btn-success">
            {{ $isEdit ? 'Guardar Cambios' : 'Crear Colmena' }}
        </button>
    </div>
</form>
