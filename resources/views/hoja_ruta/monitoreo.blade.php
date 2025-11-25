@extends('layouts.app')

@section('title','B-MaiA - Monitoreo histórico del clima')

@section('content')
    @include('hoja_ruta.partials.subnav')

    <div class="zonificacion-container">

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

    {{-- Mismos estilos y fuente que en catálogo --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet"
          href="{{ asset('css/components/home-user/zonificacion.css') }}">

    <style>
        .climate-section-header{
            margin:14px 0 10px;
            display:flex;
            gap:16px;
            align-items:flex-start;
            flex-wrap:wrap;
        }
        .section-header-left{
            display:flex;
            flex-direction:column;
            gap:4px;
            max-width:480px;
        }
        .section-chip-live{
            display:inline-flex;
            align-items:center;
            gap:6px;
            font-size:.72rem;
            padding:3px 9px;
            border-radius:999px;
            background:linear-gradient(120deg,#ecfeff,#fef9c3);
            border:1px solid rgba(14,116,144,.18);
            color:#0f172a;
            font-weight:500;
        }
        .section-chip-live .dot{
            width:7px;
            height:7px;
            border-radius:999px;
            background:#22c55e;
            box-shadow:0 0 0 5px rgba(34,197,94,.25);
        }
        .section-sub{
            margin:0;
            font-size:.82rem;
            color:#6b7280;
        }
        .climate-zone-form{
            margin-left:auto;
            display:flex;
            gap:8px;
            align-items:center;
            padding:6px 8px;
            border-radius:999px;
            background:#ffffff;
            box-shadow:0 10px 28px rgba(15,23,42,.06);
            border:1px solid #e5e7eb;
        }
        @media (max-width: 768px){
            .climate-zone-form{
                width:100%;
                border-radius:14px;
                justify-content:space-between;
                flex-wrap:wrap;
            }
        }
        .climate-zone-label{
            font-size:.9rem;
            color:#4b5563;
            margin-right:2px;
            white-space:nowrap;
        }
        .climate-zone-select{
            min-width:220px;
            border-radius:999px;
            border-color:#e5e7eb;
            font-size:.9rem;
        }
        .climate-zone-select:focus{
            box-shadow:0 0 0 1px #22c55e33;
            border-color:#22c55e;
        }
        .climate-zone-btn{
            border-radius:999px;
            padding-inline:16px;
            background:linear-gradient(135deg,#fbbf24,#f97316);
            border-color:transparent;
            font-size:.86rem;
            display:inline-flex;
            align-items:center;
            gap:6px;
        }
        .climate-zone-btn::before{
            content:'\f021';
            font-family:"Font Awesome 6 Free";
            font-weight:900;
            font-size:.75rem;
        }

        .climate-period-text{
            font-size:.9rem;
            color:#6b7280;
            margin-bottom:12px;
        }

        /* ===== KPIs ===== */
        .climate-kpi-row{
            display:grid;
            grid-template-columns: repeat(1, minmax(0,1fr));
            gap:12px;
            margin-top:4px;
            margin-bottom:16px;
        }
        @media (min-width: 992px){
            .climate-kpi-row{
                grid-template-columns: repeat(5, minmax(0,1fr));
            }
        }
        .climate-kpi-card{
            background:radial-gradient(circle at top left,#fff7ed,#ffffff);
            border-radius:14px;
            border:1px solid #f3e6cf;
            box-shadow:0 10px 28px rgba(15,23,42,.05);
            padding:10px 12px;
            display:flex;
            flex-direction:column;
            gap:2px;
            position:relative;
            overflow:hidden;
        }
        .climate-kpi-card::after{
            content:'';
            position:absolute;
            inset:auto -30px -30px auto;
            width:70px;
            height:70px;
            background:radial-gradient(circle,#fed7aa,transparent 60%);
            opacity:.4;
            pointer-events:none;
        }
        .kpi-label{
            font-size:.8rem;
            color:#6b7280;
        }
        .kpi-value{
            font-size:1.4rem;
            font-weight:700;
            color:#111827;
        }
        .kpi-sub{
            font-size:.75rem;
            color:#9ca3af;
        }

        /* ===== Resumen climático (NUEVO) ===== */
        .climate-summary-card{
            background:radial-gradient(circle at top left,#ecfeff,#ffffff);
            border-radius:16px;
            border:1px solid rgba(59,130,246,.15);
            box-shadow:0 14px 40px rgba(15,23,42,.07);
            padding:12px 14px 14px;
            margin-bottom:18px;
            position:relative;
            overflow:hidden;
        }
        .climate-summary-card::before{
            content:'';
            position:absolute;
            inset:-40px auto auto -40px;
            width:120px;
            height:120px;
            background:radial-gradient(circle,#22c55e33,transparent 70%);
            opacity:.7;
            pointer-events:none;
        }
        .climate-summary-header{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:8px;
            margin-bottom:6px;
        }
        .climate-summary-header h3{
            margin:0;
            font-size:.98rem;
            font-weight:700;
            color:#0f172a;
        }
        .climate-summary-header p{
            margin:0;
            font-size:.8rem;
            color:#64748b;
        }
        .summary-pill{
            display:inline-flex;
            align-items:center;
            gap:6px;
            padding:4px 10px;
            border-radius:999px;
            background:#0f172a;
            color:#e5e7eb;
            font-size:.75rem;
            box-shadow:0 10px 30px rgba(15,23,42,.4);
            white-space:nowrap;
        }
        .summary-pill i{
            font-size:.7rem;
            color:#facc15;
        }
        .climate-summary-text{
            margin:4px 0 8px;
            font-size:.84rem;
            color:#111827;
            max-width:780px;
        }
        .climate-summary-tags{
            display:flex;
            flex-wrap:wrap;
            gap:6px;
        }
        .summary-chip{
            font-size:.72rem;
            padding:4px 9px;
            border-radius:999px;
            border:1px solid #e5e7eb;
            background:#f9fafb;
            color:#374151;
            display:inline-flex;
            align-items:center;
            gap:6px;
        }
        .summary-chip i{
            font-size:.72rem;
        }
        .summary-chip-wet{
            border-color:#0ea5e9;
            background:#e0f2fe;
            color:#0f172a;
        }
        .summary-chip-dry{
            border-color:#f97316;
            background:#ffedd5;
            color:#7c2d12;
        }
        .summary-chip-windy{
            border-color:#22c55e;
            background:#dcfce7;
            color:#14532d;
        }

        /* ===== Tarjeta de pronóstico 5 días ===== */
        .forecast-card{
            background:#ffffff;
            border-radius:14px;
            border:1px solid #e5e7eb;
            box-shadow:0 8px 24px rgba(15,23,42,.04);
            padding:10px 14px 14px;
            margin-bottom:18px;
        }
        .forecast-head{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            margin-bottom:8px;
            gap:8px;
        }
        .forecast-head h3{
            margin:0;
            font-size:.98rem;
            font-weight:700;
            color:#111827;
        }
        .forecast-head p{
            margin:2px 0 0;
            font-size:.78rem;
            color:#9ca3af;
        }
        .forecast-pill{
            font-size:.75rem;
            padding:4px 9px;
            border-radius:999px;
            border:1px solid #e5e7eb;
            background:#f9fafb;
            color:#4b5563;
            display:inline-flex;
            align-items:center;
            gap:6px;
            white-space:nowrap;
        }
        .forecast-pill i{
            font-size:.8rem;
            color:#ef4444;
        }
        .forecast-days{
            display:grid;
            grid-template-columns: repeat(5, minmax(0,1fr));
            gap:10px;
        }
        @media (max-width: 960px){
            .forecast-days{
                grid-template-columns: repeat(2, minmax(0,1fr));
            }
        }
        .forecast-day{
            background:#f9fafb;
            border-radius:12px;
            border:1px solid #e5e7eb;
            padding:8px 10px;
            text-align:center;
        }
        .f-day-name{
            font-size:.8rem;
            font-weight:600;
            color:#4b5563;
        }
        .f-day-icon{
            font-size:1.4rem;
            margin:2px 0;
        }
        .f-day-temp{
            font-size:.9rem;
            font-weight:600;
            color:#111827;
        }
        .f-day-prec{
            font-size:.75rem;
            color:#6b7280;
        }

        /* ===== Grid y tarjetas para los gráficos de clima ===== */
        .climate-grid{
            display:grid;
            grid-template-columns: repeat(2, minmax(0,1fr));
            gap:16px;
            margin-top:12px;
            margin-bottom:24px;
        }
        @media (min-width: 1400px){
            .climate-grid{ grid-template-columns: repeat(3, minmax(0,1fr)); }
        }
        @media (max-width: 960px){
            .climate-grid{ grid-template-columns: 1fr; }
        }

        .climate-card{
            background:#ffffff;
            border-radius:14px;
            border:1px solid #f3e6cf;
            box-shadow:0 8px 24px rgba(15,23,42,.06);
            padding:12px 14px 14px;
            display:flex;
            flex-direction:column;
            min-height:260px;
            position:relative;
            overflow:hidden;
        }
        .climate-card::before{
            content:'';
            position:absolute;
            inset:-50px auto auto -50px;
            width:120px;
            height:120px;
            background:radial-gradient(circle,#fed7aa55,transparent 70%);
            pointer-events:none;
        }
        .climate-card-head{
            display:flex;
            align-items:center;
            justify-content:space-between;
            margin-bottom:6px;
            position:relative;
            z-index:2;
        }
        .climate-card-title{
            font-size:1rem;
            font-weight:700;
            color:#111827;
        }
        .climate-card-tag{
            font-size:.8rem;
            padding:3px 9px;
            border-radius:999px;
            border:1px solid #fde68a;
            background:#fffbeb;
            color:#92400e;
            white-space:nowrap;
        }
        .climate-chart-wrapper{
            position:relative;
            flex:1;
            min-height:210px;
            z-index:2;
        }
    </style>

    {{-- Asegúrate de tener Chart.js cargado (si ya está en tu layout, este script es opcional) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // ===== Datos inyectados por el controlador =====
        const CL_LABELS    = @json($labels);
        const CL_TEMP_MEAN = @json($tempMean);
        const CL_TEMP_MAX  = @json($tempMax);
        const CL_PRECIP    = @json($precip);
        const CL_WIND      = @json($windSpeed);
        const CURRENT_ZONE = @json($zonaSeleccionada->nombre);

        // ===== Pronóstico 5 días con Open-Meteo =====
        async function loadForecast5d(){
            const container = document.getElementById('forecast-days');
            const sub = document.getElementById('forecast-sub');
            if (!container || !CURRENT_ZONE) return;

            try{
                sub.textContent = 'Cargando pronóstico…';

                // 1) Geocodificar nombre de la zona
                const geoRes = await fetch(
                    'https://geocoding-api.open-meteo.com/v1/search?name='
                    + encodeURIComponent(CURRENT_ZONE)
                    + '&count=1&language=es&format=json'
                );
                const geo = await geoRes.json();
                if (!geo.results || !geo.results.length) throw new Error('Sin coordenadas');

                const { latitude, longitude } = geo.results[0];

                // 2) Pronóstico diario 5 días
                const fcRes = await fetch(
                    'https://api.open-meteo.com/v1/forecast'
                    + '?latitude=' + latitude
                    + '&longitude=' + longitude
                    + '&daily=weathercode,temperature_2m_max,temperature_2m_min,precipitation_sum'
                    + '&timezone=auto&forecast_days=5'
                );
                const fc = await fcRes.json();
                const daily = fc.daily;
                if (!daily || !daily.time || !daily.time.length) throw new Error('Sin datos');

                const dayNames = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];

                container.innerHTML = '';
                sub.textContent = 'Zona aproximada: ' + CURRENT_ZONE;

                const mapWeatherCode = (code) => {
                    if (code === 0) return '☀️';
                    if (code === 1 || code === 2) return '🌤️';
                    if (code === 3) return '☁️';
                    if (code === 45 || code === 48) return '🌫️';
                    if (code >= 51 && code <= 67) return '🌧️';
                    if (code >= 71 && code <= 77) return '❄️';
                    if (code >= 80 && code <= 82) return '🌦️';
                    if (code >= 95) return '⛈️';
                    return 'ℹ️';
                };

                for (let i = 0; i < daily.time.length && i < 5; i++){
                    const d = new Date(daily.time[i]);
                    const name = dayNames[d.getDay()];
                    const max = daily.temperature_2m_max[i];
                    const min = daily.temperature_2m_min[i];
                    const prec = daily.precipitation_sum[i];
                    const code = daily.weathercode[i];

                    const el = document.createElement('div');
                    el.className = 'forecast-day';
                    el.innerHTML = `
                        <div class="f-day-name">${name}</div>
                        <div class="f-day-icon">${mapWeatherCode(code)}</div>
                        <div class="f-day-temp">${Math.round(max)}° / ${Math.round(min)}°</div>
                        <div class="f-day-prec">${prec.toFixed(1)} mm</div>
                    `;
                    container.appendChild(el);
                }

            }catch(e){
                if(sub) sub.textContent = 'Pronóstico no disponible para esta zona.';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const hasLabels = CL_LABELS && CL_LABELS.length;

            // Aunque no haya datos históricos, intentamos cargar pronóstico
            loadForecast5d();

            if (!hasLabels) return;

            // ==== helpers numéricos ====
            const cleanArray = arr =>
                (arr || []).filter(v => v !== null && !Number.isNaN(v));

            const avg = arr => {
                const vals = cleanArray(arr);
                if (!vals.length) return null;
                return vals.reduce((a,b)=>a+b,0) / vals.length;
            };

            const sum = arr => {
                const vals = cleanArray(arr);
                if (!vals.length) return null;
                return vals.reduce((a,b)=>a+b,0);
            };

            const maxWithIndex = arr => {
                const vals = arr || [];
                let maxVal = null;
                let idx = -1;
                vals.forEach((v,i) => {
                    if (v === null || Number.isNaN(v)) return;
                    if (maxVal === null || v > maxVal){
                        maxVal = v;
                        idx = i;
                    }
                });
                return { value:maxVal, index:idx };
            };

            const minWithIndex = arr => {
                const vals = arr || [];
                let minVal = null;
                let idx = -1;
                vals.forEach((v,i) => {
                    if (v === null || Number.isNaN(v)) return;
                    if (minVal === null || v < minVal){
                        minVal = v;
                        idx = i;
                    }
                });
                return { value:minVal, index:idx };
            };

            function describeDate(label){
                if (!label) return '';
                const d = new Date(label);
                if (!Number.isNaN(d.getTime())){
                    const today = new Date();
                    const sameDay =
                        d.getFullYear() === today.getFullYear() &&
                        d.getMonth() === today.getMonth() &&
                        d.getDate() === today.getDate();

                    if (sameDay) return ' (hoy)';
                }
                return ' (' + label + ')';
            }

            // Convertimos viento a km/h (si viene en m/s)
            const WIND_KMH = (CL_WIND || []).map(v =>
                (v !== null && !Number.isNaN(v)) ? v * 3.6 : null
            );

            // ==== Pintar KPIs ====
            const kpiTempMean     = document.getElementById('kpi-temp-mean');
            const kpiTempMeanSub  = document.getElementById('kpi-temp-mean-sub');
            const kpiTempMax      = document.getElementById('kpi-temp-max');
            const kpiTempMaxSub   = document.getElementById('kpi-temp-max-sub');
            const kpiTempMin      = document.getElementById('kpi-temp-min');
            const kpiTempMinSub   = document.getElementById('kpi-temp-min-sub');
            const kpiPrecip       = document.getElementById('kpi-precip');
            const kpiPrecipSub    = document.getElementById('kpi-precip-sub');
            const kpiWind         = document.getElementById('kpi-wind');

            const { value:maxTemp, index:idxMax } = maxWithIndex(CL_TEMP_MAX);
            const { value:minTemp, index:idxMin } = minWithIndex(CL_TEMP_MEAN);
            const labelMax = idxMax >= 0 ? CL_LABELS[idxMax] : null;
            const labelMin = idxMin >= 0 ? CL_LABELS[idxMin] : null;

            const avgTemp = avg(CL_TEMP_MEAN);
            if (kpiTempMean && avgTemp !== null){
                kpiTempMean.textContent = avgTemp.toFixed(1) + ' °C';
            }
            if (kpiTempMeanSub){
                kpiTempMeanSub.textContent = 'Promedio diario último año';
            }

            if (kpiTempMax && maxTemp !== null){
                kpiTempMax.textContent = maxTemp.toFixed(1) + ' °C';
            }
            if (kpiTempMaxSub && maxTemp !== null){
                kpiTempMaxSub.textContent =
                    'Pico de calor' + describeDate(labelMax);
            }

            if (kpiTempMin && minTemp !== null){
                kpiTempMin.textContent = minTemp.toFixed(1) + ' °C';
            }
            if (kpiTempMinSub && minTemp !== null){
                kpiTempMinSub.textContent =
                    'Día más frío' + describeDate(labelMin);
            }

            const totalPrecip = sum(CL_PRECIP);
            if (kpiPrecip && totalPrecip !== null){
                kpiPrecip.textContent = totalPrecip.toFixed(0) + ' mm';
            }

            const { value:maxWind } = maxWithIndex(WIND_KMH);
            if (kpiWind && maxWind !== null){
                kpiWind.textContent = maxWind.toFixed(0) + ' km/h';
            }

            if (kpiPrecipSub && totalPrecip !== null){
                kpiPrecipSub.textContent = 'Suma anual aproximada (' + totalPrecip.toFixed(0) + ' mm)';
            }

            // ==== series derivadas para nuevos gráficos ====
            const PRECIP_ACC = [];
            let acc = 0;
            (CL_PRECIP || []).forEach(v => {
                if (v !== null && !Number.isNaN(v)) acc += v;
                PRECIP_ACC.push(acc);
            });

            const TEMP_MEAN_SMOOTH = (CL_TEMP_MEAN || []).map((_, i) => {
                let count = 0, s = 0;
                const start = Math.max(0, i - 6); // ventana 7 días
                for (let j = start; j <= i; j++){
                    const v = CL_TEMP_MEAN[j];
                    if (v !== null && !Number.isNaN(v)){
                        s += v;
                        count++;
                    }
                }
                return count ? s / count : null;
            });

            // ==== Resumen climático (texto y chips) ====
            const summaryTextEl  = document.getElementById('climate-summary-text');
            const summaryTagsEl  = document.getElementById('climate-summary-tags');

            function buildClimateSummary(){
                if (!summaryTextEl || !summaryTagsEl) return;

                const rainyDays = (CL_PRECIP || []).filter(v => v !== null && !Number.isNaN(v) && v > 0.1).length;
                const avgWind   = avg(WIND_KMH);

                let text = 'En el último año registrado para ' + CURRENT_ZONE + ', '
                    + 'la temperatura media fue de ' + (avgTemp !== null ? avgTemp.toFixed(1) + ' °C' : '—') + ', ';

                if (maxTemp !== null){
                    text += 'con un máximo de ' + maxTemp.toFixed(1) + ' °C' + describeDate(labelMax) + ' y ';
                } else {
                    text += 'con un máximo no determinado y ';
                }

                if (minTemp !== null){
                    text += 'un mínimo de ' + minTemp.toFixed(1) + ' °C' + describeDate(labelMin) + '. ';
                } else {
                    text += 'un mínimo no determinado. ';
                }

                if (totalPrecip !== null){
                    text += 'En total se acumularon alrededor de ' + totalPrecip.toFixed(0)
                        + ' mm de lluvia en ' + rainyDays + ' días con precipitación registrada. ';
                }

                if (maxWind !== null){
                    text += 'La racha de viento más intensa alcanzó aproximadamente '
                        + maxWind.toFixed(0) + ' km/h.';
                }

                summaryTextEl.textContent = text;

                // Chips
                summaryTagsEl.innerHTML = '';
                if (totalPrecip !== null){
                    let chipClass = 'summary-chip';
                    let label = 'Patrón hídrico intermedio';

                    if (totalPrecip < 200){
                        chipClass += ' summary-chip-dry';
                        label = 'Patrón más bien seco';
                    } else if (totalPrecip > 800){
                        chipClass += ' summary-chip-wet';
                        label = 'Patrón más bien húmedo';
                    }

                    const chip = document.createElement('span');
                    chip.className = chipClass;
                    chip.innerHTML = '<i class="fa-solid fa-droplet"></i>' + label;
                    summaryTagsEl.appendChild(chip);
                }

                if (avgTemp !== null){
                    const chip = document.createElement('span');
                    chip.className = 'summary-chip';
                    chip.innerHTML =
                        '<i class="fa-solid fa-temperature-half"></i>'
                        + 'Media anual: ' + avgTemp.toFixed(1) + ' °C';
                    summaryTagsEl.appendChild(chip);
                }

                if (rainyDays){
                    const chip = document.createElement('span');
                    chip.className = 'summary-chip summary-chip-wet';
                    chip.innerHTML =
                        '<i class="fa-solid fa-cloud-rain"></i>'
                        + rainyDays + ' días con lluvia';
                    summaryTagsEl.appendChild(chip);
                }

                if (avgWind !== null){
                    const chip = document.createElement('span');
                    chip.className = 'summary-chip summary-chip-windy';
                    chip.innerHTML =
                        '<i class="fa-solid fa-wind"></i>'
                        + 'Viento medio máx.: ' + avgWind.toFixed(0) + ' km/h';
                    summaryTagsEl.appendChild(chip);
                }
            }

            buildClimateSummary();

            // ==== Helpers para gráficos ====
            function makeLineChart(canvasId, label, data, colorLine, colorFill, unit){
                const canvas = document.getElementById(canvasId);
                if (!canvas || typeof Chart === 'undefined') return;
                const ctx = canvas.getContext('2d');

                const gradient = ctx.createLinearGradient(0, 0, 0, 220);
                gradient.addColorStop(0, colorFill);
                gradient.addColorStop(1, 'rgba(255,255,255,0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: CL_LABELS,
                        datasets: [{
                            label: label,
                            data: data,
                            borderColor: colorLine,
                            backgroundColor: gradient,
                            tension: 0.25,
                            pointRadius: 0,
                            borderWidth: 2,
                            fill: true,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor:'#111827',
                                borderColor:'#4b5563',
                                borderWidth:1,
                                titleColor:'#e5e7eb',
                                bodyColor:'#f9fafb',
                                padding:10,
                                displayColors:false,
                                callbacks: {
                                    label: ctx => {
                                        const v = ctx.parsed.y;
                                        if (v === null || Number.isNaN(v)) return '';
                                        return v.toFixed(1) + ' ' + unit;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                display: false,
                                grid: { display: false }
                            },
                            y: {
                                grid: { color: 'rgba(148,163,184,0.18)', drawBorder:false },
                                ticks:{ color:'#6b7280' }
                            }
                        }
                    }
                });
            }

            function makeBarChart(canvasId, label, data, colorBar, unit){
                const canvas = document.getElementById(canvasId);
                if (!canvas || typeof Chart === 'undefined') return;
                const ctx = canvas.getContext('2d');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: CL_LABELS,
                        datasets: [{
                            label: label,
                            data: data,
                            backgroundColor: colorBar,
                            borderColor: colorBar,
                            borderWidth: 1,
                            borderRadius: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor:'#111827',
                                borderColor:'#4b5563',
                                borderWidth:1,
                                titleColor:'#e5e7eb',
                                bodyColor:'#f9fafb',
                                padding:10,
                                displayColors:false,
                                callbacks: {
                                    label: ctx => {
                                        const v = ctx.parsed.y;
                                        if (v === null || Number.isNaN(v)) return '';
                                        return v.toFixed(1) + ' ' + unit;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                display: false,
                                grid: { display:false }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color:'rgba(148,163,184,0.18)', drawBorder:false },
                                ticks:{ color:'#6b7280' }
                            }
                        }
                    }
                });
            }

            // ==== Dibujar gráficos ====
            // Temperaturas
            makeLineChart(
                'chart-temp-mean',
                '°C promedio',
                CL_TEMP_MEAN,
                'rgba(249,115,22,1)',
                'rgba(249,115,22,0.35)',
                '°C'
            );

            makeLineChart(
                'chart-temp-max',
                '°C máxima',
                CL_TEMP_MAX,
                'rgba(239,68,68,1)',
                'rgba(239,68,68,0.30)',
                '°C'
            );

            // Precipitaciones (mm)
            makeBarChart(
                'chart-precip',
                'Precipitación',
                CL_PRECIP,
                'rgba(59,130,246,0.80)',
                'mm'
            );

            // Viento (km/h) usando la serie convertida
            makeBarChart(
                'chart-wind',
                'Viento máx.',
                WIND_KMH,
                'rgba(16,185,129,0.80)',
                'km/h'
            );

            // Precipitación acumulada
            makeLineChart(
                'chart-precip-acc',
                'Prec. acumulada',
                PRECIP_ACC,
                'rgba(37,99,235,1)',
                'rgba(37,99,235,0.25)',
                'mm'
            );

            // Tendencia temperatura (media móvil)
            makeLineChart(
                'chart-temp-mean-smooth',
                'Temp. prom. (7 días)',
                TEMP_MEAN_SMOOTH,
                'rgba(234,88,12,1)',
                'rgba(234,88,12,0.25)',
                '°C'
            );
        });
    </script>
@endsection
