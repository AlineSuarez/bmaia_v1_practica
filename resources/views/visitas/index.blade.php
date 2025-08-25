@extends('layouts.app')

@section('title', 'B-MaiA - Cuaderno de Campo')

@push('styles')
    <link href="{{ asset('css/components/home-user/cuaderno-de-campo.css') }}" rel="stylesheet">
@endpush

@section('content')
        <div class="apiary-dashboard">
            <!-- Encabezado con efecto parallax sutil -->
            <header class="dashboard-header">
                <div class="header-content">
                    <div class="header-icon-wrapper">
                        <div class="header-icon">
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                    <div class="header-text">
                        <h1>Cuaderno de Campo</h1>
                        <p>Gestiona y documenta el manejo y observaciones de tus apiarios</p>
                    </div>
                </div>
            </header>

            <!-- Panel de control con búsqueda y filtros -->
            <div class="control-panel">
                <div class="search-container">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="apiarySearch" placeholder="Buscar apiario por nombre..."
                            onkeyup="filterApiaries()">
                        <button class="clear-search" onclick="clearSearch()" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="search-results" id="searchResults">
                        <span id="resultsCount"></span>
                    </div>
                </div>

                <div class="view-controls">
                    <button class="view-btn active" data-view="card" title="Vista de tarjetas">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button class="view-btn" data-view="list" title="Vista de lista">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <!-- Sistema de pestañas -->
            <div class="tabs-container">
                <div class="tabs-nav">
                    @if(isset($apiariosBase) && $apiariosBase->count())
                        <button class="tab-btn" data-tab="base">
                            <i class="fas fa-warehouse"></i>
                            <span>Apiarios Base</span>
                            <span class="tab-count">{{ $apiariosBase->count() }}</span>
                        </button>
                    @endif

                    @if(isset($apiariosTemporales) && $apiariosTemporales->count())
                        <button class="tab-btn" data-tab="temporales">
                            <i class="fas fa-truck"></i>
                            <span>Temporales</span>
                            <span class="tab-count">{{ $apiariosTemporales->count() }}</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Contenedor principal de apiarios -->
            <div class="apiary-section">
                {{-- Sección Apiarios Base --}}
                @if(isset($apiariosBase) && $apiariosBase->count())
                    <div class="tab-content" id="tab-base">
                        <div class="apiaries-container card-view" id="apiariesBaseContainer">
                            @foreach($apiariosBase as $apiario)
                                <div class="apiary-card" data-name="{{ strtolower($apiario->nombre) }}">
                                    <div class="card-header">
                                        <div class="card-icon">
                                            <i class="fas fa-archive"></i>
                                        </div>
                                        <div class="card-title">
                                            <h2>{{ $apiario->nombre }}</h2>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="card-stats">
                                            <div class="stat">
                                                <div class="stat-icon">
                                                    <i class="fas fa-calendar-check"></i>
                                                </div>
                                                <div class="stat-content">
                                                    <span class="stat-label">Última visita</span>
                                                    <span class="stat-value fecha-horizontal">
                                                        @if($apiario->ultimaVisita)
                                                            {{ \Carbon\Carbon::parse($apiario->ultimaVisita->created_at)->format('d/m/Y') }}
                                                        @else
                                                            Sin registros
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="stat">
                                                <div class="stat-icon">
                                                    <i class="fas fa-clipboard-list"></i>
                                                </div>
                                                <div class="stat-content">
                                                    <span class="stat-label">Registros</span>
                                                    <span class="stat-value registros-horizontal" >{{ $apiario->visitas->count() }}</span>
                                                </div>
                                            </div>
                                            <a href="{{ route('visitas.historial', $apiario->id) }}" class="stat stat-link">
                                                <div class="stat-icon">
                                                    <i class="fas fa-history"></i>
                                                </div>
                                                <div class="stat-content">
                                                    <span class="stat-label">Ver historial</span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="card-actions">
                                            <button class="action-btn primary" data-bs-toggle="modal"
                                                data-bs-target="#modalRegistro{{ $apiario->id }}">
                                                <i class="fas fa-plus"></i>
                                                <span>Nuevo registro</span>
                                            </button>

                                            <div class="action-dropdown">
                                                <button type="button" class="action-btn secondary dropdown-toggle"
                                                    id="downloadBtn{{ $apiario->id }}">
                                                    <i class="fas fa-download"></i>
                                                    <span>Descargar</span>
                                                </button>
                                                <div class="dropdown-panel" id="downloadPanel{{ $apiario->id }}">
                                                    <div class="dropdown-header">
                                                        <i class="fas fa-file-export"></i>
                                                        <span>Exportar documentos</span>
                                                    </div>
                                                    <div class="dropdown-options">
                                                        <a href="{{ route('generate.document.visitas', $apiario->id) }}"
                                                            class="dropdown-option" data-download="visitas">
                                                            <div class="option-icon"><i class="fas fa-clipboard-list"></i></div>
                                                            <div class="option-content">
                                                                <span class="option-title">Registro de Visitas</span>
                                                                <span class="option-desc">Historial completo de visitas</span>
                                                            </div>
                                                        </a>
                                                        <a href="{{ route('generate.document.inspeccion', $apiario->id) }}"
                                                            class="dropdown-option">
                                                            <div class="option-icon"><i class="fas fa-search"></i></div>
                                                            <div class="option-content">
                                                                <span class="option-title">Registro de Inspección</span>
                                                                <span class="option-desc">Detalles de inspecciones</span>
                                                            </div>
                                                        </a>
                                                        <a href="{{ route('generate.document.medicamentos', $apiario->id) }}"
                                                            class="dropdown-option">
                                                            <div class="option-icon"><i class="fas fa-syringe"></i></div>
                                                            <div class="option-content">
                                                                <span class="option-title">Registro de Medicamentos</span>
                                                                <span class="option-desc">Tratamientos aplicados</span>
                                                            </div>
                                                        </a>
                                                        <a href="{{ route('generate.document.alimentacion', $apiario->id) }}"
                                                            class="dropdown-option">
                                                            <div class="option-icon"><i class="fas fa-leaf"></i></div>
                                                            <div class="option-content">
                                                                <span class="option-title">Registro de Alimentos</span>
                                                                <span class="option-desc">Alimentos, metodos e insumos a utilizar</span>
                                                            </div>
                                                        </a>
                                                        <a href="{{ route('generate.document.reina', $apiario->id) }}"
                                                            class="dropdown-option">
                                                            <div class="option-icon"><i class="fas fa-crown"></i></div>
                                                            <div class="option-content">
                                                                <span class="option-title">Registro de Reina</span>
                                                                <span class="option-desc">Calidad y reemplazos de reina</span>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal para registro -->
                                <div class="modal fade custom-modal" id="modalRegistro{{ $apiario->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <div class="modal-header-content">
                                                    <div class="modal-icon">
                                                        <i class="fas fa-clipboard-list"></i>
                                                    </div>
                                                    <div class="modal-title-container">
                                                        <h5 class="modal-title">Nuevo registro</h5>
                                                        <p class="modal-subtitle">{{ $apiario->nombre }}</p>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Cerrar"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="modal-text">Selecciona el tipo de registro que deseas realizar:</p>

                                                <div class="registration-options">
                                                    <a href="{{ url('visitas/create1/' . $apiario->id) }}" class="registration-option">
                                                        <div class="option-icon"><i class="fas fa-clipboard"></i></div>
                                                        <div class="option-content">
                                                            <span class="option-title">Registro de Visitas</span>
                                                            <p class="option-description">Documentación general de visitas al apiario
                                                            </p>
                                                        </div>
                                                        <div class="option-indicator"><i class="fas fa-chevron-right"></i></div>
                                                    </a>

                                                    <a href="{{ url('visitas/create/' . $apiario->id) }}"
                                                        class="registration-option featured">
                                                        <div class="option-icon"><i class="fas fa-search"></i></div>
                                                        <div class="option-content">
                                                            <span class="option-title">Inspección de Apiario</span>
                                                            <p class="option-description">Evaluación detallada de colmenas y marcos</p>
                                                        </div>
                                                        <div class="option-indicator"><i class="fas fa-chevron-right"></i></div>
                                                    </a>

                                                    <a href="{{ url('visitas/create2/' . $apiario->id) }}" class="registration-option">
                                                        <div class="option-icon"><i class="fas fa-syringe"></i></div>
                                                        <div class="option-content">
                                                            <span class="option-title">Uso de Tratamientos</span>
                                                            <p class="option-description">Control de tratamientos y medicamentos
                                                                aplicados
                                                            </p>
                                                        </div>
                                                        <div class="option-indicator"><i class="fas fa-chevron-right"></i></div>
                                                    </a>
                                                    <a href="{{ route('visitas.create3', $apiario) }}" class="registration-option">
                                                        <div class="option-icon"><i class="fas fa-leaf"></i></div>
                                                        <div class="option-content">
                                                            <span class="option-title">Alimentacion</span>
                                                            <p class="option-description">Estado nutricional e insumos utilizados
                                                            </p>
                                                        </div>
                                                        <div class="option-indicator"><i class="fas fa-chevron-right"></i></div>
                                                    </a>
                                                    <a href="{{ route('visitas.create4', $apiario) }}" class="registration-option">
                                                        <div class="option-icon"><i class="fas fa-crown"></i></div>
                                                        <div class="option-content">
                                                            <span class="option-title">Reina</span>
                                                            <p class="option-description">Calidad y reemplazos de reina</p>
                                                        </div>
                                                        <div class="option-indicator"><i class="fas fa-chevron-right"></i></div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="pagination-controls" id="paginationBase"></div>
                    </div>
                @endif

                {{-- Sección Apiarios Temporales --}}
                @if(isset($apiariosTemporales) && $apiariosTemporales->count())
                    <div class="tab-content" id="tab-temporales">
                        <div class="apiaries-container card-view" id="apiariesTemporalesContainer">
                            @foreach($apiariosTemporales as $apiario)
                                <div class="apiary-card" data-name="{{ strtolower($apiario->nombre) }}">
                                    <div class="card-header">
                                        <div class="card-icon">
                                            <i class="fas fa-archive"></i>
                                        </div>
                                        <div class="card-title">
                                            <h2>{{ $apiario->nombre }}</h2>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="card-stats">
                                            <div class="stat">
                                                <div class="stat-icon">
                                                    <i class="fas fa-calendar-check"></i>
                                                </div>
                                                <div class="stat-content">
                                                    <span class="stat-label">Última visita</span>
                                                    <span class="stat-value fecha-horizontal">@if($apiario->ultimaVisita)
                                                            {{ \Carbon\Carbon::parse($apiario->ultimaVisita->created_at)->format('d/m/Y') }}
                                                        @else
                                                            Sin registros
                                                        @endif</span>
                                                </div>
                                            </div>
                                            <div class="stat">
                                                <div class="stat-icon">
                                                    <i class="fas fa-clipboard-list"></i>
                                                </div>
                                                <div class="stat-content">
                                                    <span class="stat-label">Registros</span>
                                                    <span class="stat-value registros-horizontal">{{ $apiario->visitas->count() }}</span>
                                                </div>
                                            </div>
                                            <a href="{{ route('visitas.historial', $apiario->id) }}" class="stat stat-link">
                                                <div class="stat-icon">
                                                    <i class="fas fa-history"></i>
                                                </div>
                                                <div class="stat-content">
                                                    <span class="stat-label">Ver historial</span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="card-actions">
                                            <button class="action-btn primary" data-bs-toggle="modal"
                                                data-bs-target="#modalRegistro{{ $apiario->id }}">
                                                <i class="fas fa-plus"></i>
                                                <span>Nuevo registro</span>
                                            </button>

                                            <div class="action-dropdown">
                                                <button type="button" class="action-btn secondary dropdown-toggle"
                                                    id="downloadBtn{{ $apiario->id }}">
                                                    <i class="fas fa-download"></i>
                                                    <span>Descargar</span>
                                                </button>
                                                <div class="dropdown-panel" id="downloadPanel{{ $apiario->id }}">
                                                    <div class="dropdown-header">
                                                        <i class="fas fa-file-export"></i>
                                                        <span>Exportar documentos</span>
                                                    </div>
                                                    <div class="dropdown-options">
                                                        <a href="{{ route('generate.document.visitas', $apiario->id) }}"
                                                            class="dropdown-option" data-download="visitas">
                                                            <div class="option-icon"><i class="fas fa-clipboard-list"></i></div>
                                                            <div class="option-content">
                                                                <span class="option-title">Registro de Visitas</span>
                                                                <span class="option-desc">Historial completo de visitas</span>
                                                            </div>
                                                        </a>
                                                        <a href="{{ route('generate.document.inspeccion', $apiario->id) }}"
                                                            class="dropdown-option">
                                                            <div class="option-icon"><i class="fas fa-search"></i></div>
                                                            <div class="option-content">
                                                                <span class="option-title">Registro de Inspección</span>
                                                                <span class="option-desc">Detalles de inspecciones</span>
                                                            </div>
                                                        </a>
                                                        <a href="{{ route('generate.document.medicamentos', $apiario->id) }}"
                                                            class="dropdown-option">
                                                            <div class="option-icon"><i class="fas fa-syringe"></i></div>
                                                            <div class="option-content">
                                                                <span class="option-title">Registro de Medicamentos</span>
                                                                <span class="option-desc">Tratamientos aplicados</span>
                                                            </div>
                                                        </a>
                                                        <a href="{{ route('generate.document.alimentacion', $apiario->id) }}"
                                                            class="dropdown-option">
                                                            <div class="option-icon"><i class="fas fa-leaf"></i></div>
                                                            <div class="option-content">
                                                                <span class="option-title">Registro de Alimentos</span>
                                                                <span class="option-desc">Alimentos, metodos e insumos a utilizar</span>
                                                            </div>
                                                        </a>
                                                        <a href="{{ route('generate.document.reina', $apiario->id) }}"
                                                            class="dropdown-option">
                                                            <div class="option-icon"><i class="fas fa-crown"></i></div>
                                                            <div class="option-content">
                                                                <span class="option-title">Registro de Reina</span>
                                                                <span class="option-desc">Calidad y reemplazos de reina</span>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal para registro -->
                                <div class="modal fade custom-modal" id="modalRegistro{{ $apiario->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <div class="modal-header-content">
                                                    <div class="modal-icon">
                                                        <i class="fas fa-clipboard-list"></i>
                                                    </div>
                                                    <div class="modal-title-container">
                                                        <h5 class="modal-title">Nuevo registro</h5>
                                                        <p class="modal-subtitle">{{ $apiario->nombre }}</p>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Cerrar"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="modal-text">Selecciona el tipo de registro que deseas realizar:</p>

                                                <div class="registration-options">
                                                    <a href="{{ url('visitas/create1/' . $apiario->id) }}" class="registration-option">
                                                        <div class="option-icon"><i class="fas fa-clipboard"></i></div>
                                                        <div class="option-content">
                                                            <span class="option-title">Registro de Visitas</span>
                                                            <p class="option-description">Documentación general de visitas al apiario
                                                            </p>
                                                        </div>
                                                        <div class="option-indicator"><i class="fas fa-chevron-right"></i></div>
                                                    </a>

                                                    <a href="{{ url('visitas/create/' . $apiario->id) }}"
                                                        class="registration-option featured">
                                                        <div class="option-icon"><i class="fas fa-search"></i></div>
                                                        <div class="option-content">
                                                            <span class="option-title">Inspección de Apiario</span>
                                                            <p class="option-description">Evaluación detallada de colmenas y marcos</p>
                                                        </div>
                                                        <div class="option-indicator"><i class="fas fa-chevron-right"></i></div>
                                                    </a>

                                                    <a href="{{ url('visitas/create2/' . $apiario->id) }}" class="registration-option">
                                                        <div class="option-icon"><i class="fas fa-syringe"></i></div>
                                                        <div class="option-content">
                                                            <span class="option-title">Uso de Tratamientos</span>
                                                            <p class="option-description">Control de tratamientos y medicamentos
                                                                aplicados
                                                            </p>
                                                        </div>
                                                        <div class="option-indicator"><i class="fas fa-chevron-right"></i></div>
                                                    </a>
                                                    <a href="{{ route('visitas.create3', $apiario) }}" class="registration-option">
                                                        <div class="option-icon"><i class="fas fa-leaf"></i></div>
                                                        <div class="option-content">
                                                            <span class="option-title">Alimentacion</span>
                                                            <p class="option-description">Estado nutricional e insumos utilizados
                                                            </p>
                                                        </div>
                                                        <div class="option-indicator"><i class="fas fa-chevron-right"></i></div>
                                                    </a>
                                                    <a href="{{ route('visitas.create4', $apiario) }}" class="registration-option">
                                                        <div class="option-icon"><i class="fas fa-crown"></i></div>
                                                        <div class="option-content">
                                                            <span class="option-title">Reina</span>
                                                            <p class="option-description">Calidad y reemplazos de reina</p>
                                                        </div>
                                                        <div class="option-indicator"><i class="fas fa-chevron-right"></i></div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="pagination-controls" id="paginationTemporales"></div>
                    </div>
                @endif

                {{-- Si no hay ningún apiario --}}
                @if(
        (!isset($apiariosBase) || !$apiariosBase->count()) &&
        (!isset($apiariosTemporales) || !$apiariosTemporales->count())
    )
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-archive"></i>
                        </div>
                        <h3 class="empty-state-title">No hay apiarios registrados</h3>
                        <p class="empty-state-text">Aún no has registrado ningún apiario en el sistema. Crea uno nuevo para
                            comenzar.</p>
                        <a href="{{ route('apiarios.create') }}" class="btn-primary">
                            <i class="fas fa-plus"></i> Crear nuevo apiario
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <script src="{{ asset('js/components/home-user/cuaderno-de-campo.js') }}"></script>
@endsection