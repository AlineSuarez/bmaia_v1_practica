@extends('layouts.app')

@section('title', 'Maia - Dashboard Apícola')

@section('content')

    @php
        // Opcional: si prefieres filtrar por los tres tipos “buenos”
        $tiposOk = [
            'Visita General',
            'Inspección de Visita',
            'Uso de Medicamentos',
        ];
    @endphp

    <div class="container dashboard-container">
        <!-- Header del Dashboard -->
        <div class="row dashboard-header animate">
            <div class="col-md-12 text-center">
                <h1 class="dashboard-title">Panel de Control Apícola</h1>
                <p class="dashboard-subtitle">Visualiza y analiza el estado actual de tus apiarios y colmenas. Obtén
                    información valiosa para optimizar la producción y salud de tus abejas.</p>
            </div>
        </div>

        <!-- Tarjetas de información clave -->
        <div class="row mb-4">
            <div class="col-md-4 animate delay-1">
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-archive"></i>
                    </div>
                    <h3 class="info-card-title">Total de Apiarios</h3>
                    <div class="info-card-value">
                        {{ $dataApiarios->count() }}
                    </div>
                    <p class="info-card-text" style="font-weight: 600; font-size: medium; color: white;">Un apiario bien
                        gestionado puede
                        albergar entre 15-30 colmenas dependiendo del
                        espacio y recursos florales disponibles.</p>
                </div>
            </div>
            <div class="col-md-4 animate delay-2">
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-th"></i>
                    </div>
                    <h3 class="info-card-title">Total de Colmenas</h3>
                    <div class="info-card-value">
                        {{ array_sum(array_column($dataApiarios->toArray(), 'count')) }}
                    </div>
                    <p class="info-card-text" style="font-weight: 600; font-size: medium; color: white;">Una colmena
                        saludable puede contener hasta 60,000 abejas durante la temporada
                        alta de producción.</p>
                </div>
            </div>
            <div class="col-md-4 animate delay-3">
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="info-card-title">Visitas Realizadas</h3>
                    <div class="info-card-value">
                        {{ $visitas }}
                    </div>
                    <p class="info-card-text" style="font-weight: 600; font-size: medium; color: white;">Se recomienda
                        visitar cada colmena al menos una vez cada 7-10 días durante la
                        temporada activa.</p>
                </div>
            </div>
        </div>

        <!-- Gráficos principales con información contextual -->
        <div class="row">
            <div class="col-md-6 animate delay-2">
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title-container">
                            <h5 class="chart-title"><i class="fas fa-chart-bar"></i> Cantidad de Colmenas por Apiario</h5>
                            <p class="chart-subtitle">Distribución actual de colmenas en cada uno de tus apiarios
                                registrados.</p>
                        </div>
                        <div class="tooltip-container">
                            <div class="chart-help">?</div>
                            <div class="tooltip-content">
                                Este gráfico muestra la cantidad de colmenas en cada apiario. Un balance adecuado ayuda a
                                optimizar recursos y prevenir la sobrepoblación.
                            </div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="colmenasChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <p class="info-card-text">
                            <i class="fas fa-info-circle text-primary"></i>
                            La distribución ideal de colmenas depende de varios factores como la disponibilidad de recursos
                            florales, el espacio del terreno y la capacidad de manejo del apicultor.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 animate delay-2">
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title-container">
                            <h5 class="chart-title"><i class="fas fa-chart-pie"></i> Temporadas de Producción</h5>
                            <p class="chart-subtitle">Distribución de apiarios según su temporada principal de producción.
                            </p>
                        </div>
                        <div class="tooltip-container">
                            <div class="chart-help">?</div>
                            <div class="tooltip-content">
                                Este gráfico muestra qué temporadas son más productivas para tus apiarios. Ayuda a
                                planificar actividades y recursos a lo largo del año.
                            </div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="temporadasChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <p class="info-card-text">
                            <i class="fas fa-info-circle text-primary"></i>
                            La producción de miel varía según la temporada debido a la disponibilidad de flores, condiciones
                            climáticas y el ciclo natural de las abejas.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección educativa -->
        <div class="row animate delay-3">
            <div class="col-12">
                <div class="edu-section">
                    <h3 class="edu-title">¿Sabías que...?</h3>
                    <div class="edu-content">
                        <div class="edu-item">
                            <h4 class="edu-item-title"><i class="fas fa-bug"></i> Vida de las Abejas</h4>
                            <p class="edu-item-text">
                                Una abeja obrera vive aproximadamente 6 semanas durante la temporada activa, mientras que
                                las abejas nacidas en otoño pueden vivir hasta 6 meses para mantener la colonia durante el
                                invierno.
                            </p>
                        </div>
                        <div class="edu-item">
                            <h4 class="edu-item-title"><i class="fas fa-temperature-high"></i> Temperatura de la Colmena
                            </h4>
                            <p class="edu-item-text">
                                Las abejas mantienen la temperatura de la cámara de cría alrededor de 35°C (95°F),
                                independientemente de la temperatura exterior, mediante la vibración de sus músculos
                                torácicos.
                            </p>
                        </div>
                        <div class="edu-item">
                            <h4 class="edu-item-title"><i class="fas fa-tint"></i> Producción de Miel</h4>
                            <p class="edu-item-text">
                                Para producir 1 kg de miel, las abejas deben visitar aproximadamente 4 millones de flores y
                                volar el equivalente a 4 veces alrededor del mundo.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segunda fila de gráficos -->
        <div class="row">
            <div class="col-md-6 animate delay-3">
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title-container">
                            <h5 class="chart-title"><i class="fas fa-calendar-check"></i> Visitas por Apiario</h5>
                            <p class="chart-subtitle">Frecuencia de visitas realizadas a cada apiario para monitoreo y
                                mantenimiento.</p>
                        </div>
                        <div class="tooltip-container">
                            <div class="chart-help">?</div>
                            <div class="tooltip-content">
                                Este gráfico muestra la frecuencia de visitas a cada apiario. Un monitoreo regular es
                                esencial para detectar problemas a tiempo.
                            </div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="visitasApiariosChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <p class="info-card-text">
                            <i class="fas fa-info-circle text-primary"></i>
                            La frecuencia de visitas debe aumentar durante la temporada de enjambrazón y cuando hay riesgos
                            de plagas o enfermedades.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 animate delay-3">
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title-container">
                            <h5 class="chart-title"><i class="fas fa-clipboard-list"></i> Tipos de Visitas</h5>
                            <p class="chart-subtitle">Distribución de las visitas según su propósito y actividades
                                realizadas.</p>
                        </div>
                        <div class="tooltip-container">
                            <div class="chart-help">?</div>
                            <div class="tooltip-content">
                                Este gráfico muestra los diferentes tipos de visitas realizadas. Ayuda a entender qué
                                actividades demandan más tiempo y recursos.
                            </div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="tiposVisitasChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <p class="info-card-text">
                            <i class="fas fa-info-circle text-primary"></i>
                            Cada tipo de visita tiene objetivos específicos. Las revisiones regulares son fundamentales para
                            prevenir problemas, mientras que los tratamientos deben aplicarse solo cuando sea necesario.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de consejos -->
        <div class="row animate delay-4">
            <div class="col-12">
                <div class="tips-section">
                    <h3 class="tips-title"><i class="fas fa-lightbulb"></i> Consejos para Optimizar tu Apiario</h3>
                    <div class="tips-list">
                        <div class="tip-item">
                            <p class="tip-text"><strong>Ubicación:</strong> Coloca tus colmenas en lugares con sombra
                                parcial, protegidas del viento y con fácil acceso a fuentes de agua.</p>
                        </div>
                        <div class="tip-item">
                            <p class="tip-text"><strong>Espacio:</strong> Mantén al menos 2-3 metros entre colmenas para
                                reducir la deriva y facilitar el trabajo del apicultor.</p>
                        </div>
                        <div class="tip-item">
                            <p class="tip-text"><strong>Alimentación:</strong> Proporciona alimentación suplementaria
                                durante períodos de escasez de néctar para mantener colonias fuertes.</p>
                        </div>
                        <div class="tip-item">
                            <p class="tip-text"><strong>Registros:</strong> Mantén registros detallados de cada visita,
                                observaciones y tratamientos para tomar decisiones informadas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const dataApiarios = @json($dataApiarios);
        const dataVisitas = @json($dataVisitas);
    </script>
@endsection

@section('optional-scripts')
    <link href="{{ asset('./css/components/home-user/graficos.css') }}" rel="stylesheet">
    <script src="{{ asset('js/components/home-user/graficos.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection