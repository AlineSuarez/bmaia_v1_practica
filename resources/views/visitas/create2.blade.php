@extends('layouts.app')

@section('content')
    @php
        $visita = $visita ?? null;
        $pcc4   = $pcc4   ?? [];
        $pcc5   = $pcc5   ?? [];
    @endphp
    <div class="medication-container">
        <header class="medication-header">
            <div class="header-decoration"></div>
            <h1 class="medication-title">Registro de Uso de Medicamentos</h1>
            <div class="medication-subtitle">Registre el tratamiento aplicado a las colmenas de manera precisa</div>
            <div class="header-pattern"></div>
            <link rel="stylesheet" href="{{ asset('./css/components/home-user/create/medicines.css') }}">
        </header>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form
            action="{{ route('apiarios.medicamentos-registro.store', $apiario) }}"
            method="POST"
            class="medication-form"
        >
            @csrf
            @if(isset($visita))
                <input type="hidden" name="visita_id" value="{{ $visita->id }}">
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Información del Tratamiento -->
            <section class="form-section treatment-info">
                <div class="section-header">
                    <div class="section-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12,6 12,12 16,14" />
                        </svg>
                    </div>
                    <h2 class="section-title">Información del Tratamiento</h2>
                    <div class="section-decoration"></div>
                </div>
                <div class="field-group treatment-grid">
                    <div class="form-field">
                        <label for="fecha" class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                    <line x1="16" y1="2" x2="16" y2="6" />
                                    <line x1="8" y1="2" x2="8" y2="6" />
                                    <line x1="3" y1="10" x2="21" y2="10" />
                                </svg>
                            </span>
                            Fecha del Tratamiento
                        </label>
                        <input type="date" id="fecha" name="fecha" class="field-input date-input" required
                            value="{{ old('fecha', date('Y-m-d')) }}">
                        <span class="field-helper">Seleccione la fecha de aplicación del medicamento</span>
                    </div>

                    <div class="form-field full-width">
                        <label for="motivo_tratamiento" class="field-label">Motivo del Tratamiento</label>

                        <select id="motivo_tratamiento" name="motivo_tratamiento" class="field-input text-input" required>
                            <option value="">Seleccione...</option>
                            <option value="varroa" {{ old('motivo_tratamiento',$visita->motivo_tratamiento??'')=='varroa'   ? 'selected':'' }}>Varroa</option>
                            <option value="nosema" {{ old('motivo_tratamiento',$visita->motivo_tratamiento??'')=='nosema'   ? 'selected':'' }}>Nosema</option>
                            <option value="otro" {{ old('motivo_tratamiento',$visita->motivo_tratamiento??'')=='otro'     ? 'selected':'' }}>Otro</option>
                        </select>

                        <input
                            id="motivo_otro"
                            name="motivo_otro"
                            type="text"
                            class="field-input text-input mt-2"
                            placeholder="Especifique otro motivo"
                            value="{{ old('motivo_otro', (isset($visita) && $visita->motivo_tratamiento === 'otro' ? $visita->motivo : '')) }}"
                            style="display:none;"
                        >
                        @error('motivo_otro')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror

                        <span class="field-helper">Indique la enfermedad o condición que motivó el tratamiento</span>

                        {{-- Formulario PCC4 (varroa) --}}
                        <div id="pcc4-form" class="form-section pcc-section" style="display:none">
                            @include('sistemaexperto.partials.pcc4')
                        </div>

                        {{-- Formulario PCC5 (nosema) --}}
                        <div id="pcc5-form" class="form-section pcc-section" style="display:none">
                            @include('sistemaexperto.partials.pcc5')
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="responsable" class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </span>
                            Responsable
                        </label>
                        <input type="text" id="responsable" name="responsable" class="field-input text-input"
                            placeholder="Nombre del responsable del tratamiento" value="{{ old('responsable',$visita->responsable??'') }}">
                        <span class="field-helper">Persona que aplicó o supervisó el tratamiento</span>
                    </div>
                </div>
            </section>

            <!-- Información del Medicamento
            
            <section class="form-section medication-info">
                <div class="section-header">
                    <div class="section-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path
                                d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z" />
                            <path d="M12 15l8.5-8.5a2.83 2.83 0 0 0-4-4L8 11" />
                            <path d="M9 12l-2 2" />
                            <path d="M16 16l-2 2" />
                        </svg>
                    </div>
                    <h2 class="section-title">Información del Medicamento</h2>
                    <div class="section-decoration"></div>
                </div>
                <div class="medication-grid">
                    <div class="medication-card commercial">
                        <div class="card-header">
                            <div class="card-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14,2 14,8 20,8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <polyline points="10,9 9,9 8,9" />
                                </svg>
                            </div>
                            <span class="card-badge commercial-badge">Comercial</span>
                        </div>
                        <div class="form-field">
                            <label for="nombre_comercial_medicamento" class="field-label">Nombre Comercial</label>
                            <input type="text" id="nombre_comercial_medicamento" name="nombre_comercial_medicamento"
                                class="field-input text-input" placeholder="Ej: Apistan, CheckMite+, etc."
                                value="{{ old('nombre_comercial_medicamento',$visita->nombre_comercial_medicamento??'') }}">
                        </div>
                    </div>

                    <div class="medication-card active">
                        <div class="card-header">
                            <div class="card-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="3" />
                                    <path d="M12 1v6m0 6v6" />
                                    <path d="M1 12h6m6 0h6" />
                                </svg>
                            </div>
                            <span class="card-badge active-badge">Principio Activo</span>
                        </div>
                        <div class="form-field">
                            <label for="principio_activo_medicamento" class="field-label">Principio Activo</label>
                            <input type="text" id="principio_activo_medicamento" name="principio_activo_medicamento"
                                class="field-input text-input" placeholder="Ej: Fluvalinato, Coumafos, etc."
                                value="{{ old('principio_activo_medicamento',$visita->principio_activo_medicamento??'') }}">
                        </div>
                    </div>

                    <div class="medication-card safety">
                        <div class="card-header">
                            <div class="card-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                    <path d="M9 12l2 2 4-4" />
                                </svg>
                            </div>
                            <span class="card-badge safety-badge">Seguridad</span>
                        </div>
                        <div class="form-field">
                            <label for="periodo_resguardo" class="field-label">Período de Resguardo</label>
                            <input type="text" id="periodo_resguardo" name="periodo_resguardo"
                                class="field-input text-input" placeholder="Ej: 30, 45, etc."
                                value="{{ old('periodo_resguardo',$visita->periodo_resguardo??'') }}">
                            <span class="field-helper">Tiempo de espera en días antes de cosechar miel</span>
                        </div>
                    </div>
                </div>
            </section>
            
            -->
            

            <!-- Observaciones Adicionales -->
            <section class="form-section observations-info">
                <div class="section-header">
                    <div class="section-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14,2 14,8 20,8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                            <polyline points="10,9 9,9 8,9" />
                        </svg>
                    </div>
                    <h2 class="section-title">Observaciones Adicionales</h2>
                    <div class="section-decoration"></div>
                </div>
                <div class="field-group">
                    <div class="form-field textarea-field">
                        <label for="observaciones" class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                </svg>
                            </span>
                            Observaciones
                        </label>
                        <textarea id="observaciones" name="observaciones" class="field-textarea" rows="4"
                            placeholder="Anote cualquier observación relevante sobre la aplicación del medicamento, condiciones climáticas, reacciones observadas, etc.">{{ old('observaciones',$visita->observaciones??'') }}</textarea>
                        <span class="field-helper">Información adicional que considere relevante para el registro</span>
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
                    <button type="submit" class="btn btn-primary btn-large">
                        <span class="btn-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                <polyline points="17,21 17,13 7,13 7,21" />
                                <polyline points="7,3 7,8 15,8" />
                            </svg>
                        </span>
                        Registrar Medicamento
                    </button>
                </div>
            </footer>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.querySelector('.medication-form');
                const numberInputs = form.querySelectorAll('.number-input');
                const motivo      = document.getElementById('motivo_tratamiento');
                const pcc4        = document.getElementById('pcc4-form');
                const pcc5        = document.getElementById('pcc5-form');
                const motivoOtro  = document.getElementById('motivo_otro');

                // Validación de números
                numberInputs.forEach(input => {
                    input.addEventListener('input', function () {
                        if (this.value < 0) this.value = 0;
                    });
                });

                // Validación en tiempo real
                const inputs = form.querySelectorAll('input, textarea');
                inputs.forEach(input => {
                    input.addEventListener('blur', function () {
                        if (this.checkValidity()) {
                            this.style.borderColor = 'var(--success)';
                        } else if (this.value) {
                            this.style.borderColor = 'var(--error)';
                        }
                    });
                });

                // Efecto de entrada progresiva
                const sections = document.querySelectorAll('.form-section');
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }
                    });
                }, {
                    threshold: 0.1
                });

                sections.forEach(section => {
                    observer.observe(section);
                });

                function toggleSections() {
                const val = motivo.value;
                pcc4.style.display       = (val === 'varroa') ? 'block' : 'none';
                pcc5.style.display       = (val === 'nosema') ? 'block' : 'none';
                motivoOtro.style.display = (val === 'otro')   ? 'block' : 'none';
            }

            motivo.addEventListener('change', toggleSections);
            toggleSections(); // Mostrar según old()
            });
        </script>
    @endpush
@endsection