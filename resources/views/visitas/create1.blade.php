@extends('layouts.app')

@section('title', 'Registro de Visitas al Apiario')

@section('content')
    <div class="visit-container">
        <header class="visit-header">
            <div class="header-decoration"></div>
            <h1 class="visit-title">Registro de Visitas al Apiario</h1>
            <div class="visit-subtitle">Complete los datos para registrar su visita oficial</div>
            <div class="header-pattern"></div>
            <link rel="stylesheet" href="{{ asset('./css/components/home-user/create/visit-record.css') }}">
        </header>

        <!-- Alerta si ya existe una visita registrada -->
        @if ($visita)
            <div class="alert-container">
                <div class="alert-card warning">
                    <div class="alert-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path
                                d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                            <line x1="12" y1="9" x2="12" y2="13" />
                            <line x1="12" y1="17" x2="12.01" y2="17" />
                        </svg>
                    </div>
                    <div class="alert-content">
                        <div class="alert-title">¡Atención!</div>
                        <div class="alert-message">
                            Ya existe una visita registrada para este apiario el <strong>{{ $visita->fecha_visita }}</strong>.
                            ¿Desea modificar esta información o cancelar?
                        </div>
                    </div>
                    <div class="alert-actions">
                        <a href="#" class="btn btn-primary btn-sm">
                            <span class="btn-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                </svg>
                            </span>
                            Modificar
                        </a>
                        <a href="{{ route('visita.index') }}" class="btn btn-outline btn-sm">
                            <span class="btn-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 12H5m7-7l-7 7 7 7" />
                                </svg>
                            </span>
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('apiarios.visitas-general.store', $apiario) }}" method="POST" class="visit-form">
            @csrf
            @if(isset($visita))
                <input type="hidden" name="visita_id" value="{{ $visita->id }}">
            @endif

            <!-- Información de la Visita -->
            <section class="form-section visit-info">
                <div class="section-header">
                    <div class="section-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12,6 12,12 16,14" />
                        </svg>
                    </div>
                    <h2 class="section-title">Información de la Visita</h2>
                    <div class="section-decoration"></div>
                </div>
                <div class="field-group">
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
                            Fecha de la Visita
                        </label>
                        <input type="date" id="fecha" name="fecha" class="form-control" required
                            value="{{ old(
                                'fecha',
                                isset($visita)
                                ? \Carbon\Carbon::parse($visita->fecha_visita)->format('Y-m-d')
                                : ''
                            ) }}">
                        <span class="field-helper">Seleccione la fecha de su visita al apiario</span>
                        @error('fecha')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="motivo" class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path
                                        d="M9 11H5a2 2 0 0 0-2 2v3c0 1.1.9 2 2 2h4m6-6h4a2 2 0 0 1 2 2v3c0 1.1-.9 2-2 2h-4m-6 0a2 2 0 0 0-2-2v-3c0-1.1.9-2 2-2m6 6a2 2 0 0 1 2-2v-3c0-1.1-.9-2-2-2" />
                                </svg>
                            </span>
                            Motivo de la Visita
                        </label>
                        <select id="motivo" name="motivo" class="field-input text-input" required>
                            <option value="">Seleccione una opción</option>
                            <option value="Inspección de rutina" {{ old('motivo', $visitaGeneral->motivo ?? '') == 'Inspección de rutina' ? 'selected' : '' }}>
                                Inspección de rutina
                            </option>
                            <option value="Inspección sanitaria" {{ old('motivo', $visitaGeneral->motivo ?? '') == 'Inspección sanitaria' ? 'selected' : '' }}>
                                Inspección sanitaria
                            </option>
                            <option value="Mantenimiento" {{ old('motivo', $visitaGeneral->motivo ?? '') == 'Mantenimiento' ? 'selected' : '' }}>
                                Mantenimiento
                            </option>
                            <option value="Otros" {{ old('motivo', $visitaGeneral->motivo ?? '') == 'Otros' ? 'selected' : '' }}>
                                Otros
                            </option>
                        </select>
                        <span class="field-helper">Describa brevemente el propósito de su visita</span>
                    </div>
                </div>
            </section>

            <!-- Información del Visitante -->
            <section class="form-section visitor-info">
                <div class="section-header">
                    <div class="section-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </div>
                    <h2 class="section-title">Información del Visitante</h2>
                    <div class="section-decoration"></div>
                </div>
                <div class="visitor-grid">
                    <div class="visitor-card personal-info">
                        <div class="card-header">
                            <div class="card-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0z" />
                                    <path d="M12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7z" />
                                </svg>
                            </div>
                            <span class="card-title">Datos Personales</span>
                        </div>
                        <div class="field-group">
                            <div class="form-field">
                                <label class="field-label">
                                    <span class="label-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                            <circle cx="12" cy="7" r="4" />
                                        </svg>
                                    </span>
                                    Nombres
                                </label>
                                <input type="text" name="nombres" class="field-input"
                                    value="{{ old('nombres', auth()->user()->name) }}" required>
                            </div>

                            <div class="form-field">
                                <label class="field-label">
                                    <span class="label-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                            <circle cx="12" cy="7" r="4" />
                                        </svg>
                                    </span>
                                    Apellidos
                                </label>
                                <input type="text" name="apellidos" class="field-input"
                                    value="{{ old('apellidos', auth()->user()->last_name) }}" required>
                            </div>

                            <div class="form-field">
                                <label class="field-label">
                                    <span class="label-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                            <polyline points="14,2 14,8 20,8" />
                                            <line x1="16" y1="13" x2="8" y2="13" />
                                            <line x1="16" y1="17" x2="8" y2="17" />
                                            <polyline points="10,9 9,9 8,9" />
                                        </svg>
                                    </span>
                                    RUT
                                </label>
                                <input type="text" name="rut" class="field-input"
                                    value="{{ old('rut', auth()->user()->rut) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="visitor-card contact-info">
                        <div class="card-header">
                            <div class="card-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path
                                        d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                </svg>
                            </div>
                            <span class="card-title">Contacto</span>
                        </div>
                        <div class="field-group">
                            <div class="form-field">
                                <label class="field-label">
                                    <span class="label-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path
                                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                        </svg>
                                    </span>
                                    Teléfono
                                </label>
                                <input type="text" name="telefono" class="field-input"
                                    value="{{ old('telefono', auth()->user()->telefono) }}" required>
                            </div>

                            
                        </div>
                    </div>
                </div>
            </section>

            <!-- Acciones del Formulario -->
            <footer class="form-actions">
                <div class="actions-container">
                    <a href="{{ route('visita.index') }}" class="btn btn-secondary">
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
                        Registrar Visita
                    </button>
                </div>
            </footer>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.querySelector('.visit-form');

                // Validación en tiempo real
                const inputs = form.querySelectorAll('input:not([readonly])');
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
            });
        </script>
    @endpush
@endsection