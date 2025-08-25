@extends('layouts.app')

@section('title', 'Registro de Calidad de Reina')

@section('content')
    <head>
        <link rel="stylesheet" href="{{ asset('./css/components/home-user/create/queen-quality.css') }}">
    </head>

    <div class="queen-quality-container">
        <header class="queen-header">
            <div class="header-decoration"></div>
            <h1 class="queen-title">Registro de Calidad de Reina</h1>
            <div class="queen-subtitle">Evaluación completa del estado y características de la reina</div>
            <div class="header-pattern"></div>
        </header>

        <form action="{{ route('visitas.store4', $apiario) }}" method="POST" class="queen-form">
            @csrf
            @if(isset($visita) && $visita->exists)
                <input type="hidden" name="visita_id" value="{{ $visita->id }}">
            @endif
            @if(isset($calidadReina) && $calidadReina->exists)
                <input type="hidden" name="calidad_reina_id" value="{{ $calidadReina->id }}">
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <div class="alert-header">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/>
                            <line x1="12" y1="17" x2="12.01" y2="17"/>
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

            <section class="form-section queen-characteristics">
                <div class="section-header">
                    <div class="section-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <h2 class="section-title">Características de la Reina</h2>
                    <div class="section-decoration"></div>
                </div>

                <div class="field-group queen-grid">
                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </span>
                            Postura de la Reina
                        </label>
                        <div class="select-wrapper">
                            <select name="calidad_reina[postura_reina]" class="field-select" required>
                                <option value="" disabled selected>Seleccione el tipo de postura</option>
                                <option value="Irregular" {{ old('calidad_reina.postura_reina', $calidadReina->postura_reina ?? '') == 'Irregular' ? 'selected' : '' }}>
                                    Irregular - Postura dispersa e inconsistente
                                </option>
                                <option value="Regular" {{ old('calidad_reina.postura_reina', $calidadReina->postura_reina ?? '') == 'Regular' ? 'selected' : '' }}>
                                    Regular - Postura constante y ordenada
                                </option>
                                <option value="Compacta" {{ old('calidad_reina.postura_reina', $calidadReina->postura_reina ?? '') == 'Compacta' ? 'selected' : '' }}>
                                    Compacta - Postura densa y uniforme
                                </option>
                            </select>
                            <div class="select-arrow">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 9l6 6 6-6"/>
                                </svg>
                            </div>
                        </div>
                        <span class="field-helper">Evalúe el patrón de postura de huevos</span>
                    </div>

                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                            </span>
                            Estado de la Cría
                        </label>
                        <div class="select-wrapper">
                            <select name="calidad_reina[estado_cria]" class="field-select" required>
                                <option value="" disabled selected>Seleccione el estado de la cría</option>
                                <option value="Compacta" {{ old('calidad_reina.estado_cria', $calidadReina->estado_cria ?? '') == 'Compacta' ? 'selected' : '' }}>
                                    Compacta - Cría uniforme y densa
                                </option>
                                <option value="Semisaltada" {{ old('calidad_reina.estado_cria', $calidadReina->estado_cria ?? '') == 'Semisaltada' ? 'selected' : '' }}>
                                    Semisaltada - Algunas celdas vacías
                                </option>
                                <option value="Saltada" {{ old('calidad_reina.estado_cria', $calidadReina->estado_cria ?? '') == 'Saltada' ? 'selected' : '' }}>
                                    Saltada - Muchas celdas vacías
                                </option>
                            </select>
                            <div class="select-arrow">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 9l6 6 6-6"/>
                                </svg>
                            </div>
                        </div>
                        <span class="field-helper">Observe la continuidad del panal de cría</span>
                    </div>

                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </span>
                            Postura de Zánganos
                        </label>
                        <div class="select-wrapper">
                            <select name="calidad_reina[postura_zanganos]" class="field-select">
                                <option value="" disabled selected>Seleccione el nivel de postura</option>
                                <option value="Normal" {{ old('calidad_reina.postura_zanganos', $calidadReina->postura_zanganos ?? '') == 'Normal' ? 'selected' : '' }}>
                                    Normal - Cantidad apropiada de zánganos
                                </option>
                                <option value="Alta" {{ old('calidad_reina.postura_zanganos', $calidadReina->postura_zanganos ?? '') == 'Alta' ? 'selected' : '' }}>
                                    Alta - Exceso de postura de zánganos
                                </option>
                            </select>
                            <div class="select-arrow">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 9l6 6 6-6"/>
                                </svg>
                            </div>
                        </div>
                        <span class="field-helper">Evalúe la proporción de cría de zánganos</span>
                    </div>
                </div>
            </section>

            <section class="form-section queen-genetics">
                <div class="section-header">
                    <div class="section-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14,2 14,8 20,8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10,9 9,9 8,9"/>
                        </svg>
                    </div>
                    <h2 class="section-title">Información Genética</h2>
                    <div class="section-decoration"></div>
                </div>

                <div class="field-group genetics-grid">
                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </span>
                            Origen de la Reina
                        </label>
                        <div class="select-wrapper">
                            <select name="calidad_reina[origen_reina]" class="field-select">
                                <option value="" disabled selected>Seleccione el origen</option>
                                <option value="natural" {{ old('calidad_reina.origen_reina', $calidadReina->origen_reina ?? '') == 'natural' ? 'selected' : '' }}>
                                    Natural - Criada por la colonia
                                </option>
                                <option value="comprada" {{ old('calidad_reina.origen_reina', $calidadReina->origen_reina ?? '') == 'comprada' ? 'selected' : '' }}>
                                    Comprada - Adquirida externamente
                                </option>
                                <option value="fecundada" {{ old('calidad_reina.origen_reina', $calidadReina->origen_reina ?? '') == 'fecundada' ? 'selected' : '' }}>
                                    Fecundada - Inseminación controlada
                                </option>
                                <option value="virgen" {{ old('calidad_reina.origen_reina', $calidadReina->origen_reina ?? '') == 'virgen' ? 'selected' : '' }}>
                                    Virgen - Sin fecundar
                                </option>
                            </select>
                            <div class="select-arrow">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 9l6 6 6-6"/>
                                </svg>
                            </div>
                        </div>
                        <span class="field-helper">Indique cómo se obtuvo la reina</span>
                    </div>

                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                            </span>
                            Raza
                        </label>
                        <input type="text" 
                               name="calidad_reina[raza]" 
                               class="field-input text-input"
                               placeholder="Ej: Italiana, Carníola, Buckfast..."
                               value="{{ old('calidad_reina.raza', $calidadReina->raza ?? '') }}">
                        <span class="field-helper">Especifique la raza de la reina</span>
                    </div>

                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 3h18v18H3zM9 9h6v6H9z"/>
                                </svg>
                            </span>
                            Línea Genética
                        </label>
                        <input type="text" 
                               name="calidad_reina[linea_genetica]" 
                               class="field-input text-input"
                               placeholder="Línea genética o código de cría..."
                               value="{{ old('calidad_reina.linea_genetica', $calidadReina->linea_genetica ?? '') }}">
                        <span class="field-helper">Identifique la línea genética específica</span>
                    </div>
                </div>
            </section>

            <section class="form-section queen-status">
                <div class="section-header">
                    <div class="section-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <h2 class="section-title">Estado y Cronología</h2>
                    <div class="section-decoration"></div>
                </div>

                <div class="field-group status-grid">
                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                            </span>
                            Fecha de Introducción
                        </label>
                        <input type="date" 
                               name="calidad_reina[fecha_introduccion]" 
                               class="field-input date-input"
                               value="{{ old('calidad_reina.fecha_introduccion', $calidadReina->fecha_introduccion ?? '') }}">
                        <span class="field-helper">Fecha cuando se introdujo la reina en la colmena</span>
                    </div>

                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12,6 12,12 16,14"/>
                                </svg>
                            </span>
                            Estado Actual
                        </label>
                        <div class="select-wrapper">
                            <select name="calidad_reina[estado_actual]" class="field-select">
                                <option value="" disabled selected>Seleccione el estado actual</option>
                                <option value="activa" {{ old('calidad_reina.estado_actual', $calidadReina->estado_actual ?? '') == 'activa' ? 'selected' : '' }}>
                                    Activa - Funcionando normalmente
                                </option>
                                <option value="fallida" {{ old('calidad_reina.estado_actual', $calidadReina->estado_actual ?? '') == 'fallida' ? 'selected' : '' }}>
                                    Fallida - No cumple con expectativas
                                </option>
                                <option value="reemplazada" {{ old('calidad_reina.estado_actual', $calidadReina->estado_actual ?? '') == 'reemplazada' ? 'selected' : '' }}>
                                    Reemplazada - Ya fue sustituida
                                </option>
                            </select>
                            <div class="select-arrow">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 9l6 6 6-6"/>
                                </svg>
                            </div>
                        </div>
                        <span class="field-helper">Estado actual de la reina en la colmena</span>
                    </div>
                </div>
            </section>

            <section class="form-section queen-replacements">
                <div class="section-header">
                    <div class="section-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="1 4 1 10 7 10"/>
                            <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>
                        </svg>
                    </div>
                    <h2 class="section-title">Historial de Reemplazos</h2>
                    <div class="section-decoration"></div>
                </div>

                <div class="replacements-container" id="reemplazos-container">
                    @php
                        $reemplazos = old('calidad_reina.reemplazos_realizados', $calidadReina->reemplazos_realizados ?? []);
                        
                        if (is_string($reemplazos)) {
                            $decodedReemplazos = json_decode($reemplazos, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedReemplazos)) {
                                $reemplazos = $decodedReemplazos;
                            } else {
                                $reemplazos = [];
                            }
                        } elseif ($reemplazos instanceof \Illuminate\Support\Collection) {
                            $reemplazos = $reemplazos->toArray();
                        } elseif (!is_array($reemplazos)) {
                            $reemplazos = [];
                        }

                        if (empty($reemplazos)) {
                            $reemplazos = [[]];
                        }
                    @endphp

                    @foreach($reemplazos as $index => $reemplazo)
                        <div class="replacement-item">
                            <div class="replacement-fields">
                                <div class="form-field">
                                    <label class="field-label">
                                        <span class="label-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                                <line x1="16" y1="2" x2="16" y2="6"/>
                                                <line x1="8" y1="2" x2="8" y2="6"/>
                                                <line x1="3" y1="10" x2="21" y2="10"/>
                                            </svg>
                                        </span>
                                        Fecha de Reemplazo
                                    </label>
                                    <input type="date"
                                           name="calidad_reina[reemplazos_realizados][{{ $index }}][fecha]"
                                           value="{{ old('calidad_reina.reemplazos_realizados.' . $index . '.fecha', $reemplazo['fecha'] ?? '') }}"
                                           class="field-input date-input">
                                </div>

                                <div class="form-field">
                                    <label class="field-label">
                                        <span class="label-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                <polyline points="14,2 14,8 20,8"/>
                                                <line x1="16" y1="13" x2="8" y2="13"/>
                                                <line x1="16" y1="17" x2="8" y2="17"/>
                                                <polyline points="10,9 9,9 8,9"/>
                                            </svg>
                                        </span>
                                        Motivo del Reemplazo
                                    </label>
                                    <input type="text"
                                           name="calidad_reina[reemplazos_realizados][{{ $index }}][motivo]"
                                           value="{{ old('calidad_reina.reemplazos_realizados.' . $index . '.motivo', $reemplazo['motivo'] ?? '') }}"
                                           class="field-input text-input"
                                           placeholder="Describa el motivo del reemplazo...">
                                </div>
                            </div>

                            <div class="replacement-actions">
                                <button type="button" 
                                        class="btn btn-remove-replacement {{ count($reemplazos) === 1 && $index === 0 ? 'disabled' : '' }}"
                                        {{ count($reemplazos) === 1 && $index === 0 ? 'disabled' : '' }}>
                                    <span class="btn-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="5" y1="12" x2="19" y2="12"/>
                                        </svg>
                                    </span>
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="replacement-controls">
                    <button type="button" class="btn btn-add-replacement">
                        <span class="btn-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </span>
                        Agregar Reemplazo
                    </button>
                </div>
            </section>

            <footer class="form-actions">
                <div class="actions-container">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        <span class="btn-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 12H5m7-7l-7 7 7 7"/>
                            </svg>
                        </span>
                        Volver
                    </a>
                    <button type="reset" class="btn btn-outline">
                        <span class="btn-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="1 4 1 10 7 10"/>
                                <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>
                            </svg>
                        </span>
                        Limpiar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span class="btn-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17,21 17,13 7,13 7,21"/>
                                <polyline points="7,3 7,8 15,8"/>
                            </svg>
                        </span>
                        Guardar Calidad de Reina
                    </button>
                </div>
            </footer>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reemplazosContainer = document.getElementById('reemplazos-container');
            const addButton = document.querySelector('.btn-add-replacement');
            let replacementIndex = reemplazosContainer.children.length;

            function updateRemoveButtons() {
                const removeButtons = reemplazosContainer.querySelectorAll('.btn-remove-replacement');
                if (removeButtons.length === 1) {
                    removeButtons[0].setAttribute('disabled', 'true');
                    removeButtons[0].classList.add('disabled');
                } else {
                    removeButtons.forEach(button => {
                        button.removeAttribute('disabled');
                        button.classList.remove('disabled');
                    });
                }
            }

            function addReplacementField() {
                const newRow = document.createElement('div');
                newRow.classList.add('replacement-item');
                newRow.innerHTML = `
                    <div class="replacement-fields">
                        <div class="form-field">
                            <label class="field-label">
                                <span class="label-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                </span>
                                Fecha de Reemplazo
                            </label>
                            <input type="date"
                                   name="calidad_reina[reemplazos_realizados][${replacementIndex}][fecha]"
                                   class="field-input date-input">
                        </div>
                        <div class="form-field">
                            <label class="field-label">
                                <span class="label-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14,2 14,8 20,8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                        <polyline points="10,9 9,9 8,9"/>
                                    </svg>
                                </span>
                                Motivo del Reemplazo
                            </label>
                            <input type="text"
                                   name="calidad_reina[reemplazos_realizados][${replacementIndex}][motivo]"
                                   class="field-input text-input"
                                   placeholder="Describa el motivo del reemplazo...">
                        </div>
                    </div>
                    <div class="replacement-actions">
                        <button type="button" class="btn btn-remove-replacement">
                            <span class="btn-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                            </span>
                            Eliminar
                        </button>
                    </div>
                `;
                reemplazosContainer.appendChild(newRow);
                replacementIndex++;
                updateRemoveButtons();
            }

            addButton.addEventListener('click', addReplacementField);

            reemplazosContainer.addEventListener('click', function(event) {
                if (event.target.classList.contains('btn-remove-replacement') || event.target.closest('.btn-remove-replacement')) {
                    const button = event.target.closest('.btn-remove-replacement');
                    if (reemplazosContainer.children.length > 1) {
                        button.closest('.replacement-item').remove();
                        reindexReplacementFields();
                        updateRemoveButtons();
                    }
                }
            });

            function reindexReplacementFields() {
                const replacementItems = reemplazosContainer.querySelectorAll('.replacement-item');
                replacementItems.forEach((item, newIndex) => {
                    item.querySelectorAll('input').forEach(input => {
                        const oldName = input.name;
                        input.name = oldName.replace(/\[reemplazos_realizados\]\[\d+\]/, `[reemplazos_realizados][${newIndex}]`);
                    });
                });
                replacementIndex = replacementItems.length;
            }

            updateRemoveButtons();
        });
    </script>
    @endpush
@endsection