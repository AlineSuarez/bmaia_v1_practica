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

    {{-- COLOR ETIQUETA --}}
    <div class="mb-3">
        <label for="color_etiqueta" class="form-label">Color etiqueta</label>
        <select name="color_etiqueta" id="color_etiqueta" class="form-control" required>
            @php
                $colores = [
                    '#ffc107' => 'Amarillo',
                    '#28a745' => 'Verde',
                    '#17a2b8' => 'Celeste',
                    '#dc3545' => 'Rojo',
                    '#6f42c1' => 'Morado',
                    '#343a40' => 'Negro'
                ];
            @endphp
            @foreach ($colores as $hex => $nombre)
                <option value="{{ $hex }}" {{ (old('color_etiqueta', $colmena->color_etiqueta ?? '') == $hex) ? 'selected' : '' }}>
                    {{ $nombre }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- NUMERO --}}
    <div class="mb-3">
        <label for="numero" class="form-label">Número</label>
        <input type="number" name="numero" class="form-control" 
            value="{{ old('numero', $colmena->numero ?? '') }}" required>
    </div>

    {{-- ESTADO INICIAL --}}
    <div class="mb-3">
        <label for="estado_inicial" class="form-label">Estado inicial</label>
        <input type="text" name="estado_inicial" class="form-control" 
            value="{{ old('estado_inicial', $colmena->estado_inicial ?? '') }}">
    </div>

    {{-- NÚMERO DE MARCOS --}}
    <div class="mb-3">
        <label for="numero_marcos" class="form-label">Número de marcos</label>
        <input type="number" name="numero_marcos" class="form-control" 
            value="{{ old('numero_marcos', $colmena->numero_marcos ?? '') }}">
    </div>

    {{-- OBSERVACIONES --}}
    <div class="mb-3">
        <label for="observaciones" class="form-label">Observaciones</label>
        <textarea name="observaciones" class="form-control" rows="3">{{ old('observaciones', $colmena->observaciones ?? '') }}</textarea>
    </div>

    {{-- BOTONES --}}
    <div class="d-flex justify-content-end">
        <a href="{{ route('colmenas.index', $apiario->id) }}" class="btn btn-secondary me-2">Cancelar</a>
        <button type="submit" class="btn btn-success">
            {{ $isEdit ? 'Guardar Cambios' : 'Crear Colmena' }}
        </button>
    </div>
</form>
