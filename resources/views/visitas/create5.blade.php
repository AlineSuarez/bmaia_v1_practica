@extends('layouts.app')

@section('title', 'Registro de Cosecha de Miel')

@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('./css/components/home-user/create/honey-harvest.css') }}">
    </head>

    <div class="harvest-container">
        <header class="harvest-header">
            <div class="header-decoration"></div>
            <h1 class="harvest-title">
                @if(isset($indiceCosecha))
                    Editar Registro de Cosecha de Miel
                @else
                    Registro de Cosecha de Miel
                @endif
            </h1>
            <div class="harvest-subtitle">{{ $apiario->nombre }}</div>
            <div class="header-pattern"></div>
        </header>

        <form method="POST" action="{{ route('visitas.store5', $apiario->id) }}" class="harvest-form">
            @csrf
            @if(isset($indiceCosecha))
                <input type="hidden" name="indice_cosecha_id" value="{{ $indiceCosecha->id }}">
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

            <section class="form-section honey-quality">
                <div class="section-header">
                    <div class="section-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path
                                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                    </div>
                    <h2 class="section-title">Calidad de la Miel</h2>
                    <div class="section-decoration"></div>
                </div>

                <div class="field-group quality-grid">
                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                                </svg>
                            </span>
                            Madurez de la Miel (%)
                        </label>
                        <input type="number" step="0.01" name="madurez_miel" class="field-input"
                            placeholder="Ingrese el porcentaje de madurez..."
                            value="{{ old('madurez_miel', $indiceCosecha->madurez_miel ?? '') }}" required>
                        <span class="field-helper">Porcentaje de maduración de la miel</span>
                    </div>

                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                    <line x1="9" y1="3" x2="9" y2="21" />
                                    <line x1="15" y1="3" x2="15" y2="21" />
                                </svg>
                            </span>
                            N° de Alzadas
                        </label>
                        <input type="number" step="0.01" name="num_alzadas" class="field-input"
                            placeholder="Número de alzadas..."
                            value="{{ old('num_alzadas', $indiceCosecha->num_alzadas ?? '') }}" required>
                        <span class="field-helper">Cantidad total de alzadas cosechadas</span>
                    </div>

                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                                </svg>
                            </span>
                            N° de Marcos con Miel
                        </label>
                        <input type="number" step="0.01" name="marcos_miel" class="field-input"
                            placeholder="Número de marcos..."
                            value="{{ old('marcos_miel', $indiceCosecha->marcos_miel ?? '') }}" required>
                        <span class="field-helper">Marcos totales con miel operculada</span>
                    </div>
                </div>
            </section>

            <section class="form-section harvest-details">
                <div class="section-header">
                    <div class="section-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14,2 14,8 20,8" />
                        </svg>
                    </div>
                    <h2 class="section-title">Detalles del Proceso de Cosecha</h2>
                    <div class="section-decoration"></div>
                </div>

                <div class="field-group details-grid">
                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 3h18v18H3zM9 9h6v6H9z" />
                                </svg>
                            </span>
                            ID Lote de Cosecha
                        </label>
                        <input type="text" name="id_lote_cosecha" class="field-input"
                            placeholder="Identificador del lote..."
                            value="{{ old('id_lote_cosecha', $indiceCosecha->id_lote_cosecha ?? '') }}">
                        <span class="field-helper">Código único de identificación del lote</span>
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
                            Fecha de Cosecha
                        </label>
                        <input type="date" name="fecha_cosecha" class="field-input date-input"
                            value="{{ old('fecha_cosecha', isset($indiceCosecha->fecha_cosecha) ? $indiceCosecha->fecha_cosecha->format('Y-m-d') : '') }}">
                        <span class="field-helper">Fecha cuando se realizó la cosecha</span>
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
                            Fecha de Extracción
                        </label>
                        <input type="date" name="fecha_extraccion" class="field-input date-input"
                            value="{{ old('fecha_extraccion', isset($indiceCosecha->fecha_extraccion) ? $indiceCosecha->fecha_extraccion->format('Y-m-d') : '') }}">
                        <span class="field-helper">Fecha de extracción de la miel</span>
                    </div>

                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                            </span>
                            Lugar de Extracción
                        </label>
                        <input type="text" name="lugar_extraccion" class="field-input"
                            placeholder="Ubicación de la extracción..."
                            value="{{ old('lugar_extraccion', $indiceCosecha->lugar_extraccion ?? '') }}">
                        <span class="field-helper">Lugar donde se extrajo la miel</span>
                    </div>

                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z" />
                                </svg>
                            </span>
                            Humedad de la Miel (%)
                        </label>
                        <input type="number" step="0.01" name="humedad_miel" class="field-input"
                            placeholder="Porcentaje de humedad..."
                            value="{{ old('humedad_miel', $indiceCosecha->humedad_miel ?? '') }}">
                        <span class="field-helper">Nivel de humedad detectado en la miel</span>
                    </div>

                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z" />
                                </svg>
                            </span>
                            Temperatura Ambiente (°C)
                        </label>
                        <input type="number" step="0.1" name="temperatura_ambiente" class="field-input"
                            placeholder="Temperatura en °C..."
                            value="{{ old('temperatura_ambiente', $indiceCosecha->temperatura_ambiente ?? '') }}">
                        <span class="field-helper">Temperatura durante la extracción</span>
                    </div>

                    <div class="form-field">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </span>
                            Responsable de Cosecha
                        </label>
                        <input type="text" name="responsable_cosecha" class="field-input"
                            placeholder="Nombre del responsable..."
                            value="{{ old('responsable_cosecha', $indiceCosecha->responsable_cosecha ?? '') }}">
                        <span class="field-helper">Persona a cargo de la cosecha</span>
                    </div>

                    <div class="form-field field-full-width">
                        <label class="field-label">
                            <span class="label-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14,2 14,8 20,8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                </svg>
                            </span>
                            Notas Adicionales
                        </label>
                        <textarea name="notas" class="field-input field-textarea" rows="4"
                            placeholder="Observaciones generales sobre la cosecha...">{{ old('notas', $indiceCosecha->notas ?? '') }}</textarea>
                        <span class="field-helper">Información adicional relevante</span>
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
                        @if(isset($indiceCosecha))
                            Actualizar Registro
                        @else
                            Guardar Registro
                        @endif
                    </button>
                </div>
            </footer>
        </form>
    </div>
@endsection