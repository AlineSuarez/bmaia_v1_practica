@extends('layouts.app')

@section('title', 'Historial de Visitas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/components/home-user/record.css') }}">
@endpush

@section('content')
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

                        <!-- Statistics Cards Desktop -->
                        <div class="stats-container d-none d-lg-flex">
                            <div class="stat-card">
                                <div class="stat-number">
                                    {{ $apiario->visitas->where('tipo_visita', 'Visita General')->count() }}</div>
                                <div class="stat-label">Generales</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number">
                                    {{ $apiario->visitas->where('tipo_visita', 'Inspección de Visita')->count() }}</div>
                                <div class="stat-label">Inspecciones</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number">
                                    {{ $apiario->visitas->where('tipo_visita', 'Uso de Medicamentos')->count() }}</div>
                                <div class="stat-label">Medicamentos</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number">
                                    {{ $apiario->visitas->where('tipo_visita', 'Alimentación')->count() }}</div>
                                <div class="stat-label">Alimentación</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number">
                                    {{ $apiario->visitas->where('tipo_visita', 'Inspección de Reina')->count() }}</div>
                                <div class="stat-label">Reina</div>
                            </div>
                        </div>
                    </div>

                    <!-- Location and Date Info -->
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <div class="date-range-info">
                            <i class="fas fa-calendar-alt me-2"></i>
                            <span>Último registro:
                                {{ $apiario->visitas->max('fecha_visita') ? \Carbon\Carbon::parse($apiario->visitas->max('fecha_visita'))->format('d/m/Y') : 'Sin registros' }}</span>
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
                                    <input type="text" id="searchInput" placeholder="Buscar en todas las visitas..."
                                        class="form-control">
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
                        <div class="row g-2">
                            <div class="col-4">
                                <div class="mobile-stat-card">
                                    <div class="mobile-stat-number">
                                        {{ $apiario->visitas->where('tipo_visita', 'Visita General')->count() }}</div>
                                    <div class="mobile-stat-label">General</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mobile-stat-card">
                                    <div class="mobile-stat-number">
                                        {{ $apiario->visitas->where('tipo_visita', 'Inspección de Visita')->count() }}</div>
                                    <div class="mobile-stat-label">Inspect.</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mobile-stat-card">
                                    <div class="mobile-stat-number">
                                        {{ $apiario->visitas->where('tipo_visita', 'Uso de Medicamentos')->count() }}</div>
                                    <div class="mobile-stat-label">Medic.</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mobile-stat-card">
                                    <div class="mobile-stat-number">
                                        {{ $apiario->visitas->where('tipo_visita', 'Alimentación')->count() }}</div>
                                    <div class="mobile-stat-label">Alim.</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mobile-stat-card">
                                    <div class="mobile-stat-number">
                                        {{ $apiario->visitas->where('tipo_visita', 'Inspección de Reina')->count() }}</div>
                                    <div class="mobile-stat-label">Reina</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Custom Tabs -->
                    <div class="custom-tabs-container mb-4">
                        <ul class="custom-nav-tabs" id="visitTabs" role="tablist">
                            <li class="custom-nav-item" role="presentation">
                                <button class="custom-nav-link active" id="general-tab" data-bs-toggle="tab"
                                    data-bs-target="#general" type="button" role="tab">
                                    <i class="fas fa-users me-2"></i>
                                    <span class="tab-text">Visitas Generales</span>
                                    <span
                                        class="tab-badge">{{ $apiario->visitas->where('tipo_visita', 'Visita General')->count() }}</span>
                                </button>
                            </li>
                            <li class="custom-nav-item" role="presentation">
                                <button class="custom-nav-link" id="inspeccion-tab" data-bs-toggle="tab"
                                    data-bs-target="#inspeccion" type="button" role="tab">
                                    <i class="fas fa-search me-2"></i>
                                    <span class="tab-text">Inspecciones</span>
                                    <span
                                        class="tab-badge">{{ $apiario->visitas->where('tipo_visita', 'Inspección de Visita')->count() }}</span>
                                </button>
                            </li>
                            <li class="custom-nav-item" role="presentation">
                                <button class="custom-nav-link" id="medicamentos-tab" data-bs-toggle="tab"
                                    data-bs-target="#medicamentos" type="button" role="tab">
                                    <i class="fas fa-pills me-2"></i>
                                    <span class="tab-text">Medicamentos</span>
                                    <span
                                        class="tab-badge">{{ $apiario->visitas->where('tipo_visita', 'Uso de Medicamentos')->count() }}</span>
                                </button>
                            </li>
                            <li class="custom-nav-item" role="presentation">
                                <button class="custom-nav-link" id="alimentacion-tab" data-bs-toggle="tab"
                                    data-bs-target="#alimentacion" type="button" role="tab">
                                    <i class="fas fa-utensils me-2"></i>
                                    <span class="tab-text">Alimentación</span>
                                    <span
                                        class="tab-badge">{{ $apiario->visitas->where('tipo_visita', 'Alimentación')->count() }}</span>
                                </button>
                            </li>
                            <li class="custom-nav-item" role="presentation">
                                <button class="custom-nav-link" id="reina-tab" data-bs-toggle="tab" data-bs-target="#reina"
                                    type="button" role="tab">
                                    <i class="fas fa-crown me-2"></i>
                                    <span class="tab-text">Reina</span>
                                    <span
                                        class="tab-badge">{{ $apiario->visitas->where('tipo_visita', 'Inspección de Reina')->count() }}</span>
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
                                        <div class="table-header-content">
                                            <h5><i class="fas fa-users me-2"></i>Visitas Generales
                                                ({{ $apiario->visitas->where('tipo_visita', 'Visita General')->count() }} registros)
                                            </h5>
                                            <small class="text-muted">Registro de todas las visitas generales al apiario</small>
                                        </div>
                                        <div class="table-header-actions">
                                            <a href="{{ route('visitas.visitas-general', $apiario->id) }}" class="custom-action-btn btn-sm">
                                                <i class="fas fa-plus"></i>
                                                <span>Nuevo Registro</span>
                                            </a>
                                        </div>
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
                                                    <th><i class="fas fa-clock me-1"></i> Duración</th>
                                                    <th><i class="fas fa-cogs me-1"></i> Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($apiario->visitas->where('tipo_visita', 'Visita General')->sortByDesc('fecha_visita') as $visita)
                                                    <tr class="table-row" data-date="{{ $visita->fecha_visita }}">
                                                        <td class="date-cell">
                                                            <div class="date-container">
                                                                <span class="date-main">
                                                                    @if ($visita->fecha_visita)
                                                                        {{ \Carbon\Carbon::parse($visita->fecha_visita)->format('d/m/Y') }}
                                                                    @else
                                                                        N/A
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="visitor-name">{{ $visita->visitaGeneral->nombres ?? '---' }}</td>
                                                        <td class="visitor-surname">{{ $visita->visitaGeneral->apellidos ?? '---' }}
                                                        </td>
                                                        <td class="rut-cell">{{ $visita->visitaGeneral->rut ?? '---' }}</td>
                                                        <td class="motivo-cell">
                                                            <span
                                                                class="motivo-text">{{ $visita->visitaGeneral->motivo ?? '---' }}</span>
                                                        </td>
                                                        <td class="phone-cell">{{ $visita->visitaGeneral->telefono ?? '---' }}</td>
                                                        <td class="duration-cell">
                                                            <span
                                                                class="duration-badge">{{ $visita->duracion_visita ?? 'No especificado' }}</span>
                                                        </td>
                                                        <td class="actions-cell">
                                                            <div class="btn-group" role="group">
                                                                <a href="{{ route('generate.document.visitas', $apiario->id) }}"
                                                                    class="btn btn-outline-secondary btn-sm" title="Generar PDF"
                                                                    target="_blank">
                                                                    <i class="fas fa-file-pdf"></i>
                                                                </a>
                                                                <a href="{{ route('visitas.visitas-general', $apiario->id) }}?visita_id={{ $visita->id }}"
                                                                    class="btn btn-outline-primary btn-sm" title="Editar">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            </div>
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
                                        <div class="table-header-content">
                                            <h5><i class="fas fa-search me-2"></i>Inspecciones
                                                ({{ $apiario->visitas->where('tipo_visita', 'Inspección de Visita')->count() }}
                                                registros)</h5>
                                            <small class="text-muted">Control y seguimiento del estado de las colmenas</small>
                                        </div>
                                        <div class="table-header-actions">
                                            <a href="{{ route('visitas.create', $apiario->id) }}" class="custom-action-btn btn-sm">
                                                <i class="fas fa-plus"></i>
                                                <span>Nuevo Registro</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="custom-table inspection-table" id="inspectionTable">
                                            <thead>
                                                <tr>
                                                    <th><i class="fas fa-calendar me-1"></i> Fecha</th>
                                                    <th><i class="fas fa-calculator me-1"></i> Totales</th>
                                                    <th><i class="fas fa-heart me-1"></i> Activas</th>
                                                    <th><i class="fas fa-exclamation-triangle me-1"></i> Enfermas</th>
                                                    <th><i class="fas fa-times-circle me-1"></i> Muertas</th>
                                                    <th><i class="fas fa-eye me-1"></i> Inspeccionadas</th>
                                                    <th><i class="fas fa-tint me-1"></i> Flujo N/P</th>
                                                    <th><i class="fas fa-user-check me-1"></i> Revisor</th>
                                                    <th><i class="fas fa-exclamation me-1"></i> Sospecha</th>
                                                    <th><i class="fas fa-comment me-1"></i> Observaciones</th>
                                                    <th><i class="fas fa-flag me-1"></i> Estado</th>
                                                    <th><i class="fas fa-cogs me-1"></i> Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($apiario->visitas->where('tipo_visita', 'Inspección de Visita')->sortByDesc('fecha_visita') as $visita)
                                                    @php $inspeccion = $visita->inspeccion; @endphp
                                                    <tr class="table-row" data-date="{{ $visita->fecha_visita }}">
                                                        <td class="date-cell">
                                                            <div class="date-container">
                                                                <span
                                                                    class="date-main">{{ \Carbon\Carbon::parse($visita->fecha_visita)->format('d/m/Y') }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="number-cell">{{ $inspeccion->num_colmenas_totales ?? 'N/A' }}</td>
                                                        <td class="number-cell active">{{ $inspeccion->num_colmenas_activas ?? 'N/A' }}
                                                        </td>
                                                        <td class="number-cell sick">{{ $inspeccion->num_colmenas_enfermas ?? 'N/A' }}
                                                        </td>
                                                        <td class="number-cell dead">{{ $inspeccion->num_colmenas_muertas ?? 'N/A' }}
                                                        </td>
                                                        <td class="number-cell">{{ $inspeccion->num_colmenas_inspeccionadas ?? 'N/A' }}
                                                        </td>
                                                        <td class="flujo-cell">
                                                            <span
                                                                class="flujo-badge flujo-{{ strtolower(str_replace(' ', '-', $inspeccion->flujo_nectar_polen ?? 'normal')) }}">
                                                                {{ $inspeccion->flujo_nectar_polen ?? 'N/A' }}
                                                            </span>
                                                        </td>
                                                        <td class="revisor-cell">{{ $inspeccion->nombre_revisor_apiario ?? 'N/A' }}</td>
                                                        <td class="suspicion-cell">
                                                            @if(!empty($inspeccion?->sospecha_enfermedad) && $inspeccion->sospecha_enfermedad !== 'N/A')
                                                                <span
                                                                    class="suspicion-badge suspicion-yes">{{ $inspeccion->sospecha_enfermedad }}</span>
                                                            @else
                                                                <span class="suspicion-badge suspicion-no">Sin sospecha</span>
                                                            @endif
                                                        </td>
                                                        <td class="observations-cell">
                                                            <span class="motivo-text">{{ $inspeccion->observaciones ?? 'N/A' }}</span>
                                                        </td>
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
                                                            <div class="btn-group" role="group">
                                                                <a href="{{ route('generate.document.inspeccion', $apiario->id) }}"
                                                                    class="btn btn-outline-secondary btn-sm" title="Generar PDF"
                                                                    target="_blank">
                                                                    <i class="fas fa-file-pdf"></i>
                                                                </a>
                                                                <a href="{{ route('visitas.create', $apiario->id) }}?visita_id={{ $visita->id }}"
                                                                    class="btn btn-outline-primary btn-sm" title="Editar">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            </div>
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
                                $meds = $apiario->visitas->where('tipo_visita', 'Uso de Medicamentos');
                                $tiposConDetalle = ['varroa', 'nosema'];
                                $conDetalle = $meds->filter(fn($v) => in_array(strtolower($v->motivo), $tiposConDetalle));
                                $otros = $meds->diff($conDetalle);
                            @endphp

                            @if($meds->isEmpty())
                                <div class="no-data-message">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No hay registros de medicamentos.
                                </div>
                            @else
                                {{-- Sección Varroa/Nosemosis --}}
                                @if($conDetalle->isNotEmpty())
                                    <div class="table-container mb-4">
                                        <div class="table-header">
                                            <div class="table-header-content">
                                                <h5><i class="fas fa-pills me-2"></i>Uso de Medicamentos - Con Detalle
                                                    ({{ $conDetalle->count() }} registros)</h5>
                                                <small class="text-muted">Tratamientos específicos para Varroa y Nosemosis</small>
                                            </div>
                                            <div class="table-header-actions">
                                                <a href="{{ route('visitas.medicamentos-registro', $apiario->id) }}" class="custom-action-btn btn-sm">
                                                    <i class="fas fa-plus"></i>
                                                    <span>Nuevo Tratamiento</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="custom-table medication-table">
                                                <thead>
                                                    <tr>
                                                        <th><i class="fas fa-calendar me-1"></i> Fecha</th>
                                                        <th><i class="fas fa-tag me-1"></i> Motivo</th>
                                                        <th><i class="fas fa-capsules me-1"></i> Nombre Comercial</th>
                                                        <th><i class="fas fa-atom me-1"></i> Principio Activo</th>
                                                        <th><i class="fas fa-eye me-1"></i> Presencia</th>
                                                        <th><i class="fas fa-clock me-1"></i> Periodo Resguardo</th>
                                                        <th><i class="fas fa-user-md me-1"></i> Responsable</th>
                                                        <th><i class="fas fa-comment me-1"></i> Observaciones</th>
                                                        <th><i class="fas fa-cogs me-1"></i> Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($conDetalle->sortByDesc('fecha_visita') as $v)
                                                        @php
                                                            $mot = strtolower($v->motivo);
                                                            $med = $mot === 'varroa' ? $v->presenciaVarroa : $v->presenciaNosemosis;
                                                        @endphp
                                                        <tr class="table-row" data-date="{{ $v->fecha_visita }}">
                                                            <td class="date-cell">
                                                                <div class="date-container">
                                                                    <span
                                                                        class="date-main">{{ \Carbon\Carbon::parse($v->fecha_visita)->format('d/m/Y') }}</span>
                                                                </div>
                                                            </td>
                                                            <td class="motivo-cell">
                                                                <span
                                                                    class="status-badge status-{{ $mot === 'varroa' ? 'danger' : 'warning' }}">
                                                                    {{ ucfirst($mot) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $med->producto_comercial ?? '-' }}</td>
                                                            <td>{{ $med->ingrediente_activo ?? '-' }}</td>
                                                            <td>{{ $med ? ($mot === 'varroa' ? $med->diagnostico_visual : $med->signos_clinicos) : '-' }}
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="period-badge {{ empty($v->periodo_resguardo) ? 'period-na' : '' }}">
                                                                    {{ $v->periodo_resguardo ?: 'No especificado' }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $v->responsable }}</td>
                                                            <td class="motivo-cell">
                                                                <span class="motivo-text">{{ $v->observaciones }}</span>
                                                            </td>
                                                            <td class="actions-cell">
                                                                <div class="btn-group" role="group">
                                                                    <a href="{{ route('generate.document.medicamentos', $apiario->id) }}"
                                                                        class="btn btn-outline-secondary btn-sm" title="Generar PDF"
                                                                        target="_blank">
                                                                        <i class="fas fa-file-pdf"></i>
                                                                    </a>
                                                                    <a href="{{ route('apiarios.medicamentos-registro.edit', [$apiario->id, $v->id]) }}"
                                                                        class="btn btn-outline-primary btn-sm" title="Editar">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                                {{-- Sección Otros motivos --}}
                                @if($otros->isNotEmpty())
                                    <div class="table-container">
                                        <div class="table-header">
                                            <div class="table-header-content">
                                                <h5><i class="fas fa-pills me-2"></i>Uso de Medicamentos - Otros Motivos
                                                    ({{ $otros->count() }} registros)</h5>
                                                <small class="text-muted">Otros tratamientos y medicamentos aplicados</small>
                                            </div>
                                            <div class="table-header-actions">
                                                <a href="{{ route('visitas.medicamentos-registro', $apiario->id) }}" class="custom-action-btn btn-sm">
                                                    <i class="fas fa-plus"></i>
                                                    <span>Nuevo Registro</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="custom-table">
                                                <thead>
                                                    <tr>
                                                        <th><i class="fas fa-calendar me-1"></i> Fecha</th>
                                                        <th><i class="fas fa-tag me-1"></i> Motivo</th>
                                                        <th><i class="fas fa-user-md me-1"></i> Responsable</th>
                                                        <th><i class="fas fa-comment me-1"></i> Observaciones</th>
                                                        <th><i class="fas fa-cogs me-1"></i> Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($otros->sortByDesc('fecha_visita') as $v)
                                                        <tr class="table-row" data-date="{{ $v->fecha_visita }}">
                                                            <td class="date-cell">
                                                                <div class="date-container">
                                                                    <span
                                                                        class="date-main">{{ \Carbon\Carbon::parse($v->fecha_visita)->format('d/m/Y') }}</span>
                                                                </div>
                                                            </td>
                                                            <td class="motivo-cell">
                                                                <span class="status-badge status-secondary">{{ ucfirst($v->motivo) }}</span>
                                                            </td>
                                                            <td>{{ $v->responsable }}</td>
                                                            <td class="motivo-cell">
                                                                <span class="motivo-text">{{ $v->observaciones }}</span>
                                                            </td>
                                                            <td class="actions-cell">
                                                                <div class="btn-group" role="group">
                                                                    <a href="{{ route('generate.document.medicamentos', $apiario->id) }}"
                                                                        class="btn btn-outline-secondary btn-sm" title="Generar PDF"
                                                                        target="_blank">
                                                                        <i class="fas fa-file-pdf"></i>
                                                                    </a>
                                                                    <a href="{{ route('apiarios.medicamentos-registro.edit', [$apiario->id, $v->id]) }}"
                                                                        class="btn btn-outline-primary btn-sm" title="Editar">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
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
                                        <div class="table-header-content">
                                            <h5><i class="fas fa-utensils me-2"></i>Alimentación
                                                ({{ $apiario->visitas->where('tipo_visita', 'Alimentación')->count() }} registros)</h5>
                                            <small class="text-muted">Historial de alimentos e insumos utilizados en el apiario</small>
                                        </div>
                                        <div class="table-header-actions">
                                            <a href="{{ route('visitas.create3', $apiario->id) }}" class="custom-action-btn btn-sm">
                                                <i class="fas fa-plus"></i>
                                                <span>Nuevo Registro</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="custom-table" id="alimentacionTable">
                                            <thead>
                                                <tr>
                                                    <th><i class="fas fa-calendar me-1"></i> Fecha</th>
                                                    <th><i class="fas fa-utensils me-1"></i> Tipo de Alimentación</th>
                                                    <th><i class="fas fa-box me-1"></i> Insumo Utilizado</th>
                                                    <th><i class="fas fa-bullseye me-1"></i> Objetivo</th>
                                                    <th><i class="fas fa-calculator me-1"></i> Dosificación</th>
                                                    <th><i class="fas fa-tools me-1"></i> Método Utilizado</th>
                                                    <th><i class="fas fa-cogs me-1"></i> Acciones</th>
                                                </tr>
                                            </thead>
                                            @php
                                                $colmenasIds = $apiario->colmenas->pluck('id');
                                                $estadosNutricionales = \App\Models\EstadoNutricional::whereIn('colmena_id', $colmenasIds)
                                                    ->orderByDesc('fecha_aplicacion')
                                                    ->get()
                                                    ->groupBy('visita_id');
                                            @endphp
                                            <tbody>
                                                @forelse($estadosNutricionales as $visitaId => $grupo)
                                                    @php
                                                        $estado = $grupo->first();
                                                    @endphp
                                                    <tr class="table-row" data-date="{{ $estado->fecha_aplicacion }}">
                                                        <td class="date-cell">
                                                            <div class="date-container">
                                                                <span
                                                                    class="date-main">{{ $estado->fecha_aplicacion ? \Carbon\Carbon::parse($estado->fecha_aplicacion)->format('d/m/Y') : 'N/A' }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="motivo-cell">
                                                            <span
                                                                class="status-badge status-success">{{ $estado->tipo_alimentacion ?? 'N/A' }}</span>
                                                        </td>
                                                        <td>{{ $estado->insumo_utilizado ?? 'N/A' }}</td>
                                                        <td>{{ $estado->objetivo ?? 'N/A' }}</td>
                                                        <td>{{ $estado->dosifiacion ?? 'N/A' }}</td>
                                                        <td>{{ $estado->metodo_utilizado ?? 'N/A' }}</td>
                                                        <td class="actions-cell">
                                                            <div class="btn-group" role="group">
                                                                <a href="{{ route('generate.document.alimentacion', $apiario->id) }}"
                                                                    class="btn btn-outline-secondary btn-sm" title="Generar PDF"
                                                                    target="_blank">
                                                                    <i class="fas fa-file-pdf"></i>
                                                                </a>
                                                                <a href="{{ route('visitas.alimentacion.edit', [$apiario->id, $estado->visita_id]) }}"
                                                                    class="btn btn-outline-primary btn-sm" title="Editar">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted">No hay datos de alimentación
                                                            registrados.</td>
                                                    </tr>
                                                @endforelse
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
                                        <div class="table-header-content">
                                            <h5><i class="fas fa-crown me-2"></i>Inspección de Reina
                                                ({{ $apiario->visitas->where('tipo_visita', 'Inspección de Reina')->count() }}
                                                registros)</h5>
                                            <small class="text-muted">Historial de inspecciones de reina y reemplazos realizados</small>
                                        </div>
                                        <div class="table-header-actions">
                                            <a href="{{ route('visitas.create4', $apiario->id) }}" class="custom-action-btn btn-sm">
                                                <i class="fas fa-plus"></i>
                                                <span>Nuevo Registro</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="custom-table" id="reinaTable">
                                            <thead>
                                                <tr>
                                                    <th><i class="fas fa-calendar me-1"></i> Fecha</th>
                                                    <th><i class="fas fa-egg me-1"></i> Postura Reina</th>
                                                    <th><i class="fas fa-baby me-1"></i> Estado Cría</th>
                                                    <th><i class="fas fa-male me-1"></i> Postura Zánganos</th>
                                                    <th><i class="fas fa-home me-1"></i> Origen Reina</th>
                                                    <th><i class="fas fa-dna me-1"></i> Raza</th>
                                                    <th><i class="fas fa-code-branch me-1"></i> Línea Genética</th>
                                                    <th><i class="fas fa-plus-circle me-1"></i> Fecha Introducción</th>
                                                    <th><i class="fas fa-flag me-1"></i> Estado Actual</th>
                                                    <th><i class="fas fa-exchange-alt me-1"></i> Fecha Reemplazo</th>
                                                    <th><i class="fas fa-cogs me-1"></i> Acciones</th>
                                                </tr>
                                            </thead>
                                            @php
                                                $colmenasIds = $apiario->colmenas->pluck('id');
                                                $calidadesReina = \App\Models\CalidadReina::whereIn('colmena_id', $colmenasIds)
                                                    ->orderByDesc('fecha_introduccion')
                                                    ->get()
                                                    ->groupBy('visita_id');
                                            @endphp
                                            <tbody>
                                                @forelse($calidadesReina as $grupo)
                                                    @php
                                                        $reina = $grupo->first();
                                                        $reemplazos = [];
                                                        $ultimo = null;

                                                        if ($reina) {
                                                            $raw = $reina->reemplazos_realizados;
                                                            if (is_string($raw)) {
                                                                $reemplazos = json_decode($raw, true) ?: [];
                                                            } elseif (is_array($raw)) {
                                                                $reemplazos = $raw;
                                                            }
                                                            $ultimo = $reemplazos ? end($reemplazos) : null;
                                                        }
                                                    @endphp
                                                    <tr class="table-row" data-date="{{ $reina->fecha_introduccion }}">
                                                        <td class="date-cell">
                                                            <div class="date-container">
                                                                <span
                                                                    class="date-main">{{ $reina->fecha_introduccion ? \Carbon\Carbon::parse($reina->fecha_introduccion)->format('d/m/Y') : '-' }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="motivo-cell">
                                                            <span
                                                                class="status-badge status-{{ $reina->postura_reina === 'Buena' ? 'success' : ($reina->postura_reina === 'Regular' ? 'warning' : 'danger') }}">
                                                                {{ $reina->postura_reina }}
                                                            </span>
                                                        </td>
                                                        <td class="motivo-cell">
                                                            <span
                                                                class="status-badge status-{{ $reina->estado_cria === 'Bueno' ? 'success' : ($reina->estado_cria === 'Regular' ? 'warning' : 'danger') }}">
                                                                {{ $reina->estado_cria }}
                                                            </span>
                                                        </td>
                                                        <td class="motivo-cell">
                                                            <span
                                                                class="status-badge status-{{ $reina->postura_zanganos === 'Presente' ? 'warning' : 'success' }}">
                                                                {{ $reina->postura_zanganos }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $reina->origen_reina }}</td>
                                                        <td>{{ $reina->raza }}</td>
                                                        <td>{{ $reina->linea_genetica }}</td>
                                                        <td class="date-cell">
                                                            {{ $reina->fecha_introduccion ? \Carbon\Carbon::parse($reina->fecha_introduccion)->format('d/m/Y') : '-' }}
                                                        </td>
                                                        <td class="motivo-cell">
                                                            <span
                                                                class="status-badge status-{{ $reina->estado_actual === 'Activa' ? 'success' : 'warning' }}">
                                                                {{ $reina->estado_actual }}
                                                            </span>
                                                        </td>
                                                        <td class="date-cell">
                                                            @if($ultimo && !empty($ultimo['fecha']))
                                                                {{ \Carbon\Carbon::parse($ultimo['fecha'])->format('d/m/Y') }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="actions-cell">
                                                            <div class="btn-group" role="group">
                                                                <a href="{{ route('generate.document.reina', $apiario->id) }}"
                                                                    class="btn btn-outline-secondary btn-sm" title="Generar PDF" target="_blank">
                                                                    <i class="fas fa-file-pdf"></i>
                                                                </a>

                                                                @if(isset($visita) && isset($visita->id))
                                                                    <a href="{{ route('visitas.reina.edit', ['apiario' => $apiario->id, 'visita' => $visita->id]) }}"
                                                                        class="btn btn-outline-primary btn-sm" title="Editar">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                @endif
                                                            </div>

                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="11" class="text-center text-muted">No hay datos de reina
                                                            registrados.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Action Section -->
                <div class="action-section mt-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <a href="{{ route('visitas') }}" class="custom-btn-back">
                            <i class="fas fa-arrow-left me-2"></i>
                            Volver
                        </a>
                        @if(!$apiario->visitas->isEmpty())
                            <div class="summary-info">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Total de registros: {{ $apiario->visitas->count() }} visitas desde
                                    {{ $apiario->visitas->min('fecha_visita') ? \Carbon\Carbon::parse($apiario->visitas->min('fecha_visita'))->format('d/m/Y') : 'N/A' }}
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var apiarioId = "{{ $apiario->id }}";
            var storageKey = "lastVisitTab_" + apiarioId;
            var triggerTabList = [].slice.call(document.querySelectorAll('#visitTabs button'));
            triggerTabList.forEach(function (triggerEl) {
                var tabTrigger = new bootstrap.Tab(triggerEl);

                triggerEl.addEventListener('click', function (event) {
                    event.preventDefault();
                    tabTrigger.show();
                });
            });

            // Guardar la pestaña activa en localStorage al cambiar
            document.querySelectorAll('#visitTabs button').forEach(function (button) {
                button.addEventListener('shown.bs.tab', function (event) {
                    var tabId = event.target.getAttribute('id');
                    localStorage.setItem(storageKey, tabId);
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

            // Al cargar la página, restaurar la última pestaña activa para este apiario
            var lastTabId = localStorage.getItem(storageKey);
            if (lastTabId) {
                var lastTab = document.getElementById(lastTabId);
                if (lastTab) {
                    var tab = new bootstrap.Tab(lastTab);
                    tab.show();
                }
            } else {
                // Si no hay nada guardado, mostrar la primera por defecto
                var firstTab = document.querySelector('#general-tab');
                if (firstTab) {
                    var tab = new bootstrap.Tab(firstTab);
                    tab.show();
                }
            }

            // Función de búsqueda mejorada
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const searchTerm = this.value.toLowerCase();
                    const tables = document.querySelectorAll('.custom-table tbody');

                    tables.forEach(table => {
                        const rows = table.querySelectorAll('tr:not(.empty-row)');
                        let visibleRows = 0;

                        rows.forEach(row => {
                            const text = row.textContent.toLowerCase();
                            if (text.includes(searchTerm)) {
                                row.style.display = '';
                                visibleRows++;
                            } else {
                                row.style.display = 'none';
                            }
                        });

                        // Mostrar mensaje si no hay resultados
                        let emptyRow = table.querySelector('.empty-row');
                        if (visibleRows === 0 && searchTerm !== '') {
                            if (!emptyRow) {
                                emptyRow = document.createElement('tr');
                                emptyRow.className = 'empty-row';
                                table.appendChild(emptyRow);
                            }
                            emptyRow.innerHTML = `<td colspan="100%" class="text-center text-muted py-3">
                    <i class="fas fa-search me-2"></i>No se encontraron resultados para "${searchTerm}"
                </td>`;
                            emptyRow.style.display = '';
                        } else if (emptyRow) {
                            emptyRow.style.display = 'none';
                        }
                    });
                });
            }

            // Filtro por fecha mejorado
            const dateFilter = document.getElementById('dateFilter');
            if (dateFilter) {
                dateFilter.addEventListener('change', function () {
                    const filterValue = this.value;
                    const rows = document.querySelectorAll('.table-row');
                    const now = new Date();

                    rows.forEach(row => {
                        const dateStr = row.getAttribute('data-date');
                        if (!dateStr) return;

                        const rowDate = new Date(dateStr);
                        let show = true;

                        switch (filterValue) {
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

            // Funciones de exportación
            window.exportToPDF = function () {
                const activeTab = document.querySelector('.tab-pane.active');
                const tabName = activeTab ? activeTab.id : 'general';
                window.open(`/apiarios/{{ $apiario->id }}/export/pdf?tab=${tabName}`, '_blank');
            };

            window.exportToExcel = function () {
                const activeTab = document.querySelector('.tab-pane.active');
                const tabName = activeTab ? activeTab.id : 'general';
                window.location.href = `/apiarios/{{ $apiario->id }}/export/excel?tab=${tabName}`;
            };

            // Tooltip para botones
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Indicador de scroll para tablas
            const tableContainers = document.querySelectorAll('.table-responsive');
            tableContainers.forEach(container => {
                const table = container.querySelector('table');
                if (table && table.scrollWidth > container.clientWidth) {
                    container.classList.add('has-scroll');
                }
            });
        });
    </script>
@endpush