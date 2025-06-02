@extends('layouts.app')

@section('content')
    <div class="inspection-container">
        <header class="inspection-header">
            <div class="header-decoration"></div>
            <h1 class="inspection-title">Registro de Inspección del Apiario</h1>
            <div class="inspection-subtitle">Complete todos los campos para registrar la inspección</div>
            <div class="header-pattern"></div>
            <link rel="stylesheet" href="{{ asset('./css/components/home-user/create/inspections.css') }}">
        </header>

        <form action="{{ route('apiarios.inspeccion-apiario.store', $apiario) }}" method="POST" class="inspection-form">
            @csrf

            <!-- Información General -->
            <section class="form-section general-info">
                <div class="section-header">
                    <div class="section-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                    </div>
                    <h2 class="section-title">Información General</h2>
                    <div class="section-decoration"></div>
                        </div>
                        <div class="field-group">
                            <div class="form-field">
                                <label for="fecha_inspeccion" class="field-label">
                                    <span class="label-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10" />
                                            <polyline points="12,6 12,12 16,14" />
                                        </svg>
                                    </span>
                                    Fecha de Inspección
                                </label>
                                <input type="date" id="fecha_inspeccion" name="fecha_inspeccion" class="field-input date-input"
                                    required value="{{ old('fecha_inspeccion', date('Y-m-d')) }}">
                                <span class="field-helper">Seleccione la fecha de la inspección</span>
                            </div>

                            <div class="form-field">
                                <label for="nombre_revisor_apiario" class="field-label">
                                    <span class="label-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                            <circle cx="12" cy="7" r="4" />
                                        </svg>
                                    </span>
                                    Nombre del Revisor
                                </label>
                                <input type="text" id="nombre_revisor_apiario" name="nombre_revisor_apiario"
                                    class="field-input text-input" placeholder="Ingrese el nombre completo del revisor"
                                    value="{{ old('nombre_revisor_apiario') }}" required>
                                <span class="field-helper">Persona responsable de la inspección</span>
                            </div>
                        </div>
                    </section>

                    <!-- Estado de Colmenas -->
                    <section class="form-section colmenas-info">
                        <div class="section-header">
                            <div class="section-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                    <polyline points="9,22 9,12 15,12 15,22" />
                                </svg>
                            </div>
                            <h2 class="section-title">Estado de las Colmenas</h2>
                            <div class="section-decoration"></div>
                        </div>
                        <div class="colmenas-grid">
                            <div class="colmena-card totales">
                                <div class="card-header">
                                    <div class="card-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="18" y1="20" x2="18" y2="10" />
                                            <line x1="12" y1="20" x2="12" y2="4" />
                                            <line x1="6" y1="20" x2="6" y2="14" />
                                        </svg>
                                    </div>
                                    <span class="card-badge total">Total</span>
                                </div>
                                <div class="form-field">
                                    <label for="num_colmenas_totales" class="field-label">Colmenas Totales</label>
                                    <input type="number" id="num_colmenas_totales" name="num_colmenas_totales"
                                        class="field-input number-input" min="0" placeholder="0"
                                        value="{{ old('num_colmenas_totales') }}" required>
                                </div>
                            </div>

                            <div class="colmena-card activas">
                                <div class="card-header">
                                    <div class="card-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="20,6 9,17 4,12" />
                                        </svg>
                                    </div>
                                    <span class="card-badge active">Activo</span>
                                </div>
                                <div class="form-field">
                                    <label for="num_colmenas_activas" class="field-label">Colmenas Activas</label>
                                    <input type="number" id="num_colmenas_activas" name="num_colmenas_activas"
                                        class="field-input number-input" min="0" placeholder="0"
                                        value="{{ old('num_colmenas_activas') }}" required>
                                </div>
                            </div>

                            <div class="colmena-card enfermas">
                                <div class="card-header">
                                    <div class="card-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                                        </svg>
                                    </div>
                                    <span class="card-badge sick">Enfermo</span>
                                </div>
                                <div class="form-field">
                                    <label for="num_colmenas_enfermas" class="field-label">Colmenas Enfermas</label>
                                    <input type="number" id="num_colmenas_enfermas" name="num_colmenas_enfermas"
                                        class="field-input number-input" min="0" placeholder="0"
                                        value="{{ old('num_colmenas_enfermas') }}" required>
                                </div>
                            </div>

                            <div class="colmena-card muertas">
                                <div class="card-header">
                                    <div class="card-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10" />
                                            <line x1="15" y1="9" x2="9" y2="15" />
                                            <line x1="9" y1="9" x2="15" y2="15" />
                                        </svg>
                                    </div>
                                    <span class="card-badge dead">Muerto</span>
                                </div>
                                <div class="form-field">
                                    <label for="num_colmenas_muertas" class="field-label">Colmenas Muertas</label>
                                    <input type="number" id="num_colmenas_muertas" name="num_colmenas_muertas"
                                        class="field-input number-input" min="0" placeholder="0"
                                        value="{{ old('num_colmenas_muertas') }}" required>
                                </div>
                            </div>

                            <div class="colmena-card inspeccionadas">
                                <div class="card-header">
                                    <div class="card-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="11" cy="11" r="8" />
                                            <path d="M21 21l-4.35-4.35" />
                                        </svg>
                                    </div>
                                    <span class="card-badge inspected">Inspeccionado</span>
                                </div>
                                <div class="form-field">
                                    <label for="num_colmenas_inspeccionadas" class="field-label">Colmenas Inspeccionadas</label>
                                    <input type="number" id="num_colmenas_inspeccionadas" name="num_colmenas_inspeccionadas"
                                        class="field-input number-input" min="0" placeholder="0"
                                        value="{{ old('num_colmenas_inspeccionadas') }}" required>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Condiciones Ambientales -->
                    <section class="form-section environmental-info">
                        <div class="section-header">
                            <div class="section-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2v1" />
                                    <path d="M12 21v1" />
                                    <path d="M4.22 4.22l.707.707" />
                                    <path d="M18.364 18.364l.707.707" />
                                    <path d="M1 12h1" />
                                    <path d="M22 12h1" />
                                    <path d="M4.22 19.78l.707-.707" />
                                    <path d="M18.364 5.636l.707-.707" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </div>
                            <h2 class="section-title">Condiciones Ambientales</h2>
                            <div class="section-decoration"></div>
                        </div>
                        <div class="field-group">
                            <div class="form-field select-field">
                                <label for="flujo_nectar_polen" class="field-label">
                                    <span class="label-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path
                                                d="M12 2a3 3 0 0 0-3 3c0 1.5-1.5 3-3 3s-3 1.5-3 3 1.5 3 3 3c1.5 0 3 1.5 3 3a3 3 0 0 0 6 0c0-1.5 1.5-3 3-3s3-1.5 3-3-1.5-3-3-3c-1.5 0-3-1.5-3-3a3 3 0 0 0-3-3" />
                                        </svg>
                                    </span>
                                    Flujo de Néctar / Polen
                                </label>
                                <div class="select-wrapper">
                                    <select id="flujo_nectar_polen" name="flujo_nectar_polen" class="field-select" required>
                                        <option value="">Seleccione el estado del flujo</option>
                                        <option value="abundante" {{ old('flujo_nectar_polen') == 'abundante' ? 'selected' : '' }}>
                                            Abundante - Excelente disponibilidad
                                        </option>
                                        <option value="regular" {{ old('flujo_nectar_polen') == 'regular' ? 'selected' : '' }}>
                                            Regular - Disponibilidad moderada
                                        </option>
                                        <option value="deficiente" {{ old('flujo_nectar_polen') == 'deficiente' ? 'selected' : '' }}>
                                            Deficiente - Baja disponibilidad
                                        </option>
                                    </select>
                                    <div class="select-arrow">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M6 9l6 6 6-6" />
                                        </svg>
                                    </div>
                                </div>
                                <span class="field-helper">Evalúe la disponibilidad de recursos florales</span>
                            </div>
                        </div>
                    </section>

                    <!-- Observaciones de Salud -->
                    <section class="form-section health-info">
                        <div class="section-header">
                            <div class="section-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="3" />
                                    <path d="M12 1v6m0 6v6" />
                                    <path d="M1 12h6m6 0h6" />
                                </svg>
                            </div>
                            <h2 class="section-title">Observaciones de Salud</h2>
                            <div class="section-decoration"></div>
                        </div>
                        <div class="field-group health-grid">
                            <div class="form-field">
                                <label for="sospecha_enfermedad" class="field-label">
                                    <span class="label-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path
                                                d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                                            <line x1="12" y1="9" x2="12" y2="13" />
                                            <line x1="12" y1="17" x2="12.01" y2="17" />
                                        </svg>
                                    </span>
                                    Sospecha de Enfermedad
                                </label>
                                <input type="text" id="sospecha_enfermedad" name="sospecha_enfermedad"
                                    class="field-input text-input" placeholder="Describa cualquier signo de enfermedad observado"
                                    value="{{ old('sospecha_enfermedad') }}">
                                <span class="field-helper">Opcional: Indique síntomas o signos anómalos</span>
                            </div>

                            <div class="form-field textarea-field">
                                <label for="observaciones" class="field-label">
                                    <span class="label-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                            <polyline points="14,2 14,8 20,8" />
                                            <line x1="16" y1="13" x2="8" y2="13" />
                                            <line x1="16" y1="17" x2="8" y2="17" />
                                            <polyline points="10,9 9,9 8,9" />
                                        </svg>
                                    </span>
                                    Observaciones Generales
                                </label>
                                <textarea id="observaciones" name="observaciones" class="field-textarea" rows="3"
                                    placeholder="Ingrese observaciones adicionales, comportamiento de las abejas, condiciones climáticas, etc.">{{ old('observaciones') }}</textarea>
                                <span class="field-helper">Anote cualquier observación relevante sobre la inspección</span>
                            </div>
                        </div>
                    </section>

                    <!-- Acciones del Formulario -->
                    <footer class="form-actions">
                        <div class="actions-container">
                            <a href="{{ route('visita.index', $apiario) }}" class="btn btn-secondary">
                                <span class="btn-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M19 12H5m7-7l-7 7 7 7" />
                                    </svg>
                                </span>
                                Cancelar
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
                                Guardar Inspección
                            </button>
                        </div>
                    </footer>
                </form>
            </div>

            @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const form = document.querySelector('.inspection-form');
                        const numberInputs = form.querySelectorAll('.number-input');

                        // Validación de números
                        numberInputs.forEach(input => {
                            input.addEventListener('input', function () {
                                if (this.value < 0) this.value = 0;
                            });
                        });

                        // Validación en tiempo real
                        const inputs = form.querySelectorAll('input, select, textarea');
                        inputs.forEach(input => {
                            input.addEventListener('blur', function () {
                                if (this.checkValidity()) {
                                    this.style.borderColor = 'var(--success)';
                                } else if (this.value) {
                                    this.style.borderColor = 'var(--error)';
                                }
                            });
                        });
                    });
                </script>
            @endpush
@endsection