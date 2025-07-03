@extends('layouts.app')

@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/components/home-user/create/create-pcc.css') }}">
    </head>

    <div class="expert-system-container">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="expert-header">
                <div class="header-content">
                    <div class="header-icon"><i class="fas fa-brain"></i></div>
                    <div class="header-text">
                        <h1 class="main-title">Sistema Experto Apícola</h1>
                        <p class="subtitle">
                            Editar Evaluación PCC – Colmena #{{ $colmena->numero }}
                        </p>
                    </div>
                </div>
                <div class="progress-indicator">
                    <div class="progress-bar-custom">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <span class="progress-text" id="progressText">Paso 1 de 7</span>
                </div>
            </div>

            <div class="row g-4">
                <!-- Navigation Sidebar -->
                <div class="col-lg-3 col-md-4">
                    <div class="wizard-navigation">
                        <div class="nav-header"><i class="fas fa-list-check"></i> <span>Evaluación PCC</span></div>
                        <ul class="nav nav-pills flex-column wizard-nav" id="wizardTabs" role="tablist">
                            @foreach(range(1,7) as $n)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link{{ $n===1?' active':'' }}"
                                            id="step{{ $n }}-tab"
                                            data-bs-toggle="tab"
                                            data-bs-target="#step{{ $n }}"
                                            type="button" role="tab">
                                        <div class="step-number">{{ $n }}</div>
                                        <div class="step-content">
                                            <span class="step-title">PCC{{ $n }}</span>
                                            <small class="step-subtitle">
                                                @switch($n)
                                                    @case(1) Desarrollo Cámara de Cría @break
                                                    @case(2) Calidad de la Reina @break
                                                    @case(3) Estado Nutricional @break
                                                    @case(4) Nivel Infestación Varroa @break
                                                    @case(5) Presencia Nosemosis @break
                                                    @case(6) Índice de Cosecha @break
                                                    @case(7) Preparación Invernada @break
                                                @endswitch
                                            </small>
                                        </div>
                                        <i class="fas fa-chevron-right step-arrow"></i>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="col-lg-9 col-md-8">
                    <div class="wizard-content">
                        <form action="{{ route('visitas.pcc.update', ['visita' => $visita->id]) }}?colmena={{ $colmena->id }}" method="POST">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="apiario_id" value="{{ $visita->apiario_id }}">
                            <input type="hidden" name="colmena_id"  value="{{ $colmena->id }}">

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $e)
                                            <li>{{ $e }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="tab-content" id="wizardTabsContent">
                                {{-- PCC1 --}}
                                <div class="tab-pane fade show active" id="step1" role="tabpanel">
                                    <div class="step-card">
                                        <div class="step-header">
                                            <div class="step-icon"><i class="fas fa-seedling"></i></div>
                                            <div class="step-info">
                                                <h4 class="step-title">PCC1 - Desarrollo Cámara de Cría</h4>
                                                <p class="step-description">Evaluación del vigor de la colmena y actividad de las abejas</p>
                                            </div>
                                        </div>
                                        <div class="form-grid">
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-tachometer-alt me-2"></i><strong>Vigor de la colmena</strong></label>
                                                <select class="form-select form-control-modern"
                                                        name="desarrollo_cria[vigor_colmena]">
                                                    <option value="">Seleccionar vigor...</option>
                                                    @foreach(['Débil','Regular','Vigorosa'] as $o)
                                                        <option value="{{ $o }}"
                                                            {{ old('desarrollo_cria.vigor_colmena', $valores['desarrollo_cria']['vigor_colmena'] ?? '') == $o ? 'selected':'' }}>
                                                            {{ $o }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-chart-line me-2"></i><strong>Actividad de las abejas</strong></label>
                                                <select class="form-select form-control-modern"
                                                        name="desarrollo_cria[actividad_abejas]">
                                                    <option value="">Seleccionar actividad...</option>
                                                    @foreach(['Bajo','Medio','Alto'] as $o)
                                                        <option value="{{ $o }}"
                                                            {{ old('desarrollo_cria.actividad_abejas', $valores['desarrollo_cria']['actividad_abejas'] ?? '') == $o ? 'selected':'' }}>
                                                            {{ $o }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-circle me-2 text-warning"></i><strong>Ingreso de polen</strong></label>
                                                <div class="radio-group">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="desarrollo_cria[ingreso_polen]"
                                                               id="polen_si" value="Sí"
                                                               {{ old('desarrollo_cria.ingreso_polen', $valores['desarrollo_cria']['ingreso_polen'] ?? '') == 'Sí' ? 'checked':'' }}>
                                                        <label class="form-check-label" for="polen_si">Sí</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="desarrollo_cria[ingreso_polen]"
                                                               id="polen_no"  value="No"
                                                               {{ old('desarrollo_cria.ingreso_polen', $valores['desarrollo_cria']['ingreso_polen'] ?? '') == 'No'  ? 'checked':'' }}>
                                                        <label class="form-check-label" for="polen_no">No</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-ban me-2 text-danger"></i><strong>Bloqueo de cámara de cría</strong></label>
                                                <div class="radio-group">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="desarrollo_cria[bloqueo_camara_cria]"
                                                               id="bloqueo_si" value="Sí"
                                                               {{ old('desarrollo_cria.bloqueo_camara_cria', $valores['desarrollo_cria']['bloqueo_camara_cria'] ?? '') == 'Sí' ? 'checked':'' }}>
                                                        <label class="form-check-label" for="bloqueo_si">Sí</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="desarrollo_cria[bloqueo_camara_cria]"
                                                               id="bloqueo_no" value="No"
                                                               {{ old('desarrollo_cria.bloqueo_camara_cria', $valores['desarrollo_cria']['bloqueo_camara_cria'] ?? '') == 'No'  ? 'checked':'' }}>
                                                        <label class="form-check-label" for="bloqueo_no">No</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-crown me-2 text-warning"></i><strong>Presencia de celdas reales</strong></label>
                                                <div class="radio-group">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="desarrollo_cria[presencia_celdas_reales]"
                                                               id="celdas_si" value="Sí"
                                                               {{ old('desarrollo_cria.presencia_celdas_reales', $valores['desarrollo_cria']['presencia_celdas_reales'] ?? '') == 'Sí'  ? 'checked':'' }}>
                                                        <label class="form-check-label" for="celdas_si">Sí</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="desarrollo_cria[presencia_celdas_reales]"
                                                               id="celdas_no" value="No"
                                                               {{ old('desarrollo_cria.presencia_celdas_reales', $valores['desarrollo_cria']['presencia_celdas_reales'] ?? '') == 'No'   ? 'checked':'' }}>
                                                        <label class="form-check-label" for="celdas_no">No</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Cantidad de marcos con cría</strong></label>
                                                <input type="number" min="0"
                                                       class="form-control form-control-modern"
                                                       name="desarrollo_cria[cantidad_marcos_con_cria]"
                                                       value="{{ old('desarrollo_cria.cantidad_marcos_con_cria', $valores['desarrollo_cria']['cantidad_marcos_con_cria'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Cantidad de marcos con abejas</strong></label>
                                                <input type="number" min="0"
                                                       class="form-control form-control-modern"
                                                       name="desarrollo_cria[cantidad_marcos_con_abejas]"
                                                       value="{{ old('desarrollo_cria.cantidad_marcos_con_abejas', $valores['desarrollo_cria']['cantidad_marcos_con_abejas'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Cantidad de reservas (miel/polén)</strong></label>
                                                <input type="number" min="0"
                                                       class="form-control form-control-modern"
                                                       name="desarrollo_cria[cantidad_reservas]"
                                                       value="{{ old('desarrollo_cria.cantidad_reservas', $valores['desarrollo_cria']['cantidad_reservas'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Presencia de zánganos</strong></label>
                                                <select class="form-select form-control-modern"
                                                        name="desarrollo_cria[presencia_zanganos]">
                                                    <option value="">Seleccionar...</option>
                                                    <option value="1" {{ old('desarrollo_cria.presencia_zanganos', $valores['desarrollo_cria']['presencia_zanganos'] ?? '') == '1' ? 'selected':'' }}>Sí</option>
                                                    <option value="0" {{ old('desarrollo_cria.presencia_zanganos', $valores['desarrollo_cria']['presencia_zanganos'] ?? '') == '0' ? 'selected':'' }}>No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="step-actions">
                                            <button type="button" class="btn btn-primary btn-next" data-step="2">
                                                Siguiente <i class="fas fa-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- PCC2 --}}
                                <div class="tab-pane fade" id="step2" role="tabpanel">
                                    <div class="step-card">
                                        <div class="step-header">
                                            <div class="step-icon"><i class="fas fa-crown"></i></div>
                                            <div class="step-info">
                                                <h4 class="step-title">PCC2 - Calidad de la Reina</h4>
                                                <p class="step-description">Evaluación del estado reproductivo y calidad de la reina</p>
                                            </div>
                                        </div>
                                        <div class="form-grid">
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-egg me-2"></i><strong>Postura de la reina</strong></label>
                                                <select class="form-select form-control-modern"
                                                        name="calidad_reina[postura_reina]">
                                                    <option value="">Seleccionar postura...</option>
                                                    @foreach(['Irregular','Regular','Compacta'] as $o)
                                                        <option value="{{ $o }}"
                                                            {{ old('calidad_reina.postura_reina', $valores['calidad_reina']['postura_reina'] ?? '') == $o ? 'selected':'' }}>
                                                            {{ $o }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-baby me-2"></i><strong>Estado de la cría</strong></label>
                                                <select class="form-select form-control-modern"
                                                        name="calidad_reina[estado_cria]">
                                                    <option value="">Seleccionar estado...</option>
                                                    @foreach(['Compacta','Semisaltada','Saltada'] as $o)
                                                        <option value="{{ $o }}"
                                                            {{ old('calidad_reina.estado_cria', $valores['calidad_reina']['estado_cria'] ?? '') == $o ? 'selected':'' }}>
                                                            {{ $o }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-male me-2"></i><strong>Postura de zánganos</strong></label>
                                                <select class="form-select form-control-modern"
                                                        name="calidad_reina[postura_zanganos]">
                                                    <option value="">Seleccionar postura...</option>
                                                    @foreach(['Normal','Alta'] as $o)
                                                        <option value="{{ $o }}"
                                                            {{ old('calidad_reina.postura_zanganos', $valores['calidad_reina']['postura_zanganos'] ?? '') == $o ? 'selected':'' }}>
                                                            {{ $o }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Origen de la reina</strong></label>
                                                <select class="form-select form-control-modern"
                                                        name="calidad_reina[origen_reina]">
                                                    <option value="">Seleccionar...</option>
                                                    @foreach(['natural'=>'Natural','comprada'=>'Comprada','fecundada'=>'Fecundada','virgen'=>'Vírgen'] as $val=>$lab)
                                                        <option value="{{ $val }}"
                                                            {{ old('calidad_reina.origen_reina', $valores['calidad_reina']['origen_reina'] ?? '') == $val ? 'selected':'' }}>
                                                            {{ $lab }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Raza</strong></label>
                                                <input type="text" class="form-control form-control-modern"
                                                       name="calidad_reina[raza]" placeholder="Ej. italiana, carníca…"
                                                       value="{{ old('calidad_reina.raza', $valores['calidad_reina']['raza'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Línea genética</strong></label>
                                                <input type="text" class="form-control form-control-modern"
                                                       name="calidad_reina[linea_genetica]"
                                                       value="{{ old('calidad_reina.linea_genetica', $valores['calidad_reina']['linea_genetica'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Fecha de introducción</strong></label>
                                                <input type="date" class="form-control form-control-modern"
                                                       name="calidad_reina[fecha_introduccion]"
                                                       value="{{ old('calidad_reina.fecha_introduccion', $valores['calidad_reina']['fecha_introduccion'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Estado actual</strong></label>
                                                <select class="form-select form-control-modern"
                                                        name="calidad_reina[estado_actual]">
                                                    <option value="">Seleccionar...</option>
                                                    @foreach(['activa'=>'Activa','fallida'=>'Fallida','reemplazada'=>'Reemplazada'] as $val=>$lab)
                                                        <option value="{{ $val }}"
                                                            {{ old('calidad_reina.estado_actual', $valores['calidad_reina']['estado_actual'] ?? '') == $val ? 'selected':'' }}>
                                                            {{ $lab }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            {{-- si quieres manejar array de reemplazos, agrégalo aquí --}}
                                        </div>
                                        <div class="step-actions">
                                            <button type="button" class="btn btn-secondary btn-prev" data-step="1">
                                                <i class="fas fa-arrow-left me-2"></i> Anterior
                                            </button>
                                            <button type="button" class="btn btn-primary btn-next" data-step="3">
                                                Siguiente <i class="fas fa-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- PCC3 --}}
                                <div class="tab-pane fade" id="step3" role="tabpanel">
                                    <div class="step-card">
                                        <div class="step-header">
                                            <div class="step-icon"><i class="fas fa-apple-alt"></i></div>
                                            <div class="step-info">
                                                <h4 class="step-title">PCC3 - Estado Nutricional</h4>
                                                <p class="step-description">Evaluación de la alimentación y reservas de la colmena</p>
                                            </div>
                                        </div>
                                        <div class="form-grid">
                                            <div class="form-group">
                                                <label class="form-label"><strong>Objetivo</strong></label>
                                                <select name="estado_nutricional[objetivo]" class="form-select">
                                                    <option value="">Seleccionar…</option>
                                                    <option value="estimulacion" {{ old('estado_nutricional.objetivo', $valores['estado_nutricional']['objetivo'] ?? '')=='estimulacion' ? 'selected':'' }}>Estimulación</option>
                                                    <option value="mantencion"   {{ old('estado_nutricional.objetivo', $valores['estado_nutricional']['objetivo'] ?? '')=='mantencion'   ? 'selected':'' }}>Mantención</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-utensils me-2"></i><strong>Tipo de alimentación</strong></label>
                                                <input type="text" name="estado_nutricional[tipo_alimentacion]" class="form-control" placeholder="Ej: Jarabe, Polen…" value="{{ old('estado_nutricional.tipo_alimentacion', $valores['estado_nutricional']['tipo_alimentacion'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-calendar-alt me-2"></i><strong>Fecha de aplicación</strong></label>
                                                <input type="date" name="estado_nutricional[fecha_aplicacion]" class="form-control" value="{{ old('estado_nutricional.fecha_aplicacion', $valores['estado_nutricional']['fecha_aplicacion'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-prescription-bottle me-2"></i><strong>Insumo utilizado</strong></label>
                                                <input type="text" name="estado_nutricional[insumo_utilizado]" class="form-control" placeholder="Nombre del insumo…" value="{{ old('estado_nutricional.insumo_utilizado', $valores['estado_nutricional']['insumo_utilizado'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-vial me-2"></i><strong>Dosificación</strong></label>
                                                <input type="text" name="estado_nutricional[dosifiacion]" class="form-control" placeholder="Cantidad y frecuencia…" value="{{ old('estado_nutricional.dosifiacion', $valores['estado_nutricional']['dosifiacion'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-cogs me-2"></i><strong>Método utilizado</strong></label>
                                                <input type="text" name="estado_nutricional[metodo_utilizado]" class="form-control" placeholder="Método de aplicación…" value="{{ old('estado_nutricional.metodo_utilizado', $valores['estado_nutricional']['metodo_utilizado'] ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="step-actions">
                                            <button type="button" class="btn btn-secondary btn-prev" data-step="2">
                                                <i class="fas fa-arrow-left me-2"></i> Anterior
                                            </button>
                                            <button type="button" class="btn btn-primary btn-next" data-step="4">
                                                Siguiente <i class="fas fa-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- PCC4 --}}
                                <div class="tab-pane fade" id="step4" role="tabpanel">
                                    <div class="step-card">
                                        <div class="step-header">
                                            <div class="step-icon"><i class="fas fa-bug"></i></div>
                                            <div class="step-info">
                                                <h4 class="step-title">PCC4 - Nivel de Infestación de Varroa</h4>
                                            </div>
                                        </div>
                                        <div class="form-grid">
                                            <div class="form-group full-width">
                                                <label class="form-label"><i class="fas fa-eye me-2"></i><strong>Diagnóstico visual</strong></label>
                                                <textarea class="form-control form-control-modern" name="presencia_varroa[diagnostico_visual]" rows="3" placeholder="Ej: varroa forética visible…">{{ old('presencia_varroa.diagnostico_visual', $valores['presencia_varroa']['diagnostico_visual'] ?? '') }}</textarea>
                                            </div>
                                            <div class="form-group full-width">
                                                <label class="form-label"><i class="fas fa-microscope me-2"></i><strong>Muestreo de abejas adultas</strong></label>
                                                <textarea class="form-control form-control-modern" name="presencia_varroa[muestreo_abejas_adultas]" rows="2" placeholder="Resultados del muestreo…">{{ old('presencia_varroa.muestreo_abejas_adultas', $valores['presencia_varroa']['muestreo_abejas_adultas'] ?? '') }}</textarea>
                                            </div>
                                            <div class="form-group full-width">
                                                <label class="form-label"><i class="fas fa-search me-2"></i><strong>Muestreo cría operculada</strong></label>
                                                <textarea class="form-control form-control-modern" name="presencia_varroa[muestreo_cria_operculada]" rows="2" placeholder="Resultados…">{{ old('presencia_varroa.muestreo_cria_operculada', $valores['presencia_varroa']['muestreo_cria_operculada'] ?? '') }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Método diagnóstico</strong></label>
                                                <input type="text" class="form-control form-control-modern" name="presencia_varroa[metodo_diagnostico]" value="{{ old('presencia_varroa.metodo_diagnostico', $valores['presencia_varroa']['metodo_diagnostico'] ?? '') }}" placeholder="alcohol, azúcar…">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Fecha monitoreo Varroa</strong></label>
                                                <input type="date" class="form-control form-control-modern" name="presencia_varroa[fecha_monitoreo_varroa]" value="{{ old('presencia_varroa.fecha_monitoreo_varroa', $valores['presencia_varroa']['fecha_monitoreo_varroa'] ?? '') }}">
                                            </div>

                                            <div class="step-header">
                                                <div class="step-info"><h5 class="step-description">Tratamientos aplicados</h5></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-prescription-bottle-alt me-2"></i><strong>Tratamiento</strong></label>
                                                <input type="text" class="form-control form-control-modern" name="presencia_varroa[tratamiento]" value="{{ old('presencia_varroa.tratamiento', $valores['presencia_varroa']['tratamiento'] ?? '') }}" placeholder="Producto o práctica…">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Producto comercial</strong></label>
                                                <input type="text" class="form-control form-control-modern" name="presencia_varroa[producto_comercial]" value="{{ old('presencia_varroa.producto_comercial', $valores['presencia_varroa']['producto_comercial'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Ingrediente activo</strong></label>
                                                <input type="text" class="form-control form-control-modern" name="presencia_varroa[ingrediente_activo]" value="{{ old('presencia_varroa.ingrediente_activo', $valores['presencia_varroa']['ingrediente_activo'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-calendar-alt me-2"></i><strong>Fecha de aplicación</strong></label>
                                                <input type="date" class="form-control form-control-modern" name="presencia_varroa[fecha_aplicacion]" value="{{ old('presencia_varroa.fecha_aplicacion', $valores['presencia_varroa']['fecha_aplicacion'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-vial me-2"></i><strong>Dosificación</strong></label>
                                                <input type="text" class="form-control form-control-modern" name="presencia_varroa[dosificacion]" value="{{ old('presencia_varroa.dosificacion', $valores['presencia_varroa']['dosificacion'] ?? '') }}" placeholder="Dosis aplicada…">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-cogs me-2"></i><strong>Método de aplicación</strong></label>
                                                <input type="text" class="form-control form-control-modern" name="presencia_varroa[metodo_aplicacion]" value="{{ old('presencia_varroa.metodo_aplicacion', $valores['presencia_varroa']['metodo_aplicacion'] ?? '') }}" placeholder="Forma de aplicación…">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Periodo de carencia (días)</strong></label>
                                                <input type="number" min="0" class="form-control form-control-modern" name="presencia_varroa[periodo_carencia]" value="{{ old('presencia_varroa.periodo_carencia', $valores['presencia_varroa']['periodo_carencia'] ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="step-actions">
                                            <button type="button" class="btn btn-secondary btn-prev" data-step="3">
                                                <i class="fas fa-arrow-left me-2"></i> Anterior
                                            </button>
                                            <button type="button" class="btn btn-primary btn-next" data-step="5">
                                                Siguiente <i class="fas fa-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- PCC5 --}}
                                <div class="tab-pane fade" id="step5" role="tabpanel">
                                    <div class="step-card">
                                        <div class="step-header">
                                            <div class="step-icon"><i class="fas fa-virus"></i></div>
                                            <div class="step-info">
                                                <h4 class="step-title">PCC5 - Presencia de Nosemosis</h4>
                                                <p class="step-description">Diagnóstico y control de la enfermedad Nosema</p>
                                            </div>
                                        </div>
                                        <div class="form-grid">
                                            <div class="form-group full-width">
                                                <label class="form-label"><i class="fas fa-stethoscope me-2"></i><strong>Signos clínicos</strong></label>
                                                <textarea class="form-control form-control-modern" name="presencia_nosemosis[signos_clinicos]" rows="2" placeholder="Ej: abdomen hinchado…">{{ old('presencia_nosemosis.signos_clinicos', $valores['presencia_nosemosis']['signos_clinicos'] ?? '') }}</textarea>
                                            </div>
                                            <div class="form-group full-width">
                                                <label class="form-label"><i class="fas fa-flask me-2"></i><strong>Muestreo laboratorio</strong></label>
                                                <textarea class="form-control form-control-modern" name="presencia_nosemosis[muestreo_laboratorio]" rows="2" placeholder="Resultados laboratorio…">{{ old('presencia_nosemosis.muestreo_laboratorio', $valores['presencia_nosemosis']['muestreo_laboratorio'] ?? '') }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-pills me-2"></i><strong>Tratamiento</strong></label>
                                                <input type="text" class="form-control form-control-modern" name="presencia_nosemosis[tratamiento]" value="{{ old('presencia_nosemosis.tratamiento', $valores['presencia_nosemosis']['tratamiento'] ?? '') }}" placeholder="Medicamento o práctica…">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-calendar-alt me-2"></i><strong>Fecha de aplicación</strong></label>
                                                <input type="date" class="form-control form-control-modern" name="presencia_nosemosis[fecha_aplicacion]" value="{{ old('presencia_nosemosis.fecha_aplicacion', $valores['presencia_nosemosis']['fecha_aplicacion'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-vial me-2"></i><strong>Dosificación</strong></label>
                                                <input type="text" class="form-control form-control-modern" name="presencia_nosemosis[dosificacion]" value="{{ old('presencia_nosemosis.dosificacion', $valores['presencia_nosemosis']['dosificacion'] ?? '') }}" placeholder="Dosis aplicada…">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-cogs me-2"></i><strong>Método de aplicación</strong></label>
                                                <input type="text" class="form-control form-control-modern" name="presencia_nosemosis[metodo_aplicacion]" value="{{ old('presencia_nosemosis.metodo_aplicacion', $valores['presencia_nosemosis']['metodo_aplicacion'] ?? '') }}" placeholder="Forma de aplicación…">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Método diagnóstico lab.</strong></label>
                                                <input type="text" class="form-control form-control-modern" name="presencia_nosemosis[metodo_diagnostico_laboratorio]" value="{{ old('presencia_nosemosis.metodo_diagnostico_laboratorio', $valores['presencia_nosemosis']['metodo_diagnostico_laboratorio'] ?? '') }}" placeholder="Ej. PCR, tinción…">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Fecha monitoreo Nosema</strong></label>
                                                <input type="date" class="form-control form-control-modern" name="presencia_nosemosis[fecha_monitoreo_nosema]" value="{{ old('presencia_nosemosis.fecha_monitoreo_nosema', $valores['presencia_nosemosis']['fecha_monitoreo_nosema'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Producto comercial</strong></label>
                                                <input type="text" class="form-control form-control-modern" name="presencia_nosemosis[producto_comercial]" value="{{ old('presencia_nosemosis.producto_comercial', $valores['presencia_nosemosis']['producto_comercial'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Ingrediente activo</strong></label>
                                                <input type="text" class="form-control form-control-modern" name="presencia_nosemosis[ingrediente_activo]" value="{{ old('presencia_nosemosis.ingrediente_activo', $valores['presencia_nosemosis']['ingrediente_activo'] ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="step-actions">
                                            <button type="button" class="btn btn-secondary btn-prev" data-step="4">
                                                <i class="fas fa-arrow-left me-2"></i> Anterior
                                            </button>
                                            <button type="button" class="btn btn-primary btn-next" data-step="6">
                                                Siguiente <i class="fas fa-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- PCC6 --}}
                                <div class="tab-pane fade" id="step6" role="tabpanel">
                                    <div class="step-card">
                                        <div class="step-header">
                                            <div class="step-icon"><i class="fas fa-egg"></i></div>
                                            <div class="step-info">
                                                <h4 class="step-title">PCC6 - Índice de Cosecha</h4>
                                                <p class="step-description">Evaluación de la producción y calidad de la miel</p>
                                            </div>
                                        </div>
                                        <div class="form-grid">
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-droplet me-2"></i><strong>Madurez de la miel</strong></label>
                                                <select class="form-select form-control-modern" name="indice_cosecha[madurez_miel]">
                                                    <option value="">Seleccionar madurez...</option>
                                                    @foreach(['Inmadura','Madura'] as $o)
                                                        <option value="{{ $o }}"
                                                            {{ old('indice_cosecha.madurez_miel', $valores['indice_cosecha']['madurez_miel'] ?? '') == $o ? 'selected':'' }}>
                                                            {{ $o }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-layer-group me-2"></i><strong>N° de alzas promedio</strong></label>
                                                <input type="number" step="0.1" class="form-control form-control-modern" name="indice_cosecha[num_alzadas]" placeholder="0.0" value="{{ old('indice_cosecha.num_alzadas', $valores['indice_cosecha']['num_alzadas'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><i class="fas fa-border-all me-2"></i><strong>N° marcos miel/alza</strong></label>
                                                <input type="number" step="0.1" class="form-control form-control-modern" name="indice_cosecha[marcos_miel]" placeholder="0.0" value="{{ old('indice_cosecha.marcos_miel', $valores['indice_cosecha']['marcos_miel'] ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="step-actions">
                                            <button type="button" class="btn btn-secondary btn-prev" data-step="5">
                                                <i class="fas fa-arrow-left me-2"></i> Anterior
                                            </button>
                                            <button type="button" class="btn btn-primary btn-next" data-step="7">
                                                Siguiente <i class="fas fa-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- PCC7 --}}
                                <div class="tab-pane fade" id="step7" role="tabpanel">
                                    <div class="step-card">
                                        <div class="step-header">
                                            <div class="step-icon"><i class="fas fa-snowflake"></i></div>
                                            <div class="step-info">
                                                <h4 class="step-title">PCC7 - Preparación para la Invernada</h4>
                                                <p class="step-description">Preparativos para el período de invierno</p>
                                            </div>
                                        </div>
                                        <div class="form-grid">
                                            <div class="form-group">
                                                <label class="form-label"><strong>Marcos cubiertos (abejas)</strong></label>
                                                <input type="number" min="0" class="form-control form-control-modern" name="preparacion_invernada[cantidad_marcos_cubiertos_abejas]" value="{{ old('preparacion_invernada.cantidad_marcos_cubiertos_abejas', $valores['preparacion_invernada']['cantidad_marcos_cubiertos_abejas'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Marcos cubiertos (cría)</strong></label>
                                                <input type="number" min="0" class="form-control form-control-modern" name="preparacion_invernada[cantidad_marcos_cubiertos_cria]" value="{{ old('preparacion_invernada.cantidad_marcos_cubiertos_cria', $valores['preparacion_invernada']['cantidad_marcos_cubiertos_cria'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Marcos de reservas de miel</strong></label>
                                                <input type="number" min="0" class="form-control form-control-modern" name="preparacion_invernada[marcos_reservas_miel]" value="{{ old('preparacion_invernada.marcos_reservas_miel', $valores['preparacion_invernada']['marcos_reservas_miel'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Marcos de reservas de polén</strong></label>
                                                <input type="number" min="0" class="form-control form-control-modern" name="preparacion_invernada[presencial_reservas_polen]" value="{{ old('preparacion_invernada.presencial_reservas_polen', $valores['preparacion_invernada']['presencial_reservas_polen'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Presencia de reina</strong></label>
                                                <select class="form-select form-control-modern" name="preparacion_invernada[presencia_reina]">
                                                    <option value="">Seleccionar…</option>
                                                    <option value="1" {{ old('preparacion_invernada.presencia_reina', $valores['preparacion_invernada']['presencia_reina'] ?? '')=='1'?'selected':'' }}>Sí</option>
                                                    <option value="0" {{ old('preparacion_invernada.presencia_reina', $valores['preparacion_invernada']['presencia_reina'] ?? '')=='0'?'selected':'' }}>No</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Nivel infestación Varroa</strong></label>
                                                <input type="text" class="form-control form-control-modern" name="preparacion_invernada[nivel_infestacion_varroa]" value="{{ old('preparacion_invernada.nivel_infestacion_varroa', $valores['preparacion_invernada']['nivel_infestacion_varroa'] ?? '') }}">
                                            </div>
                                            <div class="form-group full-width">
                                                <label class="form-label"><strong>Signos de enfermedades visibles</strong></label>
                                                <textarea class="form-control form-control-modern" rows="2" name="preparacion_invernada[signos_enfermedades_visibles]">{{ old('preparacion_invernada.signos_enfermedades_visibles', $valores['preparacion_invernada']['signos_enfermedades_visibles'] ?? '') }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Fecha última revisión</strong></label>
                                                <input type="date" class="form-control form-control-modern" name="preparacion_invernada[fecha_ultima_revision_previa_receso]" value="{{ old('preparacion_invernada.fecha_ultima_revision_previa_receso', $valores['preparacion_invernada']['fecha_ultima_revision_previa_receso'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label"><strong>Fecha cierre temporada</strong></label>
                                                <input type="date" class="form-control form-control-modern" name="preparacion_invernada[fecha_cierre_temporada]" value="{{ old('preparacion_invernada.fecha_cierre_temporada', $valores['preparacion_invernada']['fecha_cierre_temporada'] ?? '') }}">
                                            </div>
                                            <div class="form-group full-width">
                                                <label class="form-label"><strong>Alimentación suplementaria</strong></label>
                                                <textarea class="form-control form-control-modern" rows="2" name="preparacion_invernada[alimentacion_suplementaria]">{{ old('preparacion_invernada.alimentacion_suplementaria', $valores['preparacion_invernada']['alimentacion_suplementaria'] ?? '') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="step-actions">
                                            <button type="button" class="btn btn-secondary btn-prev" data-step="6">
                                                <i class="fas fa-arrow-left me-2"></i> Anterior
                                            </button>
                                            <button type="submit" class="btn btn-success btn-save">
                                                Guardar Evaluación <i class="fas fa-save ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicialización de elementos
            const progressFill = document.getElementById('progressFill');
            const progressText = document.getElementById('progressText');
            const tabButtons = document.querySelectorAll('#wizardTabs .nav-link');
            const nextButtons = document.querySelectorAll('.btn-next');
            const prevButtons = document.querySelectorAll('.btn-prev');

            let currentStep = 1;
            const totalSteps = 7;

            // Función para actualizar el progreso
            function updateProgress(step) {
                const percentage = (step / totalSteps) * 100;
                progressFill.style.width = percentage + '%';
                progressText.textContent = `Paso ${step} de ${totalSteps}`;
                currentStep = step;
            }

            // Función para activar un step
            function activateStep(stepNumber) {
                // Desactivar todos los tabs
                tabButtons.forEach(tab => {
                    tab.classList.remove('active');
                    tab.setAttribute('aria-selected', 'false');
                });

                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('show', 'active');
                });

                // Activar el tab y panel correspondiente
                const targetTab = document.getElementById(`step${stepNumber}-tab`);
                const targetPane = document.getElementById(`step${stepNumber}`);

                if (targetTab && targetPane) {
                    targetTab.classList.add('active');
                    targetTab.setAttribute('aria-selected', 'true');
                    targetPane.classList.add('show', 'active');
                    updateProgress(stepNumber);

                    // Trigger Bootstrap tab event
                    const tabEvent = new bootstrap.Tab(targetTab);
                    tabEvent.show();
                }
            }

            // Navegación con botones Next
            nextButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const nextStep = parseInt(this.getAttribute('data-step'));

                    if (nextStep && nextStep >= 1 && nextStep <= totalSteps) {
                        activateStep(nextStep);

                        // Scroll to top del contenido
                        setTimeout(() => {
                            document.querySelector('.wizard-content').scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }, 100);
                    }
                });
            });

            // Navegación con botones Previous
            prevButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const prevStep = parseInt(this.getAttribute('data-step'));

                    if (prevStep && prevStep >= 1 && prevStep <= totalSteps) {
                        activateStep(prevStep);

                        // Scroll to top del contenido
                        setTimeout(() => {
                            document.querySelector('.wizard-content').scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }, 100);
                    }
                });
            });

            // Navegación directa con tabs de la sidebar
            tabButtons.forEach((button, index) => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const stepNumber = index + 1;
                    activateStep(stepNumber);
                });
            });

            // Inicializar Bootstrap tabs correctamente
            tabButtons.forEach(button => {
                new bootstrap.Tab(button);
            });

            // Evento de teclado para navegación
            document.addEventListener('keydown', function (e) {
                if (e.ctrlKey) {
                    if (e.key === 'ArrowRight' && currentStep < totalSteps) {
                        e.preventDefault();
                        activateStep(currentStep + 1);
                    } else if (e.key === 'ArrowLeft' && currentStep > 1) {
                        e.preventDefault();
                        activateStep(currentStep - 1);
                    }
                }
            });

            // Validación de formularios en tiempo real
            const formInputs = document.querySelectorAll('.form-control-modern, .form-select');
            formInputs.forEach(input => {
                input.addEventListener('blur', function () {
                    validateField(this);
                });

                input.addEventListener('input', function () {
                    if (this.classList.contains('is-invalid')) {
                        validateField(this);
                    }
                });
            });

            function validateField(field) {
                if (field.hasAttribute('required') && !field.value.trim()) {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                } else if (field.value.trim()) {
                    field.classList.add('is-valid');
                    field.classList.remove('is-invalid');
                }
            }

            // Validación antes de avanzar (opcional)
            function validateCurrentStep() {
                const currentPane = document.querySelector('.tab-pane.active');
                const requiredFields = currentPane.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    }
                });

                return isValid;
            }

            // Animación de entrada para los elementos del formulario
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationDelay = '0.1s';
                        entry.target.classList.add('animate-in');
                    }
                });
            }, observerOptions);

            // Observar elementos del formulario
            document.querySelectorAll('.form-group').forEach(group => {
                observer.observe(group);
            });

            // Efecto de loading en botón de guardar
            const saveButton = document.querySelector('.btn-save');
            if (saveButton) {
                saveButton.addEventListener('click', function () {
                    // Validar antes de enviar
                    if (!validateCurrentStep()) {
                        showNotification('Por favor, complete todos los campos requeridos.', 'warning');
                        return false;
                    }

                    this.classList.add('loading');
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
                });
            }

            // Auto-save draft (opcional)
            let saveTimeout;
            formInputs.forEach(input => {
                input.addEventListener('input', function () {
                    clearTimeout(saveTimeout);
                    saveTimeout = setTimeout(() => {
                        saveDraft();
                    }, 2000);
                });
            });

            function saveDraft() {
                // Implementar auto-guardado si es necesario
                console.log('Draft saved...');
            }

            // Inicializar con el primer paso
            updateProgress(1);

            // Asegurar que el primer tab esté activo
            setTimeout(() => {
                activateStep(1);
            }, 100);

            // Debug: Verificar que los botones existen
            console.log('Next buttons found:', nextButtons.length);
            console.log('Prev buttons found:', prevButtons.length);
            console.log('Tab buttons found:', tabButtons.length);
        });

        // Función para el script original del evaluador de actividad
        document.addEventListener('DOMContentLoaded', function () {
            const evaluarBtn = document.getElementById('evaluarActividad');
            if (evaluarBtn) {
                evaluarBtn.addEventListener('click', function () {
                    const alto = parseInt(document.getElementById('nc1').value) || 0;
                    const medio = parseInt(document.getElementById('nc2').value) || 0;
                    const bajo = parseInt(document.getElementById('nc3').value) || 0;

                    const totalColmenas = alto + medio + bajo;
                    if (totalColmenas === 0) {
                        showNotification('Debe ingresar al menos una colmena para calcular la actividad.', 'warning');
                        return;
                    }

                    const promedio = (alto * 3 + medio * 2 + bajo * 1) / totalColmenas;

                    let resultado;
                    if (promedio >= 2.5) {
                        resultado = 'Alto';
                    } else if (promedio >= 1.5) {
                        resultado = 'Medio';
                    } else {
                        resultado = 'Bajo';
                    }

                    document.getElementById('actividad').value = resultado;
                    showNotification(`Actividad calculada: ${resultado}`, 'success');
                });
            }
        });

        // Sistema de notificaciones mejorado
        function showNotification(message, type = 'info') {
            // Remover notificaciones existentes
            const existingNotifications = document.querySelectorAll('.custom-notification');
            existingNotifications.forEach(notif => notif.remove());

            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show position-fixed custom-notification`;
            notification.style.cssText = `
                                    top: 20px; 
                                    right: 20px; 
                                    z-index: 9999; 
                                    min-width: 300px;
                                    border-radius: 12px;
                                    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
                                    border: none;
                                `;
            notification.innerHTML = `
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                                        <span>${message}</span>
                                    </div>
                                    <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
                                `;

            document.body.appendChild(notification);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }
    </script>
    

@endsection
