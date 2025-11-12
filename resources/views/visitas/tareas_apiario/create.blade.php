@extends('layouts.app')

@section('title', isset($tarea) ? 'Editar Tarea del Apiario' : 'Registrar Tarea del Apiario')

@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('./css/components/home-user/create/task-visita.css') }}">
    </head>

    <div class="tasks-container">
        <header class="tasks-header">
            <div class="header-decoration"></div>
            <h1 class="tasks-title">
                {{ isset($tarea) ? 'Editar Tarea del Apiario' : 'Registrar Tarea del Apiario' }}
            </h1>
            <div class="tasks-subtitle">{{ $apiario->nombre }}</div>
            <div class="header-pattern"></div>
        </header>

        <form method="POST" action="{{ isset($tarea)
        ? route('tareas-apiario.update', ['apiarioId' => $apiario->id, 'id' => $tarea->id])
        : route('tareas-apiario.store', $apiario->id) }}" class="tasks-form">
            @csrf
            @if(isset($tarea))
                @method('PUT')
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <div class="alert-header">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path
                                d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                            <line x1="12" y1="9" x2="12" y2="13" />
                            <line x1="12" y1="17" x2="12.01" y2="17" />
                        </svg>
                        <strong>Se encontraron errores en el formulario:</strong>
                    </div>
                    <ul class="alert-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="form-section task-details">
                <div class="section-header">
                    <div class="section-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 11l3 3L22 4" />
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                        </svg>
                    </div>
                    <h2 class="section-title">Detalles de la Tarea</h2>
                    <div class="section-decoration"></div>
                </div>

                <div class="field-group task-grid">
                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 7h16M4 12h16M4 17h16" />
                                </svg>
                            </span>
                            Categoría de Tarea
                        </label>
                        <div class="select-wrapper">
                            <select name="categoria_tarea" class="field-select" required>
                                <option value="">Seleccione una categoría...</option>
                                @foreach (['Inspección', 'Sanidad', 'Alimentación', 'Manejo', 'Otro'] as $opcion)
                                    <option value="{{ $opcion }}" {{ (isset($tarea) && $tarea->categoria_tarea == $opcion) ? 'selected' : '' }}>
                                        {{ $opcion }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="select-arrow">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </span>
                        </div>
                        <span class="field-helper">Seleccione el tipo de tarea a realizar</span>
                    </div>

                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                </svg>
                            </span>
                            Tarea Específica
                        </label>
                        <input type="text" name="tarea_especifica" class="field-input"
                            placeholder="Describa la tarea específica..."
                            value="{{ old('tarea_especifica', $tarea->tarea_especifica ?? '') }}">
                        <span class="field-helper">Detalle específico de la actividad a realizar</span>
                    </div>

        <!--
        <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="9 11 12 14 22 4" />
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                                </svg>
                            </span>
                            Acción Realizada
                        </label>
                        <input type="text" name="accion_realizada" class="field-input"
                            placeholder="Describa la acción realizada..."
                            value="{{ old('accion_realizada', $tarea->accion_realizada ?? '') }}">
                        <span class="field-helper">Resultado o acción concreta ejecutada</span>
                    </div>            
        -->
                </div>
            </section>

            <section class="form-section task-timeline">
                <div class="section-header">
                    <div class="section-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                    </div>
                    <h2 class="section-title">Cronología de la Tarea</h2>
                    <div class="section-decoration"></div>
                </div>

                <div class="field-group timeline-grid">
                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                            </span>
                            Fecha de Inicio
                        </label>
                        <input type="date" name="fecha_inicio" class="field-input date-input"
                            value="{{ old('fecha_inicio', $tarea->fecha_inicio ?? '') }}">
                        <span class="field-helper">Fecha de inicio de la tarea</span>
                    </div>

                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                            </span>
                            Fecha de Término
                        </label>
                        <input type="date" name="fecha_termino" class="field-input date-input"
                            value="{{ old('fecha_termino', $tarea->fecha_termino ?? '') }}">
                        <span class="field-helper">Fecha de finalización de la tarea</span>
                    </div>

                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                    <line x1="16" y1="2" x2="16" y2="6" />
                                    <line x1="8" y1="2" x2="8" y2="6" />
                                    <line x1="3" y1="10" x2="21" y2="10" />
                                </svg>
                            </span>
                            Próximo Seguimiento
                        </label>
                        <input type="date" name="proximo_seguimiento" class="field-input date-input"
                            value="{{ old('proximo_seguimiento', $tarea->proximo_seguimiento ?? '') }}">
                        <span class="field-helper">Seleccione la fecha del próximo seguimiento</span>
                    </div>

                </div>
            </section>

            <section class="form-section task-observations">
                <div class="section-header">
                    <div class="section-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14,2 14,8 20,8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                        </svg>
                    </div>
                    <h2 class="section-title">Observaciones Adicionales</h2>
                    <div class="section-decoration"></div>
                </div>

                <div class="field-group">
                    <div class="form-field field-full-width">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 20h9" />
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
                                </svg>
                            </span>
                            Observaciones
                        </label>
                        <textarea name="observaciones" class="field-input field-textarea" rows="5"
                            placeholder="Ingrese observaciones adicionales sobre la tarea...">{{ old('observaciones', $tarea->observaciones ?? '') }}</textarea>
                        <span class="field-helper">Detalles adicionales, notas o comentarios relevantes</span>
                    </div>
                </div>
            </section>

            <footer class="form-actions">
                <div class="actions-container">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        <span class="btn-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 12H5m7-7l-7 7 7 7" />
                            </svg>
                        </span>
                        Volver
                    </a>
                    <button type="reset" class="btn btn-outline">
                        <span class="btn-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="1 4 1 10 7 10" />
                                <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10" />
                            </svg>
                        </span>
                        Limpiar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span class="btn-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                <polyline points="17,21 17,13 7,13 7,21" />
                                <polyline points="7,3 7,8 15,8" />
                            </svg>
                        </span>
                        {{ isset($tarea) ? 'Actualizar Tarea' : 'Guardar Registro' }}
                    </button>
                </div>
            </footer>
        </form>
    </div>
@endsection