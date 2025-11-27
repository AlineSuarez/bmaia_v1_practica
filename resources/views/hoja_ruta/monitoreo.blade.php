{{-- resources/views/hoja_ruta/monitoreo.blade.php --}}
@extends('layouts.app')

@section('title','B-MaiA - Monitoreo histórico del clima')

@section('content')
    @include('hoja_ruta.partials.subnav')

    {{-- Estilos compartidos (banner honeycomb, etc.) --}}
    <link rel="stylesheet"
          href="{{ asset('css/components/home-user/zonificacion.css') }}">

    {{-- ✅ Estilos específicos de Monitoreo Histórico --}}
    <link rel="stylesheet"
          href="{{ asset('css/components/home-user/hoja-ruta-monitoreo.css') }}">

    <div class="zonificacion-container"
         id="climate-root"
         data-labels='@json($labels)'
         data-temp-mean='@json($tempMean)'
         data-temp-max='@json($tempMax)'
         data-precip='@json($precip)'
         data-wind='@json($windSpeed)'
         data-current-zone="{{ $zonaSeleccionada->nombre }}">

        {{-- Mismos estilos y fuente que en catálogo --}}
        <link rel="stylesheet"
              href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet"
              href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">

        <!-- ===== Banner ===== -->
        <div class="honeycomb-header">
            <div class="honeycomb-overlay"></div>
            <div class="header-content">
                <h1 class="zonificacion-title">Monitoreo Histórico del Clima</h1>
                <p class="zonificacion-subtitle">
                    Gráficos y análisis de tendencias históricas de temperatura, precipitaciones y viento.
                </p>
            </div>
        </div>

        <!-- ===== Selector de zona / cabecera ===== -->
        <div class="section-header climate-section-header">
            <div class="section-header-left">
                <span class="section-chip-live">
                    <span class="dot"></span> Datos históricos
                </span>
                <h2 class="section-title">
                    <i class="fa-solid fa-cloud-sun-rain"></i>
                    Serie climática por zona
                </h2>
                <p class="section-sub">
                    Revisa el comportamiento del clima en el último año aproximado para cada zona operativa.
                </p>
            </div>

            <form method="GET"
                  action="{{ route('hoja.monitoreo') }}"
                  class="climate-zone-form">
                <label for="zona_id" class="climate-zone-label">
                    Zona:
                </label>
                <select name="zona_id" id="zona_id"
                        class="form-select climate-zone-select">
                    @foreach($zonas as $zona)
                        <option value="{{ $zona->id }}"
                                @selected($zonaSeleccionada->id === $zona->id)>
                            {{ $zona->nombre }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                        class="btn btn-primary climate-zone-btn">
                    Actualizar
                </button>
            </form>
        </div>

        <p class="climate-period-text">
            Serie diaria del último año aproximado para
            <strong>{{ $zonaSeleccionada->nombre }}</strong>.
        </p>

        @php
            $hasData = !empty($labels);
        @endphp

        @if (!$hasData)
            <div class="alert alert-warning">
                No se encontraron datos climáticos para esta zona en el periodo consultado.
            </div>
        @else

            <!-- ===== KPIs (incluye temp. mínima) ===== -->
            <div class="climate-kpi-row">
                <div class="climate-kpi-card">
                    <span class="kpi-label">Temp. promedio</span>
                    <span class="kpi-value" id="kpi-temp-mean">-- °C</span>
                    <span class="kpi-sub" id="kpi-temp-mean-sub">Promedio diario último año</span>
                </div>

                <div class="climate-kpi-card">
                    <span class="kpi-label">Temp. máxima</span>
                    <span class="kpi-value" id="kpi-temp-max">-- °C</span>
                    <span class="kpi-sub" id="kpi-temp-max-sub">Pico de calor</span>
                </div>

                <div class="climate-kpi-card">
                    <span class="kpi-label">Temp. mínima</span>
                    <span class="kpi-value" id="kpi-temp-min">-- °C</span>
                    <span class="kpi-sub" id="kpi-temp-min-sub">Día más frío</span>
                </div>

                <div class="climate-kpi-card">
                    <span class="kpi-label">Precipitación total</span>
                    <span class="kpi-value" id="kpi-precip">-- mm</span>
                    <span class="kpi-sub" id="kpi-precip-sub">Suma anual aproximada</span>
                </div>

                <div class="climate-kpi-card">
                    <span class="kpi-label">Viento máximo</span>
                    <span class="kpi-value" id="kpi-wind">-- km/h</span>
                    <span class="kpi-sub" id="kpi-wind-sub">Racha más alta</span>
                </div>
            </div>

            <!-- ===== Resumen climático del período (NUEVO) ===== -->
            <div class="climate-summary-card">
                <div class="climate-summary-header">
                    <div>
                        <h3>Resumen climático del período</h3>
                        <p>Descripción automática basada en los registros de la zona seleccionada.</p>
                    </div>
                    <span class="summary-pill">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        Generado a partir de datos reales
                    </span>
                </div>
                <p class="climate-summary-text" id="climate-summary-text">
                    Analizando los datos para generar un resumen…
                </p>
                <div class="climate-summary-tags" id="climate-summary-tags">
                    <!-- Chips generados desde JS -->
                </div>
            </div>

            <!-- ===== Pronóstico próximos 5 días ===== -->
            <div class="forecast-card">
                <div class="forecast-head">
                    <div>
                        <h3>Pronóstico próximos 5 días</h3>
                        <p id="forecast-sub">Cargando pronóstico…</p>
                    </div>
                    <span class="forecast-pill">
                        <i class="fa-solid fa-location-dot"></i>
                        Basado en la zona seleccionada
                    </span>
                </div>
                <div class="forecast-days" id="forecast-days"></div>
            </div>

            <!-- ===== Grid de tarjetas con gráficos (6 en total) ===== -->
            <div class="climate-grid">
                <!-- 1: Temp promedio -->
                <div class="climate-card">
                    <div class="climate-card-head">
                        <h3 class="climate-card-title">Temperatura promedio</h3>
                        <span class="climate-card-tag">°C diarios</span>
                    </div>
                    <div class="climate-chart-wrapper">
                        <canvas id="chart-temp-mean"></canvas>
                    </div>
                </div>

                <!-- 2: Temp máxima -->
                <div class="climate-card">
                    <div class="climate-card-head">
                        <h3 class="climate-card-title">Temperatura máxima</h3>
                        <span class="climate-card-tag">°C diarios</span>
                    </div>
                    <div class="climate-chart-wrapper">
                        <canvas id="chart-temp-max"></canvas>
                    </div>
                </div>

                <!-- 3: Precipitaciones -->
                <div class="climate-card">
                    <div class="climate-card-head">
                        <h3 class="climate-card-title">Precipitaciones</h3>
                        <span class="climate-card-tag">mm por día</span>
                    </div>
                    <div class="climate-chart-wrapper">
                        <canvas id="chart-precip"></canvas>
                    </div>
                </div>

                <!-- 4: Viento -->
                <div class="climate-card">
                    <div class="climate-card-head">
                        <h3 class="climate-card-title">Velocidad del viento</h3>
                        <span class="climate-card-tag">máxima diaria (km/h)</span>
                    </div>
                    <div class="climate-chart-wrapper">
                        <canvas id="chart-wind"></canvas>
                    </div>
                </div>

                <!-- 5: Precipitación acumulada -->
                <div class="climate-card">
                    <div class="climate-card-head">
                        <h3 class="climate-card-title">Precipitación acumulada</h3>
                        <span class="climate-card-tag">mm acumulados</span>
                    </div>
                    <div class="climate-chart-wrapper">
                        <canvas id="chart-precip-acc"></canvas>
                    </div>
                </div>

                <!-- 6: Tendencia temp. promedio (media móvil) -->
                <div class="climate-card">
                    <div class="climate-card-head">
                        <h3 class="climate-card-title">Tendencia temp. promedio (7 días)</h3>
                        <span class="climate-card-tag">media móvil</span>
                    </div>
                    <div class="climate-chart-wrapper">
                        <canvas id="chart-temp-mean-smooth"></canvas>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/hoja_ruta/monitoreo.js') }}"></script>
@endpush
