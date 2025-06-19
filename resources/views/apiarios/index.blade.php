@extends('layouts.app')

@section('content')

    <head>
        <link href="{{ asset('./css/components/home-user/apiarios.css') }}" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <!-- ============================================================
                 DISEÑO MINIMALISTA FULL-WIDTH CON SISTEMA DE TARJETAS
                 ============================================================ -->
    <div class="apiarios-container">

        <!-- Header Principal Minimalista -->
        <header class="page-header">
            <div class="header-main">
                <h1 class="page-title">Gestión de Apiarios</h1>
                <div class="header-stats">
                    <div class="stat-item">
                        <span class="stat-number">{{ count($apiariosFijos) }}</span>
                        <span class="stat-label">Fijos</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ count($apiariosBase) }}</span>
                        <span class="stat-label">Base</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ count($apiariosTemporales) }}</span>
                        <span class="stat-label">Temporales</span>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <button id="showArchivedButton" class="action-btn secondary" data-bs-toggle="modal"
                    data-bs-target="#archivedModal">
                    <i class="fas fa-archive"></i>
                    <span>Archivados</span>
                </button>
            </div>
        </header>

        <!-- Navegación de Pestañas Rediseñada -->
        <nav class="tabs-nav" id="apiariosTab" role="tablist">
            <button class="tab-item active" id="fijos-tab" data-bs-toggle="tab" data-bs-target="#fijos" type="button"
                role="tab" aria-controls="fijos" aria-selected="true">
                <div class="tab-icon">
                    <i class="fas fa-warehouse"></i>
                </div>
                <div class="tab-content">
                    <span class="tab-title">Apiarios Fijos/Permanentes</span>
                </div>
                <div class="tab-badge">{{ count($apiariosFijos) }}</div>
            </button>

            <button class="tab-item" id="trashumantes-tab" data-bs-toggle="tab" data-bs-target="#trashumantes" type="button"
                role="tab" aria-controls="trashumantes" aria-selected="false">
                <div class="tab-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="tab-content">
                    <span class="tab-title">Apiarios Temporales</span>
                </div>
                <div class="tab-badge">{{ count($apiariosBase) + count($apiariosTemporales) }}</div>
            </button>
        </nav>

        <!-- Contenido Principal -->
        <main class="content-area" id="apiariosTabContent">

            <!-- ============================================================
                         PESTAÑA APIARIOS FIJOS - CON SISTEMA DE TARJETAS
                         ============================================================ -->
            <section class="tab-pane active" id="fijos" role="tabpanel" aria-labelledby="fijos-tab">

                <div class="section-toolbar">
                    <div class="toolbar-left">
                        <h2 class="section-title">
                            <i class="fas fa-warehouse"></i>
                            Apiarios Fijos
                        </h2>
                    </div>

                    <div class="toolbar-right">
                        <!-- EL SWITCH SE AÑADE AUTOMÁTICAMENTE VÍA JAVASCRIPT -->
                        <a href="{{ route('apiarios.create') }}" class="action-btn primary">
                            <i class="fas fa-plus"></i>
                            <span>Nuevo Apiario</span>
                        </a>
                    </div>
                </div>

                @if(count($apiariosFijos) > 0)
                    <!-- TABLA EXISTENTE (SE MANTIENE INTACTA) -->
                    <div class="data-container">
                        <div class="table-container">
                            <table id="apiariosTable" class="data-table">
                                <thead>
                                    <tr>
                                        <th>Apiario</th>
                                        <th>Temporada</th>
                                        <th>Registro SAG</th>
                                        <th>Colmenas</th>
                                        <th>Tipo</th>
                                        <th>Manejo</th>
                                        <th>Objetivo</th>
                                        <th>Ubicación</th>
                                        <th>Coordenadas</th>
                                        <th>Imagen</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($apiariosFijos as $apiario)
                                        <tr>
                                            <td>
                                                <div class="cell-content">
                                                    <span class="item-id">{{ $apiario->nombre }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge primary">{{ $apiario->temporada_produccion }}</span>
                                            </td>
                                            <td>
                                                <span class="text-mono">{{ $apiario->registro_sag }}</span>
                                            </td>
                                            <td>
                                                <div class="numeric-cell">{{ $apiario->num_colmenas }}</div>
                                            </td>
                                            <td>
                                                <span class="badge secondary">{{ $apiario->tipo_apiario }}</span>
                                            </td>
                                            <td>
                                                <span class="badge info">{{ $apiario->tipo_manejo }}</span>
                                            </td>
                                            <td>
                                                <span class="badge warning">{{ $apiario->objetivo_produccion }}</span>
                                            </td>
                                            <td>
                                                <div class="location-cell">
                                                    {{ $apiario->comuna && $apiario->comuna->nombre ? $apiario->comuna->nombre : 'N/A' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="coords-cell">
                                                    <span class="coords">{{ $apiario->latitud }}, {{ $apiario->longitud }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                @php $fotoPath = public_path('storage/' . $apiario->foto); @endphp
                                                @if($apiario->foto && file_exists($fotoPath))
                                                    <div class="image-cell">
                                                        <img src="{{ asset('storage/' . $apiario->foto) }}"
                                                            alt="Apiario {{ $apiario->nombre }}" class="preview-image"
                                                            data-bs-toggle="modal" data-bs-target="#imageModal{{ $apiario->id }}">
                                                    </div>
                                                @else
                                                    <span class="no-image">Sin imagen</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="action-group">
                                                    <a href="{{ route('apiarios.editar', $apiario->id) }}" class="action-icon edit"
                                                        title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('colmenas.index', $apiario->id) }}" class="action-icon view"
                                                        title="Ver Colmenas">
                                                        <i class="fas fa-cubes"></i>
                                                    </a>
                                                    <a href="{{ route('generate.document', $apiario->id) }}"
                                                        class="action-icon download" title="Descargar PDF">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button class="action-icon delete" data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $apiario->id }}" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación Mejorada -->
                        <div class="pagination-container">
                            <div class="pagination-info">
                                <div class="pagination-info-main">
                                    <span class="pagination-summary">
                                        <span id="fijos-pagination-info">1-{{ count($apiariosFijos) }} de
                                            {{ count($apiariosFijos) }}</span>
                                    </span>
                                </div>
                            </div>
                            <div class="pagination-controls">
                                <div class="pagination-nav" id="fijos-pagination">
                                    <!-- Generado por JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CONTENEDOR DE TARJETAS (SE CREA AUTOMÁTICAMENTE VÍA JAVASCRIPT) -->
                    <!-- Las tarjetas se generan dinámicamente desde los datos de la tabla -->

                @else
                    <div class="empty-section">
                        <div class="empty-icon">
                            <i class="fas fa-warehouse"></i>
                        </div>
                        <h3 class="empty-title">No hay apiarios fijos</h3>
                        <p class="empty-description">Comienza creando tu primer apiario fijo</p>
                        <a href="{{ route('apiarios.create') }}" class="action-btn primary large">
                            <i class="fas fa-plus"></i>
                            <span>Crear Primer Apiario</span>
                        </a>
                    </div>
                @endif
            </section>

            <!-- ============================================================
                         PESTAÑA TRASHUMANCIA - CON SISTEMA DE TARJETAS
                         ============================================================ -->
            <section class="tab-pane" id="trashumantes" role="tabpanel" aria-labelledby="trashumantes-tab">

                <!-- Sección Apiarios Base -->
                <div class="subsection">
                    <div class="section-toolbar">
                        <div class="toolbar-left">
                            <h2 class="section-title">
                                <i class="fas fa-home"></i>
                                Apiarios Base
                            </h2>
                        </div>

                        <div class="toolbar-right">
                            <!-- EL SWITCH SE AÑADE AUTOMÁTICAMENTE VÍA JAVASCRIPT -->
                            <a href="{{ route('apiarios.create') }}?tipo=base" class="action-btn primary">
                                <i class="fas fa-plus"></i>
                                <span>Nuevo Base</span>
                            </a>
                            <button id="trasladarColmenasButton" class="action-btn warning" disabled>
                                <i class="fas fa-arrow-right"></i>
                                <span>Trasladar</span>
                            </button>
                        </div>
                    </div>

                    @if(count($apiariosBase) > 0)
                        <!-- TABLA EXISTENTE (SE MANTIENE INTACTA) -->
                        <div class="data-container">
                            <div class="table-container">
                                <table id="apiariosTableTrashumante" class="data-table">
                                    <thead>
                                        <tr>
                                            <th class="select-col">
                                                <label class="checkbox-wrapper">
                                                    <input type="checkbox" id="selectAllTrashumante">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </th>
                                            <th>Apiario</th>
                                            <th>Temporada</th>
                                            <th>Registro SAG</th>
                                            <th>Colmenas</th>
                                            <th>Tipo</th>
                                            <th>Manejo</th>
                                            <th>Objetivo</th>
                                            <th>Ubicación</th>
                                            <th>Coordenadas</th>
                                            <th>Imagen</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($apiariosBase as $apiario)
                                            <tr>
                                                <td>
                                                    <label class="checkbox-wrapper">
                                                        <input type="checkbox" class="select-checkbox-trashumante"
                                                            value="{{ $apiario->id }}">
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <div class="cell-content">
                                                        <span class="item-id">{{ $apiario->nombre }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge primary">{{ $apiario->temporada_produccion }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-mono">{{ $apiario->registro_sag }}</span>
                                                </td>
                                                <td>
                                                    <div class="numeric-cell">{{ $apiario->num_colmenas }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge secondary">{{ $apiario->tipo_apiario }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge info">{{ $apiario->tipo_manejo }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge warning">{{ $apiario->objetivo_produccion }}</span>
                                                </td>
                                                <td>
                                                    <div class="location-cell">
                                                        {{ $apiario->comuna && $apiario->comuna->nombre ? $apiario->comuna->nombre : 'N/A' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="coords-cell">
                                                        <span class="coords">{{ $apiario->latitud }},
                                                            {{ $apiario->longitud }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php $fotoPath = public_path('storage/' . $apiario->foto); @endphp
                                                    @if($apiario->foto && file_exists($fotoPath))
                                                        <div class="image-cell">
                                                            <img src="{{ asset('storage/' . $apiario->foto) }}"
                                                                alt="Apiario {{ $apiario->nombre }}" class="preview-image"
                                                                data-bs-toggle="modal" data-bs-target="#imageModal{{ $apiario->id }}">
                                                        </div>
                                                    @else
                                                        <span class="no-image">Sin imagen</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="action-group">
                                                        <a href="{{ route('apiarios.editar', $apiario->id) }}"
                                                            class="action-icon edit" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="{{ route('colmenas.index', $apiario->id) }}"
                                                            class="action-icon view" title="Ver Colmenas">
                                                            <i class="fas fa-cubes"></i>
                                                        </a>
                                                        <a href="{{ route('generate.document', $apiario->id) }}"
                                                            class="action-icon download" title="Descargar PDF">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginación Mejorada -->
                            <div class="pagination-container">
                                <div class="pagination-info">
                                    <div class="pagination-info-main">
                                        <span class="pagination-summary">
                                            <span id="base-pagination-info">1-{{ count($apiariosBase) }} de
                                                {{ count($apiariosBase) }}</span>
                                        </span>
                                    </div>
                                </div>
                                <div class="pagination-controls">
                                    <div class="pagination-nav" id="base-pagination">
                                        <!-- Generado por JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CONTENEDOR DE TARJETAS (SE CREA AUTOMÁTICAMENTE VÍA JAVASCRIPT) -->
                        <!-- Las tarjetas se generan dinámicamente desde los datos de la tabla -->

                    @else
                        <div class="empty-section">
                            <div class="empty-icon">
                                <i class="fas fa-home"></i>
                            </div>
                            <h3 class="empty-title">No hay apiarios base</h3>
                            <p class="empty-description">Crea un apiario base para comenzar la trashumancia</p>
                            <a href="{{ route('apiarios.create') }}?tipo=base" class="action-btn primary large">
                                <i class="fas fa-plus"></i>
                                <span>Crear Apiario Base</span>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Sección Apiarios Temporales -->
                <div class="subsection">
                    <div class="section-toolbar">
                        <div class="toolbar-left">
                            <h2 class="section-title">
                                <i class="fas fa-route"></i>
                                Apiarios Temporales
                            </h2>
                        </div>

                        <div class="toolbar-right">
                            <!-- EL SWITCH SE AÑADE AUTOMÁTICAMENTE VÍA JAVASCRIPT -->
                            <button id="retornarColmenasButton" class="action-btn success" disabled>
                                <i class="fas fa-arrow-left"></i>
                                <span>Retornar</span>
                            </button>
                        </div>
                    </div>

                    <!-- TABLA EXISTENTE (SE MANTIENE INTACTA) -->
                    <div class="data-container">
                        <div class="table-container">
                            <table id="apiariosTemporalesTable" class="data-table">
                                <thead>
                                    <tr>
                                        <th class="select-col">
                                            <label class="checkbox-wrapper">
                                                <input type="checkbox" id="selectAllTemporales">
                                                <span class="checkmark"></span>
                                            </label>
                                        </th>
                                        <th>Apicultor</th>
                                        <th>Colmenas</th>
                                        <th>Región Origen</th>
                                        <th>Comuna Origen</th>
                                        <th>Región Destino</th>
                                        <th>Comuna Destino</th>
                                        <th>Fecha Movimiento</th>
                                        <th>Motivo</th>
                                        <th>Cultivo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($apiariosTemporales as $apiario)
                                        @php
                                            $mov = $apiario->ultimoMovimientoDestino;
                                            $apiarioOrigen = $mov ? $mov->apiarioOrigen : null;
                                        @endphp
                                        <tr>
                                            <td>
                                                <label class="checkbox-wrapper">
                                                    <input type="checkbox" class="select-checkbox-temporales"
                                                        value="{{ $apiario->id }}">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <div class="cell-content">
                                                    <span class="item-id">{{ $apiario->nombre }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="numeric-cell">{{ $apiario->num_colmenas }}</div>
                                            </td>
                                            <td>
                                                <div class="location-cell">
                                                    {{ $apiarioOrigen && $apiarioOrigen->comuna && $apiarioOrigen->comuna->region ? $apiarioOrigen->comuna->region->nombre : 'N/A' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="location-cell">
                                                    {{ $apiarioOrigen && $apiarioOrigen->comuna ? $apiarioOrigen->comuna->nombre : 'N/A' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="location-cell">
                                                    {{ optional(optional($apiario->comuna)->region)->nombre ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="location-cell">
                                                    {{ optional($apiario->comuna)->nombre ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-mono">
                                                    {{ $mov ? $mov->fecha_movimiento->format('d/m/Y') : '—' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge info">
                                                    {{ $mov ? $mov->motivo_movimiento : '—' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-mono">
                                                    {{ $mov && $mov->motivo_movimiento === 'Polinización' ? ($mov->cultivo ?? '—') : '—' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-group">
                                                    <a href="{{ route('apiarios.editar', $apiario->id) }}"
                                                        class="action-icon edit" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('colmenas.index', $apiario->id) }}"
                                                        class="action-icon view" title="Ver Colmenas">
                                                        <i class="fas fa-cubes"></i>
                                                    </a>
                                                    <a href="{{ route('generate.document', $apiario->id) }}"
                                                        class="action-icon download" title="Descargar PDF">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11">
                                                <div class="empty-section small">
                                                    <div class="empty-icon">
                                                        <i class="fas fa-route"></i>
                                                    </div>
                                                    <h4 class="empty-title">No hay apiarios temporales</h4>
                                                    <p class="empty-description">Los apiarios trasladados aparecerán aquí</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación Mejorada -->
                        <div class="pagination-container">
                            <div class="pagination-info">
                                <div class="pagination-info-main">
                                    <span class="pagination-summary">
                                        <span id="temporales-pagination-info">1-{{ count($apiariosTemporales) }} de
                                            {{ count($apiariosTemporales) }}</span>
                                    </span>
                                </div>
                            </div>
                            <div class="pagination-controls">
                                <div class="pagination-nav" id="temporales-pagination">
                                    <!-- Generado por JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CONTENEDOR DE TARJETAS (SE CREA AUTOMÁTICAMENTE VÍA JAVASCRIPT) -->
                    <!-- Las tarjetas se generan dinámicamente desde los datos de la tabla -->

                </div>
            </section>
        </main>
    </div>

    <!-- ============================================================
                 MODAL ARCHIVADOS - REDISEÑADO
                 ============================================================ -->
    <div class="modal fade" id="archivedModal" tabindex="-1" aria-labelledby="archivedModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content modern-modal">
                <div class="modal-header">
                    <div class="modal-title-group">
                        <h5 class="modal-title" id="archivedModalLabel">
                            <i class="fas fa-archive"></i>
                            Apiarios Archivados
                        </h5>
                        <span class="modal-subtitle">{{ $apiariosArchivados->count() }} archivados</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($apiariosArchivados->count() > 0)
                        <div class="data-container">
                            <div class="table-container">
                                <table id="apiariosArchivadosTable" class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Apiario</th>
                                            <th>Colmenas</th>
                                            <th>Tipo</th>
                                            <th>Objetivo</th>
                                            <th>Región</th>
                                            <th>Comuna</th>
                                            <th>Fecha Archivado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($apiariosArchivados as $apiario)
                                            <tr>
                                                <td>
                                                    <div class="cell-content">
                                                        <span class="item-id">{{ $apiario->nombre }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="numeric-cell">{{ $apiario->colmenas_historicas ?? 0 }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge secondary">{{ ucfirst($apiario->tipo_apiario) }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge warning">{{ $apiario->objetivo_produccion }}</span>
                                                </td>
                                                <td>
                                                    <div class="location-cell">
                                                        {{ optional(optional($apiario->comuna)->region)->nombre ?? 'N/A' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="location-cell">
                                                        {{ optional($apiario->comuna)->nombre ?? 'N/A' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-mono">{{ $apiario->updated_at->format('d/m/Y') }}</span>
                                                </td>
                                                <td>
                                                    <div class="action-group">
                                                        <form action="{{ route('apiarios.destroy', $apiario->id) }}" method="POST"
                                                            style="display: inline-block;"
                                                            onsubmit="return confirm('¿Está seguro de eliminar permanentemente este apiario?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="action-icon delete"
                                                                title="Eliminar Permanentemente">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginación Mejorada -->
                            <div class="pagination-container">
                                <div class="pagination-info">
                                    <div class="pagination-info-main">
                                        <span class="pagination-summary">
                                            <span id="archived-pagination-info">1-{{ count($apiariosArchivados) }} de
                                                {{ count($apiariosArchivados) }}</span>
                                        </span>
                                    </div>
                                </div>
                                <div class="pagination-controls">
                                    <div class="pagination-nav" id="archived-pagination">
                                        <!-- Generado por JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="empty-section">
                            <div class="empty-icon">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <h3 class="empty-title">No hay apiarios archivados</h3>
                            <p class="empty-description">Los apiarios eliminados aparecerán aquí</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="action-btn secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        <span>Cerrar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
                 MODALES FUNCIONALES (SE MANTIENEN INTACTOS)
                 ============================================================ -->

    <!-- Modales de eliminación para apiarios fijos -->
    @foreach ($apiariosFijos as $apiario)
        <div class="modal fade" id="deleteModal{{ $apiario->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content modern-modal">
                    <div class="modal-header">
                        <div class="modal-title-group">
                            <h5 class="modal-title">
                                <i class="fas fa-exclamation-triangle"></i>
                                Confirmar Eliminación
                            </h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar el apiario <strong>{{ $apiario->nombre }}</strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="action-btn secondary" data-bs-dismiss="modal">
                            <span>Cancelar</span>
                        </button>
                        <form action="{{ route('apiarios.destroy', $apiario->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn danger">
                                <span>Eliminar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de imagen -->
        @if($apiario->foto)
            <div class="modal fade" id="imageModal{{ $apiario->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content modern-modal">
                        <div class="modal-header">
                            <div class="modal-title-group">
                                <h5 class="modal-title">Fotografía del Apiario {{ $apiario->nombre }}</h5>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="{{ asset('storage/' . $apiario->foto) }}" alt="Fotografía del Apiario" class="modal-image">
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Modales para apiarios base -->
    @foreach ($apiariosBase as $apiario)
        @if($apiario->foto)
            <div class="modal fade" id="imageModal{{ $apiario->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content modern-modal">
                        <div class="modal-header">
                            <div class="modal-title-group">
                                <h5 class="modal-title">Fotografía del Apiario {{ $apiario->nombre }}</h5>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="{{ asset('storage/' . $apiario->foto) }}" alt="Fotografía del Apiario" class="modal-image">
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Modales para apiarios temporales -->
    @foreach($apiariosTemporales as $apiario)
        <div class="modal fade" id="deleteModalTemporal{{ $apiario->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content modern-modal">
                    <div class="modal-header">
                        <div class="modal-title-group">
                            <h5 class="modal-title">
                                <i class="fas fa-archive"></i>
                                Archivar Apiario Temporal
                            </h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea archivar el apiario temporal <strong>{{ $apiario->nombre }}</strong>?</p>
                        <p class="modal-note">El apiario será movido a la sección de archivados.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="action-btn secondary" data-bs-dismiss="modal">
                            <span>Cancelar</span>
                        </button>
                        <form action="{{ route('apiarios.destroy', $apiario->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn warning">
                                <span>Archivar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal para crear apiario temporal -->
    <div class="modal fade" id="createTemporalModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content modern-modal">
                <div class="modal-header">
                    <div class="modal-title-group">
                        <h5 class="modal-title">
                            <i class="fas fa-route"></i>
                            Crear Apiario Temporal
                        </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="modal-description">¿Desea crear un apiario temporal con los apiarios seleccionados?</p>
                    <div class="selected-info">
                        <h6 class="selected-title">Apiarios seleccionados:</h6>
                        <ul id="selectedApiariosList" class="selected-list">
                            <!-- Se llenará dinámicamente con JavaScript -->
                        </ul>
                    </div>
                    <div class="info-panel">
                        <div class="info-item">
                            <i class="fas fa-arrow-right"></i>
                            <strong>Traslado:</strong> Mover apiarios a una ubicación temporal.
                        </div>
                        <div class="info-item">
                            <i class="fas fa-arrow-left"></i>
                            <strong>Retorno:</strong> Devolver apiarios a su ubicación original.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="action-btn secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        <span>Cancelar</span>
                    </button>
                    <button type="button" class="action-btn warning" id="createTrasladoButton">
                        <i class="fas fa-arrow-right"></i>
                        <span>Traslado</span>
                    </button>
                    <button type="button" class="action-btn success" id="createRetornoButton">
                        <i class="fas fa-arrow-left"></i>
                        <span>Retorno</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para retorno -->
    <div class="modal fade" id="returnConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content modern-modal">
                <div class="modal-header">
                    <div class="modal-title-group">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-circle"></i>
                            Confirmar Retorno
                        </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="modal-description">
                        Las colmenas de los apiarios temporales seleccionados serán devueltas a sus apiarios de origen
                        correspondientes, y estos apiarios temporales serán finalmente archivados.
                    </p>
                    <div class="selected-info">
                        <h6 class="selected-title">Apiarios temporales seleccionados:</h6>
                        <ul id="returnSelectedList" class="selected-list">
                            <!-- Se llenará dinámicamente con JavaScript -->
                        </ul>
                    </div>
                    <div class="warning-panel">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>¿Está seguro de que desea continuar?</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="action-btn secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        <span>Cancelar</span>
                    </button>
                    <button type="button" class="action-btn success" id="confirmReturnButton">
                        <i class="fas fa-check"></i>
                        <span>Confirmar Retorno</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('optional-scripts')
    <script>
        window.apiariosCreateTemporalUrl = "{{ route('apiarios.createTemporal') }}";
        window.apiariosArchivarMultiplesUrl = "{{ route('apiarios.archivarMultiples') }}";
        window.csrfToken = "{{ csrf_token() }}";
    </script>
    <script src="{{ asset('js/components/home-user/apiarios.js') }}"></script>
@endsection