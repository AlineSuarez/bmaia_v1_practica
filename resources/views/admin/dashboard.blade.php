@extends('layouts.admin')

@section('title', 'Panel de Administración - B-MaiA')
@section('page-title', 'Panel de Indicadores del Sistema')

@section('content')

    <div class="container dashboard-container">
        <!-- Header del Dashboard -->
        <div class="row dashboard-header animate">
            <div class="col-md-12 position-relative">
                <div class="text-center">
                    <h1 class="dashboard-title">Panel de Indicadores del Sistema</h1>
                    <p class="dashboard-subtitle">Visualiza y analiza el estado global de la plataforma B-MaiA. Obtén
                        información valiosa sobre productividad, apiarios y movimiento de colmenas.</p>
                </div>
                <a href="{{ route('admin.dashboard.pdf') }}" class="btn-download-pdf" target="_blank">
                    <i class="fas fa-file-pdf"></i>
                    <span>Generar Reporte</span>
                </a>
            </div>
        </div>

        <!-- SECCIÓN 1: PRODUCTIVIDAD GLOBAL -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="section-title animate">
                    <i class="fas fa-chart-line"></i> Productividad Global
                </h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 animate delay-1">
                <div class="info-card">
                    <div class="info-card-top">
                        <div class="info-card-icon">
                            <i class="fas fa-cubes"></i>
                        </div>
                        <div class="info-card-content">
                            <h3 class="info-card-title">Promedio Colmenas/Apiario</h3>
                            <div class="info-card-value">
                                {{ number_format($promedioColmenasPorApiario, 1) }}
                            </div>
                        </div>
                    </div>
                    <p class="info-card-text">
                        <i class="fas fa-info-circle"></i> Promedio de colmenas por apiario activo
                    </p>
                </div>
            </div>
            <div class="col-md-4 animate delay-2">
                <div class="info-card">
                    <div class="info-card-top">
                        <div class="info-card-icon">
                                <i class="fas fa-heartbeat"></i>
                            </div>
                        <div class="info-card-content">
                            <h3 class="info-card-title">Tasa Supervivencia</h3>
                            <div class="info-card-value">
                            {{ number_format($tasaSupervivencia, 1) }}%
                        </div>
                        </div>
                    </div>
                    <p class="info-card-text">
                        <i class="fas fa-info-circle"></i> Porcentaje de colmenas activas
                    </p>
                </div>
            </div>
            <div class="col-md-4 animate delay-3">
                <div class="info-card">
                    <div class="info-card-top">
                        <div class="info-card-icon">
                                <i class="fas fa-flask"></i>
                            </div>
                        <div class="info-card-content">
                            <h3 class="info-card-title">Promedio Tratamientos</h3>
                            <div class="info-card-value">
                            {{ number_format($promedioTratamientos, 1) }}
                        </div>
                        </div>
                    </div>
                    <p class="info-card-text">
                        <i class="fas fa-info-circle"></i> Tratamientos por colmena/año
                    </p>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 animate delay-1">
                <div class="info-card">
                    <div class="info-card-top">
                        <div class="info-card-icon">
                                <i class="fas fa-honey-pot"></i>
                            </div>
                        <div class="info-card-content">
                            <h3 class="info-card-title">Producción Anual Promedio</h3>
                            <div class="info-card-value">
                            {{ number_format($promedioProduccion, 1) }} kg
                        </div>
                        </div>
                    </div>
                    <p class="info-card-text">
                        <i class="fas fa-info-circle"></i> Kg de miel por colmena/año
                    </p>
                </div>
            </div>
            <div class="col-md-4 animate delay-2">
                <div class="info-card">
                    <div class="info-card-top">
                        <div class="info-card-icon">
                                <i class="fas fa-skull-crossbones"></i>
                            </div>
                        <div class="info-card-content">
                            <h3 class="info-card-title">Tasa Mortalidad</h3>
                            <div class="info-card-value">
                            {{ number_format($tasaMortalidad, 1) }}%
                        </div>
                        </div>
                    </div>
                    <p class="info-card-text">
                        <i class="fas fa-info-circle"></i> Porcentaje de colmenas inactivas
                    </p>
                </div>
            </div>
            <div class="col-md-4 animate delay-3">
                <div class="info-card">
                    <div class="info-card-top">
                        <div class="info-card-icon">
                                <i class="fas fa-drumstick-bite"></i>
                            </div>
                        <div class="info-card-content">
                            <h3 class="info-card-title">Alimento Suministrado</h3>
                            <div class="info-card-value">
                            {{ number_format($alimentoSuministrado, 1) }} kg
                        </div>
                        </div>
                    </div>
                    <p class="info-card-text">
                        <i class="fas fa-info-circle"></i> Kg de alimento por colmena/año
                    </p>
                </div>
            </div>
        </div>

        <!-- Gráficos de Torta -->
        <div class="row mb-4">
            <div class="col-md-6 animate delay-2">
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title-container">
                            <h5 class="chart-title"><i class="fas fa-chart-pie"></i> Tipo de Actividad de los Apiarios</h5>
                            <p class="chart-subtitle">Distribución porcentual de apiarios según su objetivo de producción.</p>
                        </div>
                        <div class="tooltip-container">
                            <div class="chart-help">?</div>
                            <div class="tooltip-content">
                                Muestra qué porcentaje de apiarios se dedica a cada tipo de actividad (producción de miel, polinización, etc.)
                            </div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="actividadesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 animate delay-2">
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title-container">
                            <h5 class="chart-title"><i class="fas fa-chart-pie"></i> Tipo de Alimento Utilizado</h5>
                            <p class="chart-subtitle">Distribución de tipos de alimento suministrado a las colmenas.</p>
                        </div>
                        <div class="tooltip-container">
                            <div class="chart-help">?</div>
                            <div class="tooltip-content">
                                Muestra qué tipos de alimento son más utilizados por los apicultores (jarabe, fondant, polen, etc.)
                            </div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="alimentosChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 2: PRODUCTIVIDAD POR APIARIO -->
        <div class="row mb-4 mt-5">
            <div class="col-12">
                <h3 class="section-title animate">
                    <i class="fas fa-map-marked-alt"></i> Productividad por Apiario
                </h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 animate delay-1">
                <div class="info-card">
                    <div class="info-card-top">
                        <div class="info-card-icon">
                                <i class="fas fa-map-pin"></i>
                            </div>
                        <div class="info-card-content">
                            <h3 class="info-card-title">Total Apiarios Activos</h3>
                            <div class="info-card-value">
                            {{ $totalApiariosActivos }}
                        </div>
                        </div>
                    </div>
                    <p class="info-card-text">
                        <i class="fas fa-info-circle"></i> Apiarios registrados y activos
                    </p>
                </div>
            </div>
            <div class="col-md-4 animate delay-2">
                <div class="info-card">
                    <div class="info-card-top">
                        <div class="info-card-icon">
                                <i class="fas fa-map"></i>
                            </div>
                        <div class="info-card-content">
                            <h3 class="info-card-title">Regiones con Apiarios</h3>
                            <div class="info-card-value">
                            {{ $regionesConApiarios }}
                        </div>
                        </div>
                    </div>
                    <p class="info-card-text">
                        <i class="fas fa-info-circle"></i> Cobertura geográfica del sistema
                    </p>
                </div>
            </div>
            <div class="col-md-4 animate delay-3">
                <div class="info-card">
                    <div class="info-card-top">
                        <div class="info-card-icon">
                                <i class="fas fa-percentage"></i>
                            </div>
                        <div class="info-card-content">
                            <h3 class="info-card-title">% Apiarios con Geo</h3>
                            <div class="info-card-value">
                            {{ number_format($porcentajeApiariosGeolocalizados, 1) }}%
                        </div>
                        </div>
                    </div>
                    <p class="info-card-text">
                        <i class="fas fa-info-circle"></i> Apiarios con coordenadas GPS
                    </p>
                </div>
            </div>
        </div>

        <!-- Gráfico de Distribución de Colmenas por Región -->
        <div class="row mb-4">
            <div class="col-md-12 animate delay-2">
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title-container">
                            <h5 class="chart-title"><i class="fas fa-chart-bar"></i> Distribución de Colmenas por Región</h5>
                            <p class="chart-subtitle">Total de colmenas registradas en cada región del país.</p>
                        </div>
                        <div class="tooltip-container">
                            <div class="chart-help">?</div>
                            <div class="tooltip-content">
                                Visualiza qué regiones concentran la mayor cantidad de colmenas en el sistema.
                            </div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="regionesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Apicultores por Región -->
        <div class="row mb-4">
            <div class="col-md-12 animate delay-2">
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title-container">
                            <h5 class="chart-title"><i class="fas fa-chart-bar"></i> Distribución de Apicultores por Región</h5>
                            <p class="chart-subtitle">Total de apicultores con apiarios activos en cada región del país.</p>
                        </div>
                        <div class="tooltip-container">
                            <div class="chart-help">?</div>
                            <div class="tooltip-content">
                                Visualiza qué regiones concentran la mayor cantidad de apicultores activos en el sistema.
                            </div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="apicultoresChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 3: MOVIMIENTO DE COLMENAS -->
        <div class="row mb-4 mt-5">
            <div class="col-12">
                <h3 class="section-title animate">
                    <i class="fas fa-truck-moving"></i> Movimiento de Colmenas
                </h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 animate delay-1">
                <div class="info-card">
                    <div class="info-card-top">
                        <div class="info-card-icon">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                        <div class="info-card-content">
                            <h3 class="info-card-title">Movimiento Total de Colmenas</h3>
                            <div class="info-card-value">
                            {{ $totalMovimientos }}
                        </div>
                        </div>
                    </div>
                    <p class="info-card-text">
                        <i class="fas fa-info-circle"></i> Traslados registrados en el sistema
                    </p>
                </div>
            </div>
            <div class="col-md-4 animate delay-2">
                <div class="info-card">
                    <div class="info-card-top">
                        <div class="info-card-icon">
                                <i class="fas fa-undo"></i>
                            </div>
                        <div class="info-card-content">
                            <h3 class="info-card-title">Retornos</h3>
                            <div class="info-card-value">
                            {{ $totalRetornos }}
                        </div>
                        </div>
                    </div>
                    <p class="info-card-text">
                        <i class="fas fa-info-circle"></i> Retornos a apiarios base
                    </p>
                </div>
            </div>
            <div class="col-md-4 animate delay-3">
                <div class="info-card">
                    <div class="info-card-top">
                        <div class="info-card-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        <div class="info-card-content">
                            <h3 class="info-card-title">Duración Total Polinización</h3>
                            <div class="info-card-value">
                            {{ number_format($duracionPromedioPolinizacion, 0) }} días
                        </div>
                        </div>
                    </div>
                    <p class="info-card-text">
                        <i class="fas fa-info-circle"></i> Suma total de días en servicios de polinización
                    </p>
                </div>
            </div>
        </div>

        <!-- Gráfico de Movimientos -->
        <div class="row mb-4">
            <div class="col-md-6 animate delay-2">
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title-container">
                            <h5 class="chart-title"><i class="fas fa-chart-pie"></i> Movimientos de Colmenas por Motivo</h5>
                            <p class="chart-subtitle">Distribución de traslados según el motivo de movimiento.</p>
                        </div>
                        <div class="tooltip-container">
                            <div class="chart-help">?</div>
                            <div class="tooltip-content">
                                Visualiza la proporción entre movimientos de Producción y Polinización
                            </div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="movimientosChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Espacio vacío reservado -->
            </div>
        </div>
    </div>

    <script>
        const dataApiarios = @json($dataApiarios);
        const dataVisitas = @json($dataVisitas);
        const actividadesData = @json($actividadesApiarios);
        const regionesData = @json($colmenasPorRegion);
        const tiposAlimentoData = @json($tiposAlimento);
        const movimientosData = @json($movimientosPorMotivo);
        const apicultoresData = @json($apicultoresPorRegion);
    </script>
@endsection

@push('styles')
    <link href="{{ asset('./css/components/home-user/graficos.css') }}" rel="stylesheet">
    <style>
        .btn-download-pdf {
            position: absolute;
            top: 0;
            right: 15px;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
            transition: all 0.3s ease;
        }

        .btn-download-pdf:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
            text-decoration: none;
            color: white;
        }

        .btn-download-pdf i {
            font-size: 1rem;
        }

        @media (max-width: 768px) {
            .btn-download-pdf {
                position: static;
                margin: 15px auto 0;
                display: flex;
                width: fit-content;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/components/admin/dashboard.js') }}"></script>
@endpush
