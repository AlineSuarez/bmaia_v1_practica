@extends('layouts.app')

@section('title', 'Registro de Estado Nutricional')

@section('content')

  <head>
    <link rel="stylesheet" href="{{ asset('./css/components/home-user/create/nutritional-status.css') }}">
  </head>

  <div class="expert-system-container">
    <div class="pcc3-content">

    @php
    $visita = $visita ?? null;
    $estado = $estado ?? null;
    @endphp

    {{-- Contenido de PCC3 --}}
    <div class="pcc3-main">
      <form action="{{ route('visitas.store3', $apiario) }}" method="POST">
      @csrf
      @if(isset($visita))
      <input type="hidden" name="visita_id" value="{{ $visita->id }}">
    @endif

      @if($errors->any())
      <div class="alert alert-danger">
        <div class="alert-header">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Se encontraron errores en el formulario:</strong>
        </div>
        <ul>
        @foreach($errors->all() as $e)
      <li>{{ $e }}</li>
      @endforeach
        </ul>
      </div>
    @endif

      <div class="step-card">
        <div class="step-header">
        <div class="step-icon">
          <i class="fas fa-seedling"></i>
        </div>
        <div class="step-header-content">
          <h4>Estado Nutricional</h4>
          <small class="text-muted">Evaluación completa de la alimentación y reservas nutricionales de la
          colmena</small>
        </div>
        <div class="step-badge">
          <small>Punto Crítico de Control</small>
        </div>
        </div>

        {{-- Sección: Configuración Nutricional --}}
        <div class="nutrition-section">
        <div class="section-title">
          <h5>
          <i class="fas fa-bullseye"></i>
          Configuración Nutricional
          </h5>
          <small class="text-muted">Defina el objetivo y tipo de alimentación a aplicar</small>
        </div>

        <div class="field-group">
          <div class="form-field">
          <label class="form-label">
            <i class="fas fa-target"></i>
            Objetivo de Alimentación
          </label>
          <select name="objetivo" class="form-select" required>
            <option value="" disabled selected>-- Elija el objetivo nutricional --</option>
            <option value="estimulacion" {{ old('objetivo', $estado->objetivo ?? '') == 'estimulacion' ? 'selected' : '' }}>
            Estimulación - Desarrollo y crecimiento activo
            </option>
            <option value="mantencion" {{ old('objetivo', $estado->objetivo ?? '') == 'mantencion' ? 'selected' : '' }}>
            Mantención - Preservación del estado actual
            </option>
          </select>
          <small class="form-text text-muted">Seleccione según las necesidades de la colmena</small>
          </div>

          <div class="form-field">
          <label class="form-label">
            <i class="fas fa-apple-alt"></i>
            Tipo de Alimentación
          </label>
          <input type="text" name="tipo_alimentacion"
            value="{{ old('tipo_alimentacion', $estado->tipo_alimentacion ?? '') }}" class="form-control"
            placeholder="Ej: Jarabe de azúcar, suplemento proteico, polen natural..." required />
          <small class="form-text text-muted">Especifique el tipo de alimento proporcionado</small>
          </div>
        </div>
        </div>

        {{-- Sección: Detalles de Aplicación --}}
        <div class="application-section">
        <div class="section-title">
          <h5>
          <i class="fas fa-calendar-check"></i>
          Detalles de Aplicación
          </h5>
          <small class="text-muted">Registre la información específica del tratamiento aplicado</small>
        </div>

        <div class="field-group">
          <div class="form-field">
          <label class="form-label">
            <i class="fas fa-calendar-day"></i>
            Fecha de Aplicación
          </label>
          <input type="date" name="fecha_aplicacion_insumo_utilizado"
            value="{{ old('fecha_aplicacion_insumo_utilizado', optional($estado)->fecha_aplicacion?->format('Y-m-d')) }}"
            class="form-control" required />
          <small class="form-text text-muted">Fecha cuando se aplicó el tratamiento nutricional</small>
          </div>

          <div class="form-field">
          <label class="form-label">
            <i class="fas fa-box"></i>
            Insumo Utilizado
          </label>
          <input type="text" name="insumo_utilizado"
            value="{{ old('insumo_utilizado', $estado->insumo_utilizado ?? '') }}" class="form-control"
            placeholder="Nombre comercial o descripción del producto utilizado..." />
          <small class="form-text text-muted">Identifique el producto específico aplicado</small>
          </div>

          <div class="form-field">
          <label class="form-label">
            <i class="fas fa-prescription-bottle"></i>
            Dosificación
          </label>
          <input type="text" name="dosificacion" value="{{ old('dosificacion', $estado->dosifiacion ?? '') }}"
            class="form-control" placeholder="Cantidad y frecuencia (ej: 500ml cada 3 días)..." />
          <small class="form-text text-muted">Especifique cantidad exacta y frecuencia de aplicación</small>
          </div>

          <div class="form-field">
          <label class="form-label">
            <i class="fas fa-tools"></i>
            Método de Aplicación
          </label>
          <input type="text" name="metodo_utilizado"
            value="{{ old('metodo_utilizado', $estado->metodo_utilizado ?? '') }}" class="form-control"
            placeholder="Método utilizado (alimentador interno, fumigación, etc.)..." />
          <small class="form-text text-muted">Describa cómo se aplicó el tratamiento</small>
          </div>
        </div>
        </div>

        {{-- Acciones del Formulario --}}
        <div class="form-actions">
        <div class="form-actions-row">
          <a href="{{ url()->previous() }}" class="btn btn-secondary">
          <i class="fas fa-arrow-left"></i>
          Volver
          </a>
          <div class="action-buttons">
          <button type="reset" class="btn btn-outline-secondary">
            <i class="fas fa-undo"></i>
            Limpiar Formulario
          </button>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i>
            Guardar Estado PCC3
          </button>
          </div>
        </div>
        </div>
      </div>
      </form>
    </div>

    </div>
  </div>
@endsection