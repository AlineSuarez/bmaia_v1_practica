@extends('layouts.app')

@section('content')
<head>
    <link rel="stylesheet" href="{{ asset('css/components/home-user/record.css') }}">
</head>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- Header Section -->
                    <div class="header-section mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="header-icon me-3">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <div>
                                    <h2 class="page-title mb-1">Historial de Visitas</h2>
                                    <p class="subtitle mb-0">{{ $apiario->nombre }}</p>
                                </div>
                            </div>
                            <!-- Statistics Cards -->
                            <div class="stats-container d-none d-lg-flex">
                                <div class="stat-card">
                                    <div class="stat-number">{{ $apiario->visitas->where('tipo_visita', 'Visita General')->count() }}</div>
                                    <div class="stat-label">Generales</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-number">{{ $apiario->visitas->where('tipo_visita', 'Inspección de Visita')->count() }}</div>
                                    <div class="stat-label">Inspecciones</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-number">{{ $apiario->visitas->where('tipo_visita', 'Uso de Medicamentos')->count() }}</div>
                                    <div class="stat-label">Medicamentos</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-number">{{ $apiario->visitas->where('tipo_visita', 'Alimentación')->count() }}</div>
                                    <div class="stat-label">Alimentación</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-number">{{ $apiario->visitas->where('tipo_visita', 'Inspección de Reina')->count() }}</div>
                                    <div class="stat-label">Reina</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($apiario->visitas->isEmpty())
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <h4>No hay visitas registradas</h4>
                            <p>Este apiario aún no tiene visitas registradas en el sistema.</p>
                            <a href="{{ route('visitas') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i>Registrar Primera Visita
                            </a>
                        </div>
                    @else
                        <!-- Filter and Search Bar -->
                        <div class="filter-section mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-6 col-lg-4">
                                    <div class="search-box">
                                        <i class="fas fa-search"></i>
                                        <input type="text" id="searchInput" placeholder="Buscar en todas las visitas..." class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="date-filter">
                                        <select id="dateFilter" class="form-select">
                                            <option value="">Todas las fechas</option>
                                            <option value="last-week">Última semana</option>
                                            <option value="last-month">Último mes</option>
                                            <option value="last-3-months">Últimos 3 meses</option>
                                            <option value="last-year">Último año</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Statistics Cards -->
                        <div class="mobile-stats d-lg-none mb-4">
                            <div class="row">
                                <div class="col-3">
                                    <div class="mobile-stat-card">
                                        <div class="mobile-stat-number">{{ $apiario->visitas->count() }}</div>
                                        <div class="mobile-stat-label">Total</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="mobile-stat-card">
                                        <div class="mobile-stat-number">{{ $apiario->visitas->where('tipo_visita', 'Visita General')->count() }}</div>
                                        <div class="mobile-stat-label">General</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="mobile-stat-card">
                                        <div class="mobile-stat-number">{{ $apiario->visitas->where('tipo_visita', 'Inspección de Visita')->count() }}</div>
                                        <div class="mobile-stat-label">Inspect.</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="mobile-stat-card">
                                        <div class="mobile-stat-number">{{ $apiario->visitas->where('tipo_visita', 'Uso de Medicamentos')->count() }}</div>
                                        <div class="mobile-stat-label">Medic.</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="mobile-stat-card">
                                        <div class="mobile-stat-number">{{ $apiario->visitas->where('tipo_visita', 'Alimentación')->count() }}</div>
                                        <div class="mobile-stat-label">Alim.</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="mobile-stat-card">
                                        <div class="mobile-stat-number">{{ $apiario->visitas->where('tipo_visita', 'Inspección de Reina')->count() }}</div>
                                        <div class="mobile-stat-label">Reina</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Custom Tabs -->
                        <div class="custom-tabs-container mb-4">
                            <ul class="custom-nav-tabs" id="visitTabs" role="tablist">
                                <li class="custom-nav-item" role="presentation">
                                    <button class="custom-nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                        <i class="fas fa-users me-2"></i>
                                        <span class="tab-text">Visitas Generales</span>
                                        <span class="tab-badge">{{ $apiario->visitas->where('tipo_visita', 'Visita General')->count() }}</span>
                                    </button>
                                </li>
                                <li class="custom-nav-item" role="presentation">
                                    <button class="custom-nav-link" id="inspeccion-tab" data-bs-toggle="tab" data-bs-target="#inspeccion" type="button" role="tab">
                                        <i class="fas fa-search me-2"></i>
                                        <span class="tab-text">Inspecciones</span>
                                        <span class="tab-badge">{{ $apiario->visitas->where('tipo_visita', 'Inspección de Visita')->count() }}</span>
                                    </button>
                                </li>
                                <li class="custom-nav-item" role="presentation">
                                    <button class="custom-nav-link" id="medicamentos-tab" data-bs-toggle="tab" data-bs-target="#medicamentos" type="button" role="tab">
                                        <i class="fas fa-pills me-2"></i>
                                        <span class="tab-text">Medicamentos</span>
                                        <span class="tab-badge">{{ $apiario->visitas->where('tipo_visita', 'Uso de Medicamentos')->count() }}</span>
                                    </button>
                                </li>
                                <li class="custom-nav-item" role="presentation">
                                    <button class="custom-nav-link" id="alimentacion-tab" data-bs-toggle="tab" data-bs-target="#alimentacion" type="button" role="tab">
                                        <i class="fas fa-pills me-2"></i>
                                        <span class="tab-text">Alimentación</span>
                                        <span class="tab-badge">{{ $apiario->visitas->where('tipo_visita', 'Alimentación')->count() }}</span>
                                    </button>
                                </li>
                                <li class="custom-nav-item" role="presentation">
                                    <button class="custom-nav-link" id="alimentacion-tab" data-bs-toggle="tab" data-bs-target="#reina" type="button" role="tab">
                                        <i class="fas fa-pills me-2"></i>
                                        <span class="tab-text">Reina</span>
                                        <span class="tab-badge">{{ $apiario->visitas->where('tipo_visita', 'Inspección de Reina')->count() }}</span>
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content custom-tab-content" id="visitTabsContent">
                            <!-- Visitas Generales Tab -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel">
                                @if($apiario->visitas->where('tipo_visita', 'Visita General')->isEmpty())
                                    <div class="no-data-message">
                                        <i class="fas fa-info-circle me-2"></i>
                                        No hay visitas generales registradas.
                                    </div>
                                @else
                                    <div class="table-container">
                                        <div class="table-header">
                                            <h5><i class="fas fa-users me-2"></i>Visitas Generales ({{ $apiario->visitas->where('tipo_visita', 'Visita General')->count() }} registros)</h5>
                                            <small class="text-muted">Registro de todas las visitas generales al apiario</small>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="custom-table" id="generalTable">
                                                <thead>
                                                    <tr>
                                                        <th><i class="fas fa-calendar me-1"></i> Fecha</th>
                                                        <th><i class="fas fa-user me-1"></i> Nombres</th>
                                                        <th><i class="fas fa-user me-1"></i> Apellidos</th>
                                                        <th><i class="fas fa-id-card me-1"></i> RUT</th>
                                                        <th><i class="fas fa-comment me-1"></i> Motivo</th>
                                                        <th><i class="fas fa-phone me-1"></i> Teléfono</th>
                                                        <th><i class="fas fa-signature me-1"></i> Firma</th>
                                                        <th><i class="fas fa-clock me-1"></i> Duración</th>
                                                        <th><i class="fas fa-clipboard me-1"></i> Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($apiario->visitas->where('tipo_visita', 'Visita General')->sortByDesc('fecha_visita') as $visita)
                                                        <tr class="table-row" data-date="{{ $visita->fecha_visita }}">
                                                            <td class="date-cell">
                                                                <span class="date-main">
                                                                    @if ($visita->fecha_visita)
                                                                        {{ date('d/m/Y', strtotime($visita->fecha_visita)) }}
                                                                    @else
                                                                        N/A
                                                                    @endif
                                                                </span>
                                                            </td>
                                                            <td class="visitor-name">{{ $visita->visitaGeneral->nombres ?? '---' }}</td>
                                                            <td class="visitor-surname">{{ $visita->visitaGeneral->apellidos ?? '---' }}</td>
                                                            <td class="rut-cell">{{ $visita->visitaGeneral->rut ?? '---' }}</td>
                                                            <td class="motivo-cell">{{ $visita->visitaGeneral->motivo ?? '---' }}</td>
                                                            <td class="phone-cell">{{ $visita->visitaGeneral->telefono ?? '---' }}</td>
                                                            <td class="signature-cell">{{ $visita->visitaGeneral->firma ?? '---' }}</td>
                                                            <td class="duration-cell">
                                                                <span class="duration-badge">{{ $visita->duracion_visita ?? 'No especificado' }}</span>
                                                            </td>
                                                            <td class="actions-cell">
                                                                <a href="{{ route('generate.document.visitas', $apiario->id) }}"
                                                                    class="btn btn-outline-secondary btn-sm" title="Generar PDF" target="_blank">
                                                                    <i class="fas fa-file-pdf"></i>
                                                                </a>
                                                                <a href="{{ route('visitas.visitas-general', $apiario->id) }}?visita_id={{ $visita->id }}"
                                                                    class="btn btn-outline-primary btn-sm">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Inspecciones Tab -->
                            <div class="tab-pane fade" id="inspeccion" role="tabpanel">
                                @if($apiario->visitas->where('tipo_visita', 'Inspección de Visita')->isEmpty())
                                    <div class="no-data-message">
                                        <i class="fas fa-info-circle me-2"></i>
                                        No hay inspecciones registradas.
                                    </div>
                                @else
                                    <div class="table-container">
                                        <div class="table-header">
                                            <h5><i class="fas fa-search me-2"></i>Inspecciones ({{ $apiario->visitas->where('tipo_visita', 'Inspección de Visita')->count() }} registros)</h5>
                                            <small class="text-muted">Control y seguimiento del estado de las colmenas</small>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="custom-table inspection-table" id="inspectionTable">
                                                <thead>
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Totales</th>
                                                        <th>Activas</th>
                                                        <th>Enfermas</th>
                                                        <th>Muertas</th>
                                                        <th>Inspeccionadas</th>
                                                        <th>Flujo N/P</th>
                                                        <th>Revisor</th>
                                                        <th>Sospecha</th>
                                                        <th>Observaciones</th>
                                                        <th>Estado</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($apiario->visitas->where('tipo_visita', 'Inspección de Visita')->sortByDesc('fecha_visita') as $visita)
                                                        @php $inspeccion = $visita->inspeccion; @endphp
                                                        <tr class="table-row" data-date="{{ $visita->fecha_visita }}">
                                                            <td class="date-cell">
                                                                <div class="date-container">
                                                                    <span class="date-main">{{ \Carbon\Carbon::parse($visita->fecha_visita)->format('d/m/Y') }}</span>
                                                                </div>
                                                            </td>
                                                            <td class="number-cell">{{ $inspeccion->num_colmenas_totales ?? 'N/A' }}</td>
                                                            <td class="number-cell active">{{ $inspeccion->num_colmenas_activas ?? 'N/A' }}</td>
                                                            <td class="number-cell sick">{{ $inspeccion->num_colmenas_enfermas ?? 'N/A' }}</td>
                                                            <td class="number-cell dead">{{ $inspeccion->num_colmenas_muertas ?? 'N/A' }}</td>
                                                            <td class="number-cell">{{ $inspeccion->num_colmenas_inspeccionadas ?? 'N/A' }}</td>
                                                            <td class="flujo-cell">
                                                                <span class="flujo-badge flujo-{{ strtolower(str_replace(' ', '-', $inspeccion->flujo_nectar_polen ?? 'normal')) }}">
                                                                    {{ $inspeccion->flujo_nectar_polen ?? 'N/A' }}
                                                                </span>
                                                            </td>
                                                            <td class="revisor-cell">{{ $inspeccion->nombre_revisor_apiario ?? 'N/A' }}</td>
                                                            <td class="suspicion-cell">
                                                                @if(!empty($inspeccion?->sospecha_enfermedad) && $inspeccion->sospecha_enfermedad !== 'N/A')
                                                                    <span class="suspicion-badge suspicion-yes">{{ $inspeccion->sospecha_enfermedad }}</span>
                                                                @else
                                                                    <span class="suspicion-badge suspicion-no">Sin sospecha</span>
                                                                @endif
                                                            </td>
                                                            <td class="observations-cell">{{ $inspeccion->observaciones ?? 'N/A' }}</td>
                                                            <td class="status-cell">
                                                                @php
                                                                    $totalColmenas = $inspeccion->num_colmenas_totales ?? 0;
                                                                    $colmenasEnfermas = $inspeccion->num_colmenas_enfermas ?? 0;
                                                                    $colmenasMuertas = $inspeccion->num_colmenas_muertas ?? 0;

                                                                    if ($colmenasEnfermas > 0 || $colmenasMuertas > 0) {
                                                                        $status = 'Requiere Atención';
                                                                        $statusClass = 'warning';
                                                                    } elseif ($totalColmenas > 0) {
                                                                        $status = 'Saludable';
                                                                        $statusClass = 'success';
                                                                    } else {
                                                                        $status = 'Sin Datos';
                                                                        $statusClass = 'secondary';
                                                                    }
                                                                @endphp
                                                                <span class="status-badge status-{{ $statusClass }}">{{ $status }}</span>
                                                            </td>
                                                            <td class="actions-cell">
                                                                <a href="{{ route('generate.document.inspeccion', $apiario->id) }}"
                                                                class="btn btn-outline-secondary btn-sm" title="Generar PDF" target="_blank">
                                                                    <i class="fas fa-file-pdf"></i>
                                                                </a>
                                                                <a href="{{ route('visitas.create', $apiario->id) }}?visita_id={{ $visita->id }}"
                                                                class="btn btn-outline-primary btn-sm">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>


                            <!-- Medicamentos Tab -->
                            <div class="tab-pane fade" id="medicamentos" role="tabpanel">
                                @php
                                    // obtienes las visitas de medicamentos
                                    $meds = $apiario->visitas->where('tipo_visita','Uso de Medicamentos');
                                    // las de Varroa o Nosemosis
                                    $tiposConDetalle = ['varroa','nosema'];
                                    $conDetalle = $meds->filter(fn($v) => in_array(strtolower($v->motivo), $tiposConDetalle));
                                    // el resto
                                    $otros     = $meds->diff($conDetalle);
                                @endphp

                                {{-- sección Varroa/Nosemosis --}}
                                @if($conDetalle->isNotEmpty())
                                <h5>Uso de Medicamentos ({{ $conDetalle->count() }} con detalle)</h5>
                                <table class="custom-table">
                                    <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Motivo</th>
                                        <th>Nombre Comercial</th>
                                        <th>Principio Activo</th>
                                        <th>Presencia</th>
                                        <th>Periodo Resguardo</th>
                                        <th>Responsable</th>
                                        <th>Observaciones</th>
                                        <th>Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($conDetalle->sortByDesc('fecha_visita') as $v)
                                        @php
                                        $mot   = strtolower($v->motivo);
                                        $med   = $mot==='varroa' ? $v->presenciaVarroa : $v->presenciaNosemosis;
                                        @endphp
                                        <tr>
                                        <td>{{ \Carbon\Carbon::parse($v->fecha_visita)->format('d/m/Y') }}</td>
                                        <td>{{ ucfirst($mot) }}</td>
                                        <td>{{ $med->producto_comercial ?? '-' }}</td>
                                        <td>{{ $med->ingrediente_activo   ?? '-' }}</td>
                                        <td>{{ $med
                                                ? ($mot==='varroa'
                                                        ? $med->diagnostico_visual
                                                        : $med->signos_clinicos)
                                                : '-' }}</td>
                                        <td>{{ $v->periodo_resguardo ?: 'No especificado' }}</td>
                                        <td>{{ $v->responsable }}</td>
                                        <td>{{ $v->observaciones }}</td>
                                        <td class="actions-cell">
                                                <a href="{{ route('generate.document.medicamentos', $apiario->id) }}"
                                                class="btn btn-outline-secondary btn-sm" title="Generar PDF" target="_blank">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                <a href="{{ route('apiarios.medicamentos-registro.edit', [$apiario->id, $v->id]) }}"
                                                class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                @endif

                                {{-- sección Otros motivos --}}
                                @if($otros->isNotEmpty())
                                <h5>Uso de Medicamentos – Otros Motivos ({{ $otros->count() }})</h5>
                                <table class="custom-table">
                                    <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Motivo</th>
                                        <th>Responsable</th>
                                        <th>Observaciones</th>
                                        <th>Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($otros->sortByDesc('fecha_visita') as $v)
                                        <tr>
                                        <td>{{ \Carbon\Carbon::parse($v->fecha_visita)->format('d/m/Y') }}</td>
                                        <td>{{ ucfirst($v->motivo) }}</td>
                                        <td>{{ $v->responsable }}</td>
                                        <td>{{ $v->observaciones }}</td>
                                        <td class="actions-cell">
                                                <a href="{{ route('generate.document.medicamentos', $apiario->id) }}"
                                                class="btn btn-outline-secondary btn-sm" title="Generar PDF" target="_blank">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                <a href="{{ route('apiarios.medicamentos-registro.edit', [$apiario->id, $v->id]) }}"
                                                class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                @endif
                            </div>

                            <!-- Alimentaciones Tab -->
                            <div class="tab-pane fade" id="alimentacion" role="tabpanel">
                                @if($apiario->visitas->where('tipo_visita', 'Alimentación')->isEmpty())
                                    <div class="no-data-message">
                                        <i class="fas fa-info-circle me-2"></i>
                                        No hay registros de alimentación.
                                    </div>
                                @else
                                    <div class="table-container">
                                        <div class="table-header">
                                            <h5><i class="fas fa-utensils me-2"></i>Alimentación ({{ $apiario->visitas->where('tipo_visita', 'Alimentación')->count() }} registros)</h5>
                                            <small class="text-muted">Historial de alimentos e insumos utilizados en el apiario.</small>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="custom-table" id="alimentacionTable"> {{-- <- Le ponemos un id único --}}
                                                <thead>
                                                    <tr>
                                                        <th>Tipo de Alimentación</th>
                                                        <th>Fecha de Aplicación</th>
                                                        <th>Insumo Utilizado</th>
                                                        <th>Objetivo</th>
                                                        <th>Dosificación</th>
                                                        <th>Método Utilizado</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                @php
                                                    $colmenasIds = $apiario->colmenas->pluck('id'); // ← IDs de las colmenas del apiario
                                                    $estado = \App\Models\EstadoNutricional::whereIn('colmena_id', $colmenasIds)
                                                                ->latest('fecha_aplicacion')
                                                                ->first();
                                                @endphp

                                                <tbody>
                                                @if ($estado)
                                                    <tr>
                                                        <td>{{ $estado->tipo_alimentacion ?? 'N/A' }}</td>
                                                        <td>{{ date('Y-m-d', strtotime($estado->fecha_aplicacion)) ?? 'N/A' }}</td>
                                                        <td>{{ $estado->insumo_utilizado ?? 'N/A' }}</td>
                                                        <td>{{ $estado->objetivo ?? 'N/A' }}</td>
                                                        <td>{{ $estado->dosifiacion ?? 'N/A' }}</td>
                                                        <td>{{ $estado->metodo_utilizado ?? 'N/A' }}</td>
                                                        <td class="actions-cell">
                                                            <a href="{{ route('generate.document.alimentacion', $apiario->id) }}"
                                                                class="btn btn-outline-secondary btn-sm" title="Generar PDF" target="_blank">
                                                                <i class="fas fa-file-pdf"></i>
                                                            </a>
                                                            <a href="{{ route('visitas.alimentacion.edit', [$apiario->id, $estado->visita_id]) }}"
                                                                class="btn btn-outline-primary btn-sm">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted">No hay datos de alimentación registrados.</td>
                                                    </tr>
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Reina Tab -->
                            <div class="tab-pane fade" id="reina" role="tabpanel"> 
                                @if($apiario->visitas->where('tipo_visita', 'Inspección de Reina')->isEmpty())
                                    <div class="no-data-message">
                                        <i class="fas fa-info-circle me-2"></i>
                                        No hay registros de reina.
                                    </div>
                                @else
                                    <div class="table-container">
                                        <div class="table-header">
                                            <h5><i class="fas fa-utensils me-2"></i>Reina ({{ $apiario->visitas->where('tipo_visita', 'Inspección de Reina')->count() }} registros)</h5>
                                            <small class="text-muted">Historial de reina y reemplazos realizados.</small>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="custom-table" id="reinaTable">
                                                <thead>
                                                    <tr>
                                                        <th>Postura Reina</th>
                                                        <th>Estado Cria</th>
                                                        <th>Postura Zánganos</th>
                                                        <th>Origen Reina</th>
                                                        <th>Raza</th>
                                                        <th>Linea Genetica</th>
                                                        <th>Fecha Introducción</th>
                                                        <th>Estado Actual</th>
                                                        <th>Fecha Reemplazo</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                @php
                                                    $colmenasIds = $apiario->colmenas->pluck('id');
                                                    $reina = \App\Models\CalidadReina::whereIn('colmena_id', $colmenasIds)
                                                                ->latest('fecha_introduccion')
                                                                ->first();

                                                    $reemplazos = [];
                                                    $ultimo     = null;

                                                    if ($reina) {
                                                        $raw = $reina->reemplazos_realizados;
                                                        if (is_string($raw)) {
                                                            // si viene como string, lo decodificamos
                                                            $reemplazos = json_decode($raw, true) ?: [];
                                                        } elseif (is_array($raw)) {
                                                            // si ya es array (cast de Eloquent), lo usamos directamente
                                                            $reemplazos = $raw;
                                                        }
                                                        // tomamos el último reemplazo (si existe)
                                                        $ultimo = $reemplazos ? end($reemplazos) : null;
                                                    }
                                                @endphp

                                                <tbody>
                                                @if ($reina)
                                                    <tr>
                                                        <td>{{ $reina->postura_reina }}</td>
                                                        <td>{{ $reina->estado_cria }}</td>
                                                        <td>{{ $reina->postura_zanganos }}</td>
                                                        <td>{{ $reina->origen_reina }}</td>
                                                        <td>{{ $reina->raza }}</td>
                                                        <td>{{ $reina->linea_genetica }}</td>
                                                        <td>{{ $reina->fecha_introduccion ? \Carbon\Carbon::parse($reina->fecha_introduccion)->format('d/m/Y') : '-' }}</td>
                                                        <td>{{ $reina->estado_actual }}</td>
                                                        <td>
                                                            @if($ultimo && ! empty($ultimo['fecha']))
                                                                {{ \Carbon\Carbon::parse($ultimo['fecha'])->format('d/m/Y') }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="actions-cell">
                                                            @if($apiario->calidadesReina->count())
                                                                <a href="{{ route('generate.document.reina', $apiario->id) }}"
                                                                    class="btn btn-outline-secondary btn-sm" title="Generar PDF" target="_blank">
                                                                    <i class="fas fa-file-pdf"></i>
                                                                </a>
                                                            @endif
                                                            <a href="{{ route('visitas.reina.edit', [$apiario->id, $reina->visita_id]) }}"
                                                                class="btn btn-outline-primary btn-sm">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted">No hay datos de reina registrados.</td>
                                                    </tr>
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="action-section mt-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('visitas') }}" class="custom-btn-back">
                                <i class="fas fa-arrow-left me-2"></i>
                                Volver a Mis Apiarios
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar tabs de Bootstrap
            var triggerTabList = [].slice.call(document.querySelectorAll('#visitTabs button'))
            triggerTabList.forEach(function (triggerEl) {
                var tabTrigger = new bootstrap.Tab(triggerEl)

                triggerEl.addEventListener('click', function (event) {
                    event.preventDefault()
                    tabTrigger.show()
                })
            })

            // Activar la primera pestaña
            var firstTab = document.querySelector('#general-tab');
            if (firstTab) {
                var tab = new bootstrap.Tab(firstTab);
                tab.show();
            }

            // Función de búsqueda
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const tables = document.querySelectorAll('.custom-table tbody');
                    
                    tables.forEach(table => {
                        const rows = table.querySelectorAll('tr');
                        rows.forEach(row => {
                            const text = row.textContent.toLowerCase();
                            if (text.includes(searchTerm)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    });
                });
            }

            // Filtro por fecha
            const dateFilter = document.getElementById('dateFilter');
            if (dateFilter) {
                dateFilter.addEventListener('change', function() {
                    const filterValue = this.value;
                    const rows = document.querySelectorAll('.table-row');
                    const now = new Date();
                    
                    rows.forEach(row => {
                        const dateStr = row.getAttribute('data-date');
                        const rowDate = new Date(dateStr);
                        let show = true;
                        
                        switch(filterValue) {
                            case 'last-week':
                                const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                                show = rowDate >= weekAgo;
                                break;
                            case 'last-month':
                                const monthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
                                show = rowDate >= monthAgo;
                                break;
                            case 'last-3-months':
                                const threeMonthsAgo = new Date(now.getTime() - 90 * 24 * 60 * 60 * 1000);
                                show = rowDate >= threeMonthsAgo;
                                break;
                            case 'last-year':
                                const yearAgo = new Date(now.getTime() - 365 * 24 * 60 * 60 * 1000);
                                show = rowDate >= yearAgo;
                                break;
                            default:
                                show = true;
                        }
                        
                        row.style.display = show ? '' : 'none';
                    });
                });
            }

            // Animaciones suaves al cambiar tabs
            document.querySelectorAll('#visitTabs button').forEach(function (button) {
                button.addEventListener('shown.bs.tab', function (event) {
                    var target = document.querySelector(event.target.getAttribute('data-bs-target'));
                    if (target) {
                        target.style.opacity = '0';
                        target.style.transform = 'translateY(20px)';

                        setTimeout(function () {
                            target.style.opacity = '1';
                            target.style.transform = 'translateY(0)';
                        }, 50);
                    }
                });
            });
        });
    </script>
@endsection