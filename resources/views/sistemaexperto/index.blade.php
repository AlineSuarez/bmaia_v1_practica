@extends('layouts.app')

@section('title', 'B-Maia - Sistema Experto de Apiarios')

@section('content')

    <head>
        <link href="{{ asset('./css/components/home-user/sisexperto.css') }}" rel="stylesheet">
    </head>
    <!-- Pantalla de carga (overlay) -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;" aria-hidden="true">
        <div class="spinner-container">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando consejos, por favor espera...</p>
        </div>
    </div>

    <!-- Sistema de Notificaciones -->
    <div id="notificationContainer" class="notification-container"></div>

    <div class="container mt-4">
        <header class="mb-4">
            <h1 class="page-title">Sistema Experto de Apiarios</h1>
            <p class="lead">Recomendaciones personalizadas basadas en tus registros de colmenas</p>
        </header>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0 card-title-responsive">Consejos para tus Apiarios</h2>
                <button class="btn btn-primary btn-regenerar-responsive" id="regenerarConsejos"
                    aria-label="Regenerar todos los consejos">
                    <i class="fas fa-sync me-1"></i>
                    <span class="btn-text-full">Regenerar Todos</span>
                    <span class="btn-text-short d-none">Regenerar</span>
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0" id="apiariosTable">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Nombre del Apiario</th>
                                <th scope="col">N° de Colmenas</th>
                                <th scope="col" class="w-50">Consejo Personalizado</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="apiariosTableBody">
                            @forelse($apiarios as $apiario)
                                <tr data-apiario="{{ $apiario->id }}" class="apiario-row">
                                    <td>
                                        <strong>{{ $apiario->nombre }}</strong>
                                    </td>
                                    <td class="text-center">{{ $apiario->num_colmenas }}</td>
                                    <td class="consejo-td">
                                        <div class="d-flex align-items-center">
                                            <div class="spinner-border spinner-border-sm text-primary me-2 d-none"
                                                role="status"></div>
                                            <div class="consejo-text"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-outline-primary btn-sm btn-consejo"
                                                data-id="{{ $apiario->id }}" title="Regenerar Consejo"
                                                aria-label="Regenerar consejo para {{ $apiario->nombre }}">
                                                <i class="fas fa-sync"></i>
                                            </button>
<!--
<a href="{{ route('sistemaexperto.create', $apiario->id) }}"
                                                class="btn btn-success btn-sm" title="Registrar PCC"
                                                aria-label="Registrar PCC para {{ $apiario->nombre }}">
                                                <i class="fas fa-plus"></i>
                                            </a>                                            
-->
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptyRow">
                                    <td colspan="4" class="text-center py-4">
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No hay apiarios registrados actualmente.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Paginación -->
                <div class="pagination-container" id="paginationContainer" style="display: none;">
                    <div class="pagination-info">
                        <span id="paginationInfo">Mostrando 1-5 de 10 apiarios</span>
                    </div>
                    <div class="pagination-controls">
                        <nav aria-label="Navegación de páginas">
                            <ul class="pagination-nav" id="paginationNav">
                                <!-- Los botones de paginación se generarán dinámicamente -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light d-flex justify-content-end">
                <span class="text-muted small">Última actualización: <time
                        datetime="{{ date('Y-m-d') }}">{{ date('d/m/Y') }}</time></span>
            </div>
        </div>

        <section class="text-center mb-4">
            <div class="alert alert-info" role="alert">
                <p class="mb-0"><i class="fas fa-lightbulb me-2"></i> Los consejos se generan analizando los datos
                    históricos de tus colmenas</p>
            </div>
        </section>
    </div>

    <!-- Modal de Confirmación para Regenerar Consejos -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-2">
                    <h5 class="modal-title fw-semibold text-dark" id="confirmModalLabel">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Confirmar Regeneración
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body py-3">
                    <p class="text-muted mb-3" id="confirmMessage">
                        ¿Estás seguro de que deseas regenerar los consejos? Esta acción sobrescribirá los consejos actuales.
                    </p>
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        <small class="text-muted mb-0">
                            Los nuevos consejos se generarán basándose en los datos más recientes de tus colmenas.
                        </small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="confirmRegenerate">
                        <i class="fas fa-sync me-1"></i>
                        Confirmar Regeneración
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/components/home-user/sisexperto.js') }}"></script>
@endsection