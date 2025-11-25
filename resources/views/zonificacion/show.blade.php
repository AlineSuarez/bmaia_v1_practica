@extends('layouts.app')
@section('title', 'B-Maia - Detalle Apiario')

@section('content')
<div class="zonificacion-container">
    <!-- Header con efecto panal -->
    <div class="honeycomb-header">
        <div class="honeycomb-overlay"></div>
        <div class="header-content">
            <h1 class="zonificacion-title">{{ $apiario->nombre ?? 'Apiario' }}</h1>
            <p class="zonificacion-subtitle">Detalle y localización del apiario seleccionado</p>
        </div>
    </div>

    <!-- Chips resumen -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-house-chimney"></i></div>
            <div class="stat-content">
                <h3>Apiario</h3>
                <p class="stat-value">{{ $apiario->nombre ?? '—' }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
            <div class="stat-content">
                <h3>Colmenas</h3>
                <p class="stat-value">{{ $apiario->num_colmenas ?? '—' }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-location-dot"></i></div>
            <div class="stat-content">
                <h3>Ubicación</h3>
                <p class="stat-value">
                    @if(!is_null($apiario->latitud) && !is_null($apiario->longitud))
                        {{ number_format((float)$apiario->latitud, 6) }}, {{ number_format((float)$apiario->longitud, 6) }}
                    @else
                        Sin ubicación
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="tabs-container">
        <div class="tabs-nav">
            <button class="tab-btn active" data-tab="monitoreo">
                <i class="fa-solid fa-leaf"></i>
                <span class="tab-text">Monitoreo Ambiental</span>
            </button>

            <button class="tab-btn" data-tab="floraciones">
                <i class="fa-solid fa-calendar-days"></i>
                <span class="tab-text">Prediccion de Floracion</span>
            </button>

            <button class="tab-btn" data-tab="rendimiento">
                <i class="fa-solid fa-chart-line"></i>
                <span class="tab-text">Predicción de Rendimiento</span>
            </button>

            <button class="tab-btn" data-tab="catalogo">
                <i class="fa-solid fa-seedling"></i>
                <span class="tab-text">Catálogo de Flora</span>
            </button>

            <button class="tab-btn" data-tab="historico">
                <i class="fa-solid fa-chart-area"></i>
                <span class="tab-text">Monitoreo Histórico</span>
            </button>

                        <button class="tab-btn" data-tab="historico">
                <i class="fa-solid fa-chart-area"></i>
                <span class="tab-text">Monitoreo Histórico</span>
            </button>
                        <button class="tab-btn" data-tab="historico">
                <i class="fa-solid fa-chart-area"></i>
                <span class="tab-text">Monitoreo Histórico</span>
            </button>
        </div>

        <!-- ===== TAB: Monitoreo Ambiental ===== -->
        <div class="tab-content active" data-tab="monitoreo">
            <!-- ====== MÉTRICAS EN UNA FILA ====== -->
            <div class="weather-row" id="weather-now" style="margin-top:12px;">
                <div class="stat-card weather">
                    <div class="stat-icon"><i class="fa-solid fa-temperature-half"></i></div>
                    <div class="stat-content">
                        <h3>Temperatura</h3>
                        <p class="stat-value" id="w-temp">—</p>
                    </div>
                </div>
                <div class="stat-card weather">
                    <div class="stat-icon"><i class="fa-solid fa-droplet"></i></div>
                    <div class="stat-content">
                        <h3>Humedad</h3>
                        <p class="stat-value" id="w-humidity">—</p>
                    </div>
                </div>
                <div class="stat-card weather">
                    <div class="stat-icon"><i class="fa-solid fa-cloud-rain"></i></div>
                    <div class="stat-content">
                        <h3>Precipitación</h3>
                        <p class="stat-value" id="w-rain">—</p>
                    </div>
                </div>
                <div class="stat-card weather">
                    <div class="stat-icon"><i class="fa-solid fa-sun"></i></div>
                    <div class="stat-content">
                        <h3>Índice UV</h3>
                        <p class="stat-value" id="w-uv">—</p>
                    </div>
                </div>
                <div class="stat-card weather">
                    <div class="stat-icon"><i class="fa-solid fa-gauge-high"></i></div>
                    <div class="stat-content">
                        <h3>Presión</h3>
                        <p class="stat-value" id="w-pressure">—</p>
                    </div>
                </div>
            </div>

            <!-- ====== PRONÓSTICO 6 DÍAS ====== -->
            <div class="section-header" style="margin-top:8px;">
                <h2 class="section-title">
                    <i class="fa-solid fa-calendar-week"></i>
                    Pronóstico para 6 días
                </h2>
            </div>
            <div id="forecast-6" class="forecast-scroll">
                <!-- JS inyecta tarjetas -->
            </div>

            <!-- ====== MAPA ====== -->
            <div class="map-section">
                <div class="section-header">
                    <h2 class="section-title"><i class="fa-solid fa-map"></i> Ubicación del Apiario</h2>
                    <div class="map-controls">
                        <a href="{{ route('zonificacion') }}" class="view-btn" title="Volver a zonificación">
                            <i class="fa-solid fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <div id="map-container" style="position:relative;">
                    <div id="map" class="apiary-map" style="height: 480px; border-radius: 12px;"></div>
                </div>
            </div>
        </div>

        <!-- ===== TAB: Calendario de Floraciones ===== -->
        <div class="tab-content" data-tab="floraciones">
            <div class="section-header" style="margin-top:8px;">
                <h2 class="section-title">
                    <i class="fa-solid fa-calendar-days"></i>
                    Predicción de Floración
                    <span id="pf-especie-titulo" class="pf-title"></span>
                </h2>
            </div>

            <div class="pf-layout">
                <!-- Lista de especies -->
                <aside class="pf-sidebar">
                    <div class="pf-side-title">
                        <i class="fa-solid fa-seedling"></i> Flora Nativa
                    </div>
                    <ul class="pf-species-list" id="pf-species-list">
                        {{-- JS pinta la lista con radio buttons --}}
                    </ul>
                    <div class="pf-hint">
                        <i class="fa-regular fa-lightbulb"></i>
                        Selecciona una especie para ver sus etapas de floración.
                    </div>
                </aside>

                <!-- Grilla de fenofases -->
                <section class="pf-grid">
                    <div class="pf-grid-head">
                        @foreach($fenofases as $f)
                            <div class="pf-col-head">
                                <div class="pf-col-title">{{ $f->nombre }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pf-grid-body" id="pf-grid-body">
                        {{-- JS inyecta las 4 imágenes (o placeholders) según la especie seleccionada --}}
                    </div>

                    <div class="pf-grid-foot">
                        @foreach($fenofases as $f)
                            <div class="pf-col-foot">
                                @if($f->clave === 'boton')
                                    <span class="pf-foot-note pf-green">Botón floral</span>
                                @elseif($f->clave === 'inicio')
                                    <span class="pf-foot-note pf-green">10% Flores abiertas</span>
                                @elseif($f->clave === 'plena')
                                    <span class="pf-foot-note pf-red">20–75% Floración Máxima</span>
                                @elseif($f->clave === 'terminal')
                                    <span class="pf-foot-note pf-green">10% Flores abiertas y en declive</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- ====== NUEVO BLOQUE: Agroclima + Timeline fenológico ====== -->
                    <div class="agro-wrapper"
                        style="margin-top:2rem; border:1px solid #e2e8f0; border-radius:12px; padding:16px; background:#fffef8;
                               box-shadow:0 8px 24px rgba(0,0,0,.04);">

                        <div style="display:flex; flex-wrap:wrap; gap:24px; align-items:flex-start;">
                            <!-- Clima actual -->
                            <div style="flex:1 1 240px; min-width:240px;">
                                <h3 style="font-size:1rem; font-weight:600; color:#444; display:flex; align-items:center; gap:8px;">
                                    <i class="fa-solid fa-cloud-sun-rain" style="color:#f39c12;"></i>
                                    Condiciones agroclimáticas
                                </h3>

                                <p style="font-size:.85rem; color:#666; margin-top:4px;">
                                    Temperatura y lluvia estimadas para la ubicación del apiario.
                                </p>

                                <div style="display:flex; flex-direction:column; gap:8px; margin-top:12px;">

                                    <div style="display:flex; align-items:flex-start; gap:10px;
                                                background:#fff; border:1px solid #f5dbc0; border-radius:10px;
                                                padding:10px 12px; box-shadow:0 4px 12px rgba(0,0,0,.03);">
                                        <div style="font-size:1.2rem; line-height:1;">
                                            <i class="fa-solid fa-temperature-half" style="color:#e67e22;"></i>
                                        </div>
                                        <div style="font-size:.9rem;">
                                            <div style="color:#444; font-weight:600;">Temperatura</div>
                                            <div style="color:#2c3e50;">
                                                <span id="agro-temp">—</span> °C
                                            </div>
                                        </div>
                                    </div>

                                    <div style="display:flex; align-items:flex-start; gap:10px;
                                                background:#fff; border:1px solid #d0e5ff; border-radius:10px;
                                                padding:10px 12px; box-shadow:0 4px 12px rgba(0,0,0,.03);">
                                        <div style="font-size:1.2rem; line-height:1;">
                                            <i class="fa-solid fa-cloud-rain" style="color:#3498db;"></i>
                                        </div>
                                        <div style="font-size:.9rem;">
                                            <div style="color:#444; font-weight:600;">Precipitación</div>
                                            <div style="color:#2c3e50;">
                                                <span id="agro-pp">—</span> mm
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Timeline fenológico -->
                            <div style="flex:2 1 320px; min-width:280px;">
                                <h3 style="font-size:1rem; font-weight:600; color:#444; display:flex; align-items:center; gap:8px;">
                                    <i class="fa-solid fa-seedling" style="color:#27ae60;"></i>
                                    Línea de tiempo de floración
                                </h3>

                                <p style="font-size:.85rem; color:#666; margin-top:4px;">
                                    Marca cuándo viste <strong>Botón floral</strong> y calculamos las fechas estimadas
                                    para las siguientes etapas de floración.
                                </p>

                                <!-- selector de fecha -->
                                <div style="margin-top:12px;">
                                    <label for="fecha-boton"
                                           style="font-size:.8rem; color:#555; font-weight:500; display:block; margin-bottom:4px;">
                                        Fecha observada de "Botón floral"
                                    </label>
                                    <input id="fecha-boton"
                                           type="date"
                                           style="width:100%; max-width:220px;
                                                  border:1px solid #d1d5db; border-radius:8px;
                                                  padding:8px 10px; font-size:.9rem; outline:none;">
                                </div>

                                <!-- offsets usados (se actualizan por especie) -->
                                <div id="pf-offsets" style="display:flex; gap:8px; flex-wrap:wrap; margin-top:10px;">
                                    <span class="pf-foot-note" id="off-inicio-chip">Inicio: —</span>
                                    <span class="pf-foot-note" id="off-plena-chip">Plena: —</span>
                                    <span class="pf-foot-note" id="off-terminal-chip">Terminal: —</span>
                                </div>

                                <!-- resultados de cálculo -->
                                <div style="display:flex; flex-wrap:wrap; gap:12px; margin-top:16px;">

                                    <div style="flex:1 1 140px; min-width:140px;
                                                background:#fff; border:1px solid #d1fae5; border-radius:10px;
                                                padding:12px; box-shadow:0 4px 12px rgba(0,0,0,.03);">
                                        <div style="font-size:.75rem; font-weight:600; color:#065f46; margin-bottom:4px;">
                                            Inicio de floración
                                        </div>
                                        <div style="font-size:.9rem; color:#111;">
                                            <span id="fecha-inicio">—</span>
                                        </div>
                                    </div>

                                    <div style="flex:1 1 140px; min-width:140px;
                                                background:#fff; border:1px solid #fde68a; border-radius:10px;
                                                padding:12px; box-shadow:0 4px 12px rgba(0,0,0,.03);">
                                        <div style="font-size:.75rem; font-weight:600; color:#92400e; margin-bottom:4px;">
                                            Plena floración
                                        </div>
                                        <div style="font-size:.9rem; color:#111;">
                                            <span id="fecha-plena">—</span>
                                        </div>
                                    </div>

                                    <div style="flex:1 1 140px; min-width:140px;
                                                background:#fff; border:1px solid #fecaca; border-radius:10px;
                                                padding:12px; box-shadow:0 4px 12px rgba(0,0,0,.03);">
                                        <div style="font-size:.75rem; font-weight:600; color:#7f1d1d; margin-bottom:4px;">
                                            Floración terminal
                                        </div>
                                        <div style="font-size:.9rem; color:#111;">
                                            <span id="fecha-terminal">—</span>
                                        </div>
                                    </div>
                                </div>

                                <p style="font-size:.7rem; color:#999; margin-top:10px; line-height:1.4;">
                                    Nota: estas fechas son estimadas usando promedios fisiológicos (desarrollo floral rápido).
                                    Pueden cambiar por clima, lluvia, estrés hídrico, etc.
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- ====== /BLOQUE NUEVO ====== -->

                </section>
            </div>
        </div>

        <!-- ===== TAB: Catálogo de Flora (NUEVO) ====== -->
        <div class="tab-content" data-tab="catalogo">
            <div class="section-header" style="margin-top:8px;">
                <h2 class="section-title">
                    <i class="fa-solid fa-seedling"></i>
                    Catálogo de Flora del Territorio
                </h2>
                <div style="margin-left:auto; display:flex; gap:8px; align-items:center;">
                    <input id="cat-search" type="text" placeholder="Buscar especie..." 
                           style="border:1px solid #e5e7eb;border-radius:8px;padding:8px 10px;min-width:220px;">
                </div>
            </div>

            <div id="catalogo-grid" class="catalogo-grid">
                {{-- JS pinta las tarjetas --}}
            </div>
        </div>

        <!-- ===== TAB: Predicción de Rendimiento (NUEVO COMPLETO) ===== -->
        <div class="tab-content" data-tab="rendimiento">
            <div class="section-header" style="margin-top:8px;">
                <h2 class="section-title">
                    <i class="fa-solid fa-chart-line"></i>
                    Predicción de Rendimiento de Miel
                </h2>
            </div>

            <div class="rend-grid">
                <!-- Panel izquierdo: tabla histórica -->
                <div class="rend-left">
                    <div class="rend-box">
                        <div class="rend-box-head">
                            <div class="rb-title">Ingresa el rendimiento obtenido en temporadas pasadas (kg/colmena)</div>
                        </div>
                        <table class="rend-table" id="rend-hist-table">
                            <thead>
                                <tr>
                                    <th>Temporada</th>
                                    <th>Kg/colmena</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2021/2022</td>
                                    <td><input type="number" step="0.1" class="rend-input" value="27"></td>
                                </tr>
                                <tr>
                                    <td>2022/2023</td>
                                    <td><input type="number" step="0.1" class="rend-input" value="20"></td>
                                </tr>
                                <tr>
                                    <td>2023/2024</td>
                                    <td><input type="number" step="0.1" class="rend-input" value="20"></td>
                                </tr>
                                <tr>
                                    <td>2024/2025</td>
                                    <td><input type="number" step="0.1" class="rend-input" value="15"></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="rend-actions">
                            <button id="btn-calc-rend" class="btn btn-primary">
                                <i class="fa-solid fa-calculator"></i> Calcular Rendimiento
                            </button>
                            <div class="rend-note">Tip: deja en blanco temporadas sin datos.</div>
                        </div>
                    </div>

                    <div class="rend-factors">
                        <div class="rf-title">Factores Influyentes (aplicados a la proyección)</div>
                        <ul class="rf-list">
                            <li><span>Clima favorable</span> <b id="rf-clima">+15%</b></li>
                            <li><span>Diversidad floral</span> <b id="rf-div">+10%</b></li>
                            <li><span>Riesgo de sequía</span> <b id="rf-sequia">-5%</b></li>
                        </ul>
                        <div class="rf-total">Ajuste neto: <b id="rf-net">+20%</b></div>
                    </div>
                </div>

                <!-- Panel derecho: cards + mapa -->
                <div class="rend-right">
                    <div class="rend-kpis">
                        <div class="rk-card">
                            <div class="rk-icon"><i class="fa-solid fa-box-open"></i></div>
                            <div class="rk-body">
                                <div class="rk-label">Rendimiento Estimado — Temporada Actual</div>
                                <div class="rk-value" id="rk-kg-col">0.0 kg</div>
                                <div class="rk-sub">Por colmena (proyección)</div>
                            </div>
                        </div>
                        <div class="rk-card">
                            <div class="rk-icon"><i class="fa-solid fa-scale-balanced"></i></div>
                            <div class="rk-body">
                                <div class="rk-value" id="rk-kg-apiario">0</div>
                                <div class="rk-sub">kg/apiario</div>
                            </div>
                        </div>
                        <div class="rk-card">
                            <div class="rk-icon"><i class="fa-solid fa-drum"></i></div>
                            <div class="rk-body">
                                <div class="rk-value" id="rk-tambores">0.0</div>
                                <div class="rk-sub">Tambores</div>
                            </div>
                        </div>
                    </div>

                    <div class="rend-map-wrap">
                        <div class="section-header" style="margin:0 0 8px;">
                            <h3 class="section-title" style="font-size:1.05rem;">
                                <i class="fa-solid fa-map-location-dot"></i> Mapa del Apiario
                            </h3>
                        </div>
                        <div id="map-rend" style="height:360px; border-radius:12px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- placeholders del resto de tabs -->
        <div class="tab-content" data-tab="historico"></div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
<link rel="stylesheet" href="{{ asset('css/components/home-user/zonificacion.css') }}">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- ====== ESTILOS LIGEROS PARA LA FILA Y EL PRONÓSTICO ====== -->
<style>
/* Métricas en fila */
.weather-row{ display:flex; gap:14px; flex-wrap:wrap; }
.weather-row .stat-card.weather{ flex: 1 1 calc(20% - 14px); min-width: 180px; }

/* Carrusel horizontal sutil para pronóstico */
.forecast-scroll{
    display:flex; gap:14px; overflow-x:auto; padding: 8px 2px 4px;
    scroll-snap-type: x proximity;
}
.forecast-card{
    scroll-snap-align: start; flex: 0 0 200px; border-radius: 14px; padding: 14px;
    background: linear-gradient(180deg, #ffffff 0%, #fff7ec 100%);
    border: 1px solid #f2e4d0; box-shadow: 0 6px 16px rgba(0,0,0,.06);
}
.forecast-card .fc-top{ display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; font-weight:600; }
.forecast-card .fc-sub{ opacity:.75; font-size:.85rem; margin-bottom:10px; }
.forecast-card .fc-icon{ font-size:1.8rem; margin:6px 0; }
.temp-row{ display:flex; align-items:center; gap:8px; margin:8px 0 4px; }
.temp-chip{ padding:4px 8px; border-radius:999px; font-size:.9rem; font-weight:600; background:#fff; border:1px solid #f0d8b7; }
.temp-chip .min{opacity:.8} .temp-chip .max{opacity:.95}
.hum-chip{ display:inline-flex; align-items:center; gap:6px; background:#fff; border:1px dashed #f0d8b7; padding:4px 8px; border-radius:10px; font-size:.9rem; opacity:.9 }

/* ====== Estilos Calendario de Floración ====== */
.pf-layout{ display:grid; grid-template-columns: 280px 1fr; gap:18px; margin-top:8px; }
.pf-sidebar{ background:#fff; border:1px solid #f0e1c9; border-radius:14px; padding:12px; box-shadow:0 6px 16px rgba(0,0,0,.06); }
.pf-side-title{ font-weight:700; margin-bottom:8px; }
.pf-species-list{ list-style:none; margin:0; padding:0; display:grid; gap:6px; max-height: 360px; overflow:auto; }
.pf-species-list li{ display:flex; align-items:center; gap:8px; padding:8px 10px; border:1px solid #f5e6cf; border-radius:10px; cursor:pointer; background:#fff; }
.pf-species-list li.active{ border-color:#f4a53a; background:#fff9f0; box-shadow:0 4px 12px rgba(244,165,58,.15); }
.pf-species-list input{ margin-right:4px; }
.pf-hint{ margin-top:10px; font-size:.9rem; opacity:.8 }

.pf-grid{ background:#fff; border:1px solid #f0e1c9; border-radius:14px; padding:12px; box-shadow:0 6px 16px rgba(0,0,0,.06); }
.pf-grid-head, .pf-grid-foot{ display:grid; grid-template-columns: repeat(4, 1fr); gap:12px; align-items:center; }
.pf-col-head, .pf-col-foot{ background:#fff8ed; border:1px dashed #f0d8b7; border-radius:10px; padding:10px; text-align:center; font-weight:600; }
.pf-col-title{ font-size:1rem; }
.pf-grid-body{ display:grid; grid-template-columns: repeat(4, 1fr); gap:12px; margin:12px 0; }
.pf-cell{ height:210px; border-radius:12px; overflow:hidden; border:1px solid #f2e4d0; background:#fff; }
.pf-img{ width:100%; height:100%; object-fit:cover; display:block; }
.pf-placeholder{ display:flex; align-items:center; justify-content:center; height:100%; color:#999;
    background: repeating-linear-gradient(45deg,#faf5ee,#faf5ee 10px,#fff 10px,#fff 20px); }
.pf-placeholder i{ font-size:2rem; opacity:.6 }
.pf-foot-note{ display:inline-block; padding:4px 8px; border-radius:999px; border:1px solid #f0d8b7; background:#fff; }
.pf-green{color:#1e8449} .pf-red{color:#c0392b}
.pf-title{ margin-left:8px; font-weight:700; color:#444 }

@media (max-width: 1024px){ .pf-layout{grid-template-columns: 1fr} }

/* ====== Catálogo de Flora ====== */
.catalogo-grid{
    display:grid;
    grid-template-columns: repeat(12, 1fr);
    gap:16px;
}
@media (max-width: 1400px){ .catalogo-grid{ grid-template-columns: repeat(9, 1fr);} }
@media (max-width: 1100px){ .catalogo-grid{ grid-template-columns: repeat(6, 1fr);} }
@media (max-width: 820px){ .catalogo-grid{ grid-template-columns: repeat(4, 1fr);} }
@media (max-width: 640px){ .catalogo-grid{ grid-template-columns: repeat(2, 1fr);} }

.cat-card{
    grid-column: span 3 / span 3;
    background:#fff; border:1px solid #f0e1c9; border-radius:14px;
    box-shadow: 0 6px 16px rgba(0,0,0,.06); overflow:hidden;
    display:flex; flex-direction:column;
}
.cat-cover{ width:100%; height:160px; object-fit:cover; background:#faf5ee; }
.cat-body{ padding:12px; display:flex; flex-direction:column; gap:6px; }
.cat-title{ font-weight:700; color:#333; line-height:1.2; }
.cat-sub{ color:#666; font-size:.9rem; }
.cat-meta{ color:#444; font-size:.9rem; }
.cat-actions{ display:flex; gap:8px; margin-top:8px; }
.btn-soft{
    border:1px solid #f0d8b7; background:#fff; padding:8px 10px; border-radius:10px; cursor:pointer; font-weight:600;
}
.btn-soft:hover{ background:#fff9f0; }

/* Modal (lectura) — (se mantiene por compatibilidad, pero no se usa para Catálogo) */
.modal-backdrop{
    position:fixed; inset:0; background:rgba(0,0,0,.3); display:none; align-items:center; justify-content:center; z-index:60;
}
.modal{ width:min(900px, 92vw); background:#fff; border-radius:14px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.25); }
.modal-head{ padding:14px 16px; border-bottom:1px solid #eee; display:flex; align-items:center; justify-content:space-between; }
.modal-body{ padding:16px; display:grid; grid-template-columns: 320px 1fr; gap:16px; }
@media (max-width: 820px){ .modal-body{ grid-template-columns: 1fr; } }
.modal-cover{ width:100%; height:240px; object-fit:cover; border-radius:10px; background:#faf5ee; }
.modal-close{ border:none; background:transparent; font-size:1.2rem; cursor:pointer; }
.modal-meta b{ color:#333; }

/* ====== Modal (edición de perfil) ====== */
.modal-edit-backdrop{ position:fixed; inset:0; background:rgba(0,0,0,.4); display:none; align-items:center; justify-content:center; z-index:70; }
.modal-edit{ width:min(980px, 95vw); background:#fff; border-radius:14px; overflow:hidden; box-shadow:0 24px 64px rgba(0,0,0,.28); }
.modal-edit-head{ padding:14px 16px; border-bottom:1px solid #eee; display:flex; align-items:center; justify-content:space-between; }
.modal-edit-body{ padding:16px; display:grid; grid-template-columns: 360px 1fr; gap:16px; }
@media (max-width: 900px){ .modal-edit-body{ grid-template-columns: 1fr; } }
.modal-edit-cover{ width:100%; height:260px; object-fit:cover; border-radius:12px; background:#faf5ee; }
.form-grid{ display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
@media (max-width: 700px){ .form-grid{ grid-template-columns: 1fr; } }
.form-control{ width:100%; border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; font-size:.95rem; outline:none; }
.form-label{ font-size:.9rem; color:#444; font-weight:600; margin-bottom:6px; display:block; }
.textarea{ min-height:96px; resize:vertical; }
.modal-edit-foot{ padding:12px 16px; border-top:1px solid #eee; display:flex; align-items:center; justify-content:flex-end; gap:8px; }
.btn{ border:1px solid #e5e7eb; padding:8px 12px; border-radius:10px; background:#fff; cursor:pointer; font-weight:600; }
.btn-primary{ background:#f59e0b; border-color:#f59e0b; color:#111; }
.btn-primary:hover{ filter:brightness(.95); }
.btn-danger{ background:#ef4444; border-color:#ef4444; color:#fff; }
.badge-soft{ display:inline-block; padding:4px 8px; border:1px solid #f0d8b7; border-radius:999px; background:#fff; font-size:.8rem; }
.alert-soft{ padding:8px 12px; border-radius:10px; border:1px dashed #f0d8b7; background:#fffaf3; font-size:.9rem; }

/* ===== MODAL NEUTRO exclusivo del Catálogo (evita conflictos) ===== */
.flora-backdrop{
    position:fixed; inset:0;
    background:rgba(0,0,0,.35);
    display:none;                 /* se activa con display:flex */
    align-items:center; justify-content:center;
    z-index:3000;                 /* sobre todo */
}
.flora-modal{
    width:min(900px, 92vw);
    background:#fff;
    border-radius:14px;
    overflow:hidden;
    box-shadow:0 20px 60px rgba(0,0,0,.25);
    position:relative;
}
.flora-modal-head{
    padding:14px 16px; border-bottom:1px solid #eee;
    display:flex; align-items:center; justify-content:space-between;
}
.flora-modal-body{
    padding:16px; display:grid; grid-template-columns: 320px 1fr; gap:16px;
}
@media (max-width: 820px){ .flora-modal-body{ grid-template-columns: 1fr; } }
.flora-modal-cover{ width:100%; height:240px; object-fit:cover; border-radius:10px; background:#faf5ee; }
.flora-modal-close{ border:none; background:transparent; font-size:1.2rem; cursor:pointer; }
.flora-modal-meta b{ color:#333; }
/* evita scroll del fondo con modal abierto */
.no-scroll{ overflow:hidden; }

/* ====== Predicción de Rendimiento (estilos nuevos) ====== */
.rend-grid{ display:grid; grid-template-columns: 420px 1fr; gap:18px; }
@media (max-width: 1100px){ .rend-grid{ grid-template-columns: 1fr; } }

.rend-left{ display:flex; flex-direction:column; gap:14px; }
.rend-right{ display:flex; flex-direction:column; gap:14px; }

.rend-box{ background:#fff; border:1px solid #f0e1c9; border-radius:14px; box-shadow:0 6px 16px rgba(0,0,0,.06); overflow:hidden; }
.rend-box-head{ padding:12px 14px; border-bottom:1px solid #f5e6cf; background:#fffaf3; }
.rb-title{ font-weight:700; color:#333; }

.rend-table{ width:100%; border-collapse:separate; border-spacing:0; }
.rend-table th, .rend-table td{ padding:10px 12px; border-bottom:1px solid #f5e6cf; }
.rend-table thead th{ background:#fff7ea; font-weight:700; }
.rend-table tbody tr:nth-child(odd){ background:#fff; }
.rend-table tbody tr:nth-child(even){ background:#fffdf8; }
.rend-input{ width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:8px 10px; }

.rend-actions{ display:flex; align-items:center; gap:10px; padding:12px 14px; }
.rend-note{ font-size:.85rem; color:#777; }

.rend-factors{ background:#fff; border:1px dashed #f0d8b7; border-radius:14px; padding:12px; }
.rf-title{ font-weight:700; color:#333; margin-bottom:6px; }
.rf-list{ list-style:none; margin:0; padding:0; display:grid; gap:6px; }
.rf-list li{ display:flex; align-items:center; justify-content:space-between; padding:8px 10px; border:1px solid #f5e6cf; border-radius:10px; background:#fff; }
.rf-total{ margin-top:8px; font-weight:700; }

.rend-kpis{ display:grid; grid-template-columns: repeat(3, 1fr); gap:12px; }
@media (max-width: 700px){ .rend-kpis{ grid-template-columns: 1fr; } }
.rk-card{ display:flex; gap:12px; align-items:center; background:#fff; border:1px solid #f0e1c9; border-radius:14px; padding:12px; box-shadow:0 6px 16px rgba(0,0,0,.06); }
.rk-icon{ font-size:1.6rem; color:#d97706; }
.rk-label{ font-size:.9rem; color:#555; font-weight:600; }
.rk-value{ font-size:1.6rem; font-weight:800; color:#1f2937; line-height:1; }
.rk-sub{ font-size:.85rem; color:#6b7280; }

.rend-map-wrap{ background:#fff; border:1px solid #f0e1c9; border-radius:14px; padding:10px; box-shadow:0 6px 16px rgba(0,0,0,.06); }
</style>

<script>
// ==================== TABS ====================
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.tab-btn');
    if (!btn) return;
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    btn.classList.add('active');
    const tab = btn.dataset.tab;
    const content = document.querySelector('.tab-content[data-tab="'+tab+'"]');
    if (content) content.classList.add('active');

    // Lazy init del mapa de rendimiento cuando se abre esa pestaña
    if (tab === 'rendimiento') {
        if (!window.__rendMapInited) {
            initRendimientoTab();
            window.__rendMapInited = true;
        }
    }
});

// ====== ICONOS/ETIQUETAS CLIMA ======
const weatherEs = {
    Clear:"Despejado", Clouds:"Nublado", Rain:"Lluvioso", Snow:"Nevado", Fog:"Neblina",
    Thunderstorm:"Tormenta", Drizzle:"Llovizna", Mist:"Neblina", Haze:"Neblina",
    Smoke:"Humo", Dust:"Polvo", Sand:"Arena", Ash:"Ceniza", Squall:"Chubasco", Tornado:"Tornado"
};
const weatherIcons = {
    "01d":"<i class='fa-solid fa-sun'></i>","01n":"<i class='fa-solid fa-moon'></i>",
    "02d":"<i class='fa-solid fa-cloud-sun'></i>","02n":"<i class='fa-solid fa-cloud-moon'></i>",
    "03d":"<i class='fa-solid fa-cloud'></i>","03n":"<i class='fa-solid fa-cloud'></i>",
    "04d":"<i class='fa-solid fa-cloud'></i>","04n":"<i class='fa-solid fa-cloud'></i>",
    "09d":"<i class='fa-solid fa-cloud-rain'></i>","09n":"<i class='fa-solid fa-cloud-rain'></i>",
    "10d":"<i class='fa-solid fa-cloud-sun-rain'></i>","10n":"<i class='fa-solid fa-cloud-moon-rain'></i>",
    "11d":"<i class='fa-solid fa-bolt'></i>","11n":"<i class='fa-solid fa-bolt'></i>",
    "13d":"<i class='fa-solid fa-snowflake'></i>","13n":"<i class='fa-solid fa-snowflake'></i>",
    "50d":"<i class='fa-solid fa-smog'></i>","50n":"<i class='fa-solid fa-smog'></i>"
};

// ==================== MAPA + CLIMA OPENWEATHER ====================
(function initMapAndWeather() {
    const rawLat = @json($apiario->latitud);
    const rawLon = @json($apiario->longitud);
    const apiKey = 'e7898e26c93386e793bebfc5b7ead995';

    const lat = Number(rawLat);
    const lon = Number(rawLon);
    const hasCoords = Number.isFinite(lat) && Number.isFinite(lon);

    // --- Mapa con capas y preferencia persistente ---
    const map = L.map('map', { zoomControl: true });
    const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 40, attribution: '&copy; OpenStreetMap'
    });
    const esriSat = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}');
    const preferredLayer = localStorage.getItem('mapLayer') || 'satellite';
    (preferredLayer === 'satellite' ? esriSat : osm).addTo(map);
    L.control.layers({ 'Mapa Base (OSM)': osm, 'Satélite (Esri)': esriSat }, null, { position:'topright'}).addTo(map);
    map.on('baselayerchange', e => localStorage.setItem('mapLayer', e.name === 'Satélite (Esri)' ? 'satellite' : 'osm'));

    const beeIcon = L.icon({ iconUrl: '/img/apiario.webp', iconSize: [38,38], iconAnchor: [20,20], popupAnchor: [0,-32] });

    let marker, popupEl;

    if (hasCoords) {
        marker = L.marker([lat, lon], { icon: beeIcon }).addTo(map);
        marker.bindPopup(`
            <div class="popup-wrap" style="min-width:200px">
                <strong>{{ $apiario->nombre }}</strong><br>
                <i class="fa-solid fa-border-all" style="margin-right:6px"></i>
                Colmenas: {{ $apiario->num_colmenas ?? '—' }}<br>
                <i class="fa-solid fa-location-dot" style="margin-right:6px"></i>
                ${lat.toFixed(6)}, ${lon.toFixed(6)}<br>
                <span id="popup-weather" style="display:inline-block;margin-top:6px;">Cargando clima…</span>
            </div>
        `);
        map.setView([lat, lon], 15);
        marker.openPopup();
        marker.on('popupopen', () => popupEl = document.getElementById('popup-weather'));
    } else {
        map.setView([-33.45, -70.6667], 6);
        const warn = L.control({position: 'topright'});
        warn.onAdd = function () {
            const div = L.DomUtil.create('div', 'leaflet-bar');
            div.style.padding = '10px'; div.style.background = 'white';
            div.style.borderRadius = '8px'; div.style.boxShadow = '0 2px 8px rgba(0,0,0,.15)';
            div.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="color:#e67e22;margin-right:6px"></i> Apiario sin ubicación';
            return div;
        };
        warn.addTo(map);
    }

    // --- Helpers clima ---
    const el = (id)=>document.getElementById(id);
    function setNow(temp, hum, rain, uv, press, icon, main) {
        if (el('w-temp'))     el('w-temp').innerHTML     = `${temp.toFixed(1)}°C`;
        if (el('w-humidity')) el('w-humidity').innerHTML = `${hum}%`;
        if (el('w-rain'))     el('w-rain').innerHTML     = typeof rain === 'number' ? `${rain.toFixed(1)} mm` : '0 mm';
        if (el('w-uv'))       el('w-uv').innerHTML       = typeof uv === 'number' ? uv.toFixed(1) : '—';
        if (el('w-pressure')) el('w-pressure').innerHTML = `${press} hPa`;
        if (popupEl) popupEl.innerHTML = `${weatherIcons[icon]||''} ${weatherEs[main]||main} • ${temp.toFixed(1)}°C, ${hum}%`;
    }

    function cardHtml(dow, dateStr, iconHtml, desc, tmin, tmax, hum){
        return `
        <div class="forecast-card">
            <div class="fc-top">
                <span>${dow}</span>
                <span class="fc-sub">${dateStr}</span>
            </div>
            <div class="fc-icon">${iconHtml}</div>
            <div class="fc-sub">${desc}</div>
            <div class="temp-row">
                <span class="temp-chip"><i class="fa-solid fa-temperature-low"></i> <span class="min">${Math.round(tmin)}°</span></span>
                <span class="temp-chip"><i class="fa-solid fa-temperature-high"></i> <span class="max">${Math.round(tmax)}°</span></span>
            </div>
            <div class="hum-chip"><i class="fa-solid fa-droplet"></i> ${hum}%</div>
        </div>`;
    }

    function renderForecast(days){
        const cont = document.getElementById('forecast-6');
        if (!cont) return;
        cont.innerHTML = '';
        const fmt = new Intl.DateTimeFormat('es-CL',{weekday:'long', day:'2-digit', month:'2-digit'});
        days.slice(0,6).forEach(d=>{
            const dt = new Date(d.dt*1000);
            const parts = fmt.formatToParts(dt);
            const dow = parts.find(p=>p.type==='weekday').value;
            const dateStr = `${parts.find(p=>p.type==='day').value}/${parts.find(p=>p.type==='month').value}`;
            const iconHtml = weatherIcons[d.weather[0].icon] || '';
            const desc = weatherEs[d.weather[0].main] || d.weather[0].main;
            const tmin = d.temp?.min ?? d.main?.temp_min ?? d.temp?.day ?? 0;
            const tmax = d.temp?.max ?? d.main?.temp_max ?? d.temp?.day ?? 0;
            const hum  = d.humidity ?? d.main?.humidity ?? 0;
            cont.insertAdjacentHTML('beforeend', cardHtml(
                dow.charAt(0).toUpperCase()+dow.slice(1),
                dateStr, iconHtml, desc, tmin, tmax, hum
            ));
        });
    }

    // --- Fetch clima ---
    async function loadWeather(){
        if(!hasCoords) return;

        try{
            const r = await fetch(`https://api.openweathermap.org/data/2.5/onecall?lat=${lat}&lon=${lon}&units=metric&lang=es&exclude=minutely,hourly,alerts&appid=${apiKey}`);
            if(!r.ok) throw new Error('onecall not ok');
            const j = await r.json();
            const cur = j.current;
            const daily = j.daily || [];
            setNow(cur.temp, cur.humidity, (cur.rain?.['1h'] ?? 0), daily?.[0]?.uvi ?? cur.uvi, cur.pressure, cur.weather?.[0]?.icon, cur.weather?.[0]?.main);
            renderForecast(daily);
            return;
        }catch(_e){/* fallback */ }

        try{
            const [rc, rf] = await Promise.all([
                fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&units=metric&lang=es&appid=${apiKey}`),
                fetch(`https://api.openweathermap.org/data/2.5/forecast?lat=${lat}&lon=${lon}&units=metric&lang=es&appid=${apiKey}`)
            ]);
            const jc = await rc.json();
            const jf = await rf.json();
            setNow(jc.main.temp, jc.main.humidity, (jc.rain?.['1h'] ?? jc.rain?.['3h'] ?? 0), null, jc.main.pressure, jc.weather?.[0]?.icon, jc.weather?.[0]?.main);

            const byDay = {};
            (jf.list||[]).forEach(it=>{
                const d = new Date(it.dt*1000);
                const key = new Date(d.getFullYear(), d.getMonth(), d.getDate()).getTime()/1000;
                if(!byDay[key]){
                    byDay[key] = {dt:key, temp:{min: it.main.temp_min, max: it.main.temp_max, day: it.main.temp}, humidity: it.main.humidity, weather: [it.weather[0]]};
                }else{
                    byDay[key].temp.min = Math.min(byDay[key].temp.min, it.main.temp_min);
                    byDay[key].temp.max = Math.max(byDay[key].temp.max, it.main.temp_max);
                    byDay[key].humidity = Math.round((byDay[key].humidity + it.main.humidity)/2);
                }
            });
            const days = Object.values(byDay).sort((a,b)=>a.dt-b.dt);
            renderForecast(days.slice(0,6));
        }catch(e){
            console.error('Weather error', e);
            const popupSpan = document.getElementById('popup-weather');
            if (popupSpan) popupSpan.textContent = 'No se pudo cargar el clima';
        }
    }

    loadWeather();
})();

// ==================== JS Calendario de Floraciones ====================

// Datos desde el controlador (ya compactados)
const PF_FASES   = @json($fenofases->map(fn($f)=>['clave'=>$f->clave,'nombre'=>$f->nombre]));
const PF_ESPECES = @json($especies->map(fn($e)=>['id'=>$e->id,'nombre'=>$e->nombre,'slug'=>$e->slug]));
const PF_IMGS    = @json($imgByFlorAndFase); // [flor_id][fenofase_clave] => path relativo
// NUEVO: duraciones desde la BD: [flor_id][fase_clave] => offset_dias
const DUR_BY_FLOR_FASE = @json($durByFlorFase ?? []);
// NUEVO: perfiles informativos para catálogo (puede venir vacío)
const PROFILES_BY_ID = @json($perfilesByFlorId ?? []);  // ← FIX aquí

let CURRENT_FLOR_ID = null; // especie seleccionada actualmente

(function initFloraciones(){
    const listEl  = document.getElementById('pf-species-list');
    const gridEl  = document.getElementById('pf-grid-body');
    const titleEl = document.getElementById('pf-especie-titulo');

    if(!listEl || !gridEl) return;

    // Render lista de especies
    PF_ESPECES.forEach((esp, idx)=>{
        const li = document.createElement('li');
        li.innerHTML = `
            <label style="cursor:pointer;display:flex;align-items:center;gap:8px;width:100%;">
                <input type="radio" name="pf-species" value="${esp.id}" ${idx===0?'checked':''}>
                <span>${esp.nombre}</span>
            </label>
        `;
        if(idx===0) li.classList.add('active');
        listEl.appendChild(li);
    });

    function setOffsetsChips(specId){
        const offs = getOffsetsForSpecies(specId);
        const chipI = document.getElementById('off-inicio-chip');
        const chipP = document.getElementById('off-plena-chip');
        const chipT = document.getElementById('off-terminal-chip');
        if (chipI) chipI.textContent = `Inicio: +${offs.inicio} días`;
        if (chipP) chipP.textContent = `Plena: +${offs.plena} días`;
        if (chipT) chipT.textContent = `Terminal: +${offs.terminal} días`;
    }

    function setActive(id){
        CURRENT_FLOR_ID = Number(id);

        // marcar activa en la lista
        listEl.querySelectorAll('li').forEach(li=>li.classList.remove('active'));
        const li = listEl.querySelector(`input[value="${id}"]`)?.closest('li');
        if(li) li.classList.add('active');

        // título
        const esp = PF_ESPECES.find(e=>e.id==id);
        if (titleEl) titleEl.textContent = esp ? `— ${esp.nombre}` : '';

        // pintar las 4 celdas
        gridEl.innerHTML = '';
        PF_FASES.forEach(f=>{
            const path = (PF_IMGS[id] && PF_IMGS[id][f.clave]) ? PF_IMGS[id][f.clave] : null;
            const cell = document.createElement('div');
            cell.className = 'pf-cell';
            if(path){
                cell.innerHTML = `
                    <img class="pf-img" src="{{ asset('storage') }}/${path}" alt="${esp?.nombre || ''} - ${f.nombre}">
                `;
            }else{
                cell.innerHTML = `<div class="pf-placeholder"><i class="fa-regular fa-image"></i></div>`;
            }
            gridEl.appendChild(cell);
        });

        // actualizar chips de offsets + recalcular si ya hay fecha base
        setOffsetsChips(CURRENT_FLOR_ID);
        const baseInput = document.getElementById('fecha-boton');
        if (baseInput && baseInput.value) {
            calcularFechasFenologicas(baseInput.value);
        }
    }

    // Primera selección al cargar
    if(PF_ESPECES.length){ setActive(PF_ESPECES[0].id); }

    // Listeners
    listEl.addEventListener('change', (e)=>{
        if(e.target && e.target.name==='pf-species'){
            setActive(e.target.value);
        }
    });
})();

// ==================== LÍNEA DE TIEMPO FENOLÓGICA ====================

// Fallback por si alguna especie no tiene registro
const PHASE_OFFSETS_DEFAULT = { inicio: 7, plena: 14, terminal: 21 };

function getOffsetsForSpecies(specId){
    const o = DUR_BY_FLOR_FASE?.[specId] || {};
    return {
        inicio:   Number.isFinite(+o?.inicio)   ? +o.inicio   : PHASE_OFFSETS_DEFAULT.inicio,
        plena:    Number.isFinite(+o?.plena)    ? +o.plena    : PHASE_OFFSETS_DEFAULT.plena,
        terminal: Number.isFinite(+o?.terminal) ? +o.terminal : PHASE_OFFSETS_DEFAULT.terminal
    };
}

// Utilidad para formatear fecha a DD/MM/YYYY
function formatDateForDisplay(dateObj) {
    if (!dateObj || isNaN(dateObj.getTime())) return "—";
    const dd = String(dateObj.getDate()).padStart(2, "0");
    const mm = String(dateObj.getMonth() + 1).padStart(2, "0");
    const yyyy = dateObj.getFullYear();
    return `${dd}/${mm}/${yyyy}`;
}

// Calcula y muestra fechas estimadas usando offsets de la especie actual
function calcularFechasFenologicas(fechaBaseStr) {
    const outInicio   = document.getElementById('fecha-inicio');
    const outPlena    = document.getElementById('fecha-plena');
    const outTerminal = document.getElementById('fecha-terminal');

    if (!fechaBaseStr) {
        if(outInicio) outInicio.textContent = "—";
        if(outPlena) outPlena.textContent = "—";
        if(outTerminal) outTerminal.textContent = "—";
        return;
    }

    const base = new Date(fechaBaseStr + "T00:00:00");
    if (isNaN(base.getTime())) {
        if(outInicio) outInicio.textContent = "—";
        if(outPlena) outPlena.textContent = "—";
        if(outTerminal) outTerminal.textContent = "—";
        return;
    }

    const offs = getOffsetsForSpecies(CURRENT_FLOR_ID || 0);

    const inicio   = new Date(base); inicio.setDate(inicio.getDate() + offs.inicio);
    const plena    = new Date(base); plena.setDate(plena.getDate() + offs.plena);
    const terminal = new Date(base); terminal.setDate(terminal.getDate() + offs.terminal);

    if(outInicio)   outInicio.textContent   = formatDateForDisplay(inicio);
    if(outPlena)    outPlena.textContent    = formatDateForDisplay(plena);
    if(outTerminal) outTerminal.textContent = formatDateForDisplay(terminal);
}

// cuando el usuario elige la fecha de "Botón floral"
document.addEventListener('change', (e) => {
    if (e.target && e.target.id === 'fecha-boton') {
        calcularFechasFenologicas(e.target.value);
    }
});

// ==================== BLOQUE: Agroclima (simulado) ====================
const apiarioLat = @json($apiario->latitud);
const apiarioLon = @json($apiario->longitud);

async function fetchAgroData(lat, lon) {
    // FUTURO: fetch(`/api/agroclima?lat=${lat}&lon=${lon}`)
    const data = { temp_actual_c: 22.7, precipitacion_24h_mm: 0.4 };

    const tempEl = document.getElementById('agro-temp');
    const ppEl   = document.getElementById('agro-pp');

    if (tempEl) tempEl.textContent = (typeof data.temp_actual_c !== "undefined") ? data.temp_actual_c.toFixed(1) : "—";
    if (ppEl)   ppEl.textContent   = (typeof data.precipitacion_24h_mm !== "undefined") ? data.precipitacion_24h_mm.toFixed(1) : "—";
}

document.addEventListener('DOMContentLoaded', () => {
    if (apiarioLat && apiarioLon) fetchAgroData(apiarioLat, apiarioLon);
});


/* ==================== CATÁLOGO DE FLORA (cards + modal) ==================== */

const CAT_ESPECIES = PF_ESPECES;              // id, nombre, slug
const CAT_IMGS = PF_IMGS;                     // respaldo de imágenes por fenofase
const CAT_PROFILES = PROFILES_BY_ID || {};    // { flor_id: { nombre_cientifico, habitat, usos, descripcion, cover_path } }

function pickCoverFor(id){
    const prof = CAT_PROFILES[id];
    if (prof && prof.cover_path) return `{{ asset('storage') }}/${prof.cover_path}`;
    // fallback a imagen de 'plena' o 'inicio'
    const plena = CAT_IMGS?.[id]?.plena;
    const inicio = CAT_IMGS?.[id]?.inicio;
    const boton = CAT_IMGS?.[id]?.boton;
    const p = plena || inicio || boton;
    return p ? `{{ asset('storage') }}/${p}` : '';
}

function renderCatalog(list){
    const grid = document.getElementById('catalogo-grid');
    if(!grid) return;
    grid.innerHTML = '';
    list.forEach(e=>{
        const prof = CAT_PROFILES[e.id] || {};
        const cover = pickCoverFor(e.id);
        const cient = prof.nombre_cientifico || 'No disponible';
        const habitat = prof.habitat || 'No disponible';

        grid.insertAdjacentHTML('beforeend', `
            <div class="cat-card" data-id="${e.id}">
                ${cover ? `<img class="cat-cover" src="${cover}" alt="${e.nombre}">` : `<div class="cat-cover"></div>`}
                <div class="cat-body">
                    <div class="cat-title">${e.nombre}</div>
                    <div class="cat-sub"><em>${cient}</em></div>
                    <div class="cat-meta"><i class="fa-solid fa-mountain-sun" style="margin-right:6px"></i>${habitat}</div>
                    <div class="cat-actions">
                        <button class="btn-soft" data-cat="ver" data-id="${e.id}">
                            <i class="fa-regular fa-eye"></i> Ver más
                        </button>
                    </div>
                </div>
            </div>
        `);
    });
}

function filterCatalog(q){
    const term = (q||'').toLowerCase().trim();
    if(!term) return CAT_ESPECIES;
    return CAT_ESPECIES.filter(e=>{
        const prof = CAT_PROFILES[e.id] || {};
        return [e.nombre, prof.nombre_cientifico, prof.habitat, prof.usos]
            .filter(Boolean)
            .some(txt => String(txt).toLowerCase().includes(term));
    });
}

(function initCatalog(){
    // pintar inicial
    renderCatalog(CAT_ESPECIES);

    // search
    const search = document.getElementById('cat-search');
    if(search){
        search.addEventListener('input', (e)=>{
            renderCatalog(filterCatalog(e.target.value));
        });
    }

    // modal handlers
    document.addEventListener('click', (e)=>{
        const btn = e.target.closest('[data-cat="ver"]');
        if(!btn) return;
        const id = Number(btn.dataset.id);
        openCatalogModal(id);
    });
})();

/* ======= MODAL NEUTRO para la vista de Catálogo ======= */
function openCatalogModal(id){
    const esp = CAT_ESPECIES.find(x=>x.id==id);
    const prof = CAT_PROFILES[id] || {};
    const cover = pickCoverFor(id);
    const cient = prof.nombre_cientifico || 'No disponible';
    const habitat = prof.habitat || 'No disponible';
    const usos = prof.usos || 'No disponible';
    const desc = prof.descripcion || 'Aún no registras una descripción para esta especie.';

    let backdrop = document.getElementById('flora-modal-backdrop');
    if(!backdrop){
        document.body.insertAdjacentHTML('beforeend', `
            <div id="flora-modal-backdrop" class="flora-backdrop" role="dialog" aria-modal="true" aria-labelledby="flora-modal-title">
                <div class="flora-modal">
                    <div class="flora-modal-head">
                        <div style="font-weight:700;" id="flora-modal-title"></div>
                        <div style="display:flex; gap:8px; align-items:center;">
                            <button class="btn btn-primary" id="btn-open-edit" title="Editar perfil de flora">
                                <i class="fa-regular fa-pen-to-square"></i> Editar perfil
                            </button>
                            <button class="flora-modal-close" aria-label="Cerrar" onclick="closeCatalogModal()">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flora-modal-body">
                        <img id="flora-modal-cover" class="flora-modal-cover" alt="">
                        <div>
                            <div class="flora-modal-meta" style="margin-bottom:10px;">
                                <div><b>Nombre científico:</b> <span id="flora-modal-cient"></span></div>
                                <div><b>Hábitat:</b> <span id="flora-modal-habitat"></span></div>
                                <div><b>Usos:</b> <span id="flora-modal-usos"></span></div>
                            </div>
                            <div style="white-space:pre-line;" id="flora-modal-desc"></div>
                        </div>
                    </div>
                </div>
            </div>
        `);
        backdrop = document.getElementById('flora-modal-backdrop');

        // cerrar al clickear afuera
        backdrop.addEventListener('click', (ev)=>{
            if(ev.target === backdrop) closeCatalogModal();
        });

        // cerrar con ESC
        document.addEventListener('keydown', (ev)=>{
            if(ev.key === 'Escape' && backdrop.style.display === 'flex') closeCatalogModal();
        });
    }

    // set contenido
    document.getElementById('flora-modal-title').textContent = esp?.nombre || 'Especie';
    const img = document.getElementById('flora-modal-cover');
    if(cover){ img.src = cover; img.alt = esp?.nombre || ''; } else { img.removeAttribute('src'); img.alt=''; }
    document.getElementById('flora-modal-cient').textContent = cient;
    document.getElementById('flora-modal-habitat').textContent = habitat;
    document.getElementById('flora-modal-usos').textContent = usos;
    document.getElementById('flora-modal-desc').textContent = desc;

    // abrir editor con el ID actual
    const editBtn = document.getElementById('btn-open-edit');
    if (editBtn) {
        editBtn.onclick = () => openEditPerfilModal(id, esp?.nombre || 'Especie');
    }

    // mostrar
    backdrop.style.display = 'flex';
    document.body.classList.add('no-scroll');
}
function closeCatalogModal(){
    const backdrop = document.getElementById('flora-modal-backdrop');
    if(backdrop) backdrop.style.display = 'none';
    document.body.classList.remove('no-scroll');
}

/* ==================== NUEVO: Modal de EDICIÓN del Perfil de Flora ==================== */

function ensureEditModal(){
    let backdrop = document.getElementById('flora-edit-backdrop');
    if (backdrop) return backdrop;

    document.body.insertAdjacentHTML('beforeend', `
        <div id="flora-edit-backdrop" class="modal-edit-backdrop">
            <div class="modal-edit">
                <div class="modal-edit-head">
                    <div>
                        <div id="flora-edit-title" style="font-weight:700;"></div>
                        <div class="badge-soft" id="flora-edit-subtitle" style="margin-top:4px;"></div>
                    </div>
                    <div style="display:flex; gap:8px; align-items:center;">
                        <button class="btn" onclick="closeEditPerfilModal()" aria-label="Cerrar">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
                <div class="modal-edit-body">
                    <div>
                        <img id="flora-edit-cover" class="modal-edit-cover" alt="">
                        <div class="alert-soft" style="margin-top:10px;">
                            La imagen de portada se calcula con <em>cover_path</em> o, en su defecto, con la fenofase "plena".
                            Puedes cambiarla en el módulo de imágenes por fenofase.
                        </div>
                    </div>
                    <form id="flora-edit-form">
                        @csrf
                        <input type="hidden" id="flora-edit-id">
                        <div class="form-grid">
                            <div>
                                <label class="form-label" for="pe-nombre-comun-alt">Nombre común alternativo</label>
                                <input id="pe-nombre-comun-alt" name="nombre_comun_alt" type="text" class="form-control" maxlength="255">
                            </div>
                            <div>
                                <label class="form-label" for="pe-fuente">Fuente (texto)</label>
                                <input id="pe-fuente" name="fuente" type="text" class="form-control" maxlength="500">
                            </div>
                            <div>
                                <label class="form-label" for="pe-enlace">Enlace (URL)</label>
                                <input id="pe-enlace" name="enlace" type="url" class="form-control" maxlength="1000" placeholder="https://...">
                            </div>
                            <div>
                                <label class="form-label" for="pe-habitat">Hábitat</label>
                                <input id="pe-habitat" name="habitat" type="text" class="form-control" maxlength="2000">
                            </div>
                            <div>
                                <label class="form-label" for="pe-distribucion">Distribución</label>
                                <input id="pe-distribucion" name="distribucion" type="text" class="form-control" maxlength="2000">
                            </div>
                            <div>
                                <label class="form-label" for="pe-usos">Usos</label>
                                <input id="pe-usos" name="usos" type="text" class="form-control" maxlength="2000">
                            </div>
                            <div>
                                <label class="form-label" for="pe-nectar">Néctar (0–10)</label>
                                <input id="pe-nectar" name="nectar_score" type="number" min="0" max="10" class="form-control">
                            </div>
                            <div>
                                <label class="form-label" for="pe-polen">Polen (0–10)</label>
                                <input id="pe-polen" name="polen_score" type="number" min="0" max="10" class="form-control">
                            </div>
                            <div style="grid-column: 1 / -1;">
                                <label class="form-label" for="pe-resumen">Resumen</label>
                                <textarea id="pe-resumen" name="resumen" class="form-control textarea" maxlength="2000"></textarea>
                            </div>
                            <div style="grid-column: 1 / -1;">
                                <label class="form-label" for="pe-descripcion">Descripción</label>
                                <textarea id="pe-descripcion" name="descripcion" class="form-control textarea" maxlength="10000" rows="6"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-edit-foot">
                    <div id="flora-edit-feedback" class="badge-soft" style="margin-right:auto; display:none;"></div>
                    <button class="btn" onclick="closeEditPerfilModal()">Cancelar</button>
                    <button class="btn btn-primary" id="flora-edit-save">
                        <i class="fa-regular fa-floppy-disk"></i> Guardar cambios
                    </button>
                </div>
            </div>
        </div>
    `);
    return document.getElementById('flora-edit-backdrop');
}

function openEditPerfilModal(florId, nombreEspecie){
    const backdrop = ensureEditModal();
    // Título
    document.getElementById('flora-edit-title').textContent = `Editar perfil — ${nombreEspecie}`;
    document.getElementById('flora-edit-subtitle').textContent = `ID: ${florId}`;
    document.getElementById('flora-edit-id').value = florId;

    // Imagen de portada actual
    const cover = pickCoverFor(florId);
    const coverImg = document.getElementById('flora-edit-cover');
    if (cover) { coverImg.src = cover; coverImg.alt = nombreEspecie; } else { coverImg.removeAttribute('src'); coverImg.alt = ''; }

    // Limpiar feedback
    const fb = document.getElementById('flora-edit-feedback');
    fb.style.display = 'none'; fb.textContent = '';

    // Cargar datos actuales (GET /flora-perfil/{id})
    fetch(`/flora-perfil/${florId}`, { headers: { 'Accept':'application/json' } })
        .then(r => r.json())
        .then(json => {
            if (!json.ok) throw new Error(json.message || 'No se pudo cargar el perfil.');
            const perfil = json.data?.perfil || {};
            setEditValue('pe-nombre-comun-alt', perfil.nombre_comun_alt);
            setEditValue('pe-fuente', perfil.fuente);
            setEditValue('pe-enlace', perfil.enlace);
            setEditValue('pe-habitat', perfil.habitat);
            setEditValue('pe-distribucion', perfil.distribucion);
            setEditValue('pe-usos', perfil.usos);
            setEditValue('pe-nectar', perfil.nectar_score);
            setEditValue('pe-polen', perfil.polen_score);
            setEditValue('pe-resumen', perfil.resumen);
            setEditValue('pe-descripcion', perfil.descripcion);
        })
        .catch(err => {
            showEditFeedback('No se pudo cargar el perfil: ' + (err?.message || 'Error'));
        });

    // Guardar (POST /flora-perfil/{id})
    const saveBtn = document.getElementById('flora-edit-save');
    saveBtn.onclick = async () => {
        const form = document.getElementById('flora-edit-form');
        const fd = new FormData(form);
        try {
            const resp = await fetch(`/flora-perfil/${florId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept':'application/json'
                },
                body: fd
            });

            if (resp.status === 422) {
                const j = await resp.json();
                const first = Object.values(j.errors || {})[0]?.[0] || 'Revisa los campos.';
                showEditFeedback(first);
                return;
            }
            if (!resp.ok) throw new Error('No se pudo guardar la información.');

            const j = await resp.json();
            if (!j.ok) throw new Error(j.message || 'Error al guardar.');

            showEditFeedback('Guardado correctamente.', true);

            // Actualizar cache en memoria (CAT_PROFILES) para reflejar cambios en catálogo y modal de lectura
            const p = j.perfil || {};
            CAT_PROFILES[florId] = {
                ...(CAT_PROFILES[florId] || {}),
                nombre_cientifico: (CAT_PROFILES[florId]?.nombre_cientifico || p.nombre_cientifico || ''),
                habitat: p.habitat ?? CAT_PROFILES[florId]?.habitat,
                usos: p.usos ?? CAT_PROFILES[florId]?.usos,
                descripcion: p.descripcion ?? CAT_PROFILES[florId]?.descripcion,
                cover_path: CAT_PROFILES[florId]?.cover_path || null
            };

            // Refrescar grilla filtrada (si había búsqueda activa)
            const q = document.getElementById('cat-search')?.value || '';
            renderCatalog(filterCatalog(q));

            // Si el modal de lectura sigue abierto, refrescar sus campos
            const openRead = document.getElementById('flora-modal-backdrop');
            if (openRead && openRead.style.display === 'flex') {
                openCatalogModal(florId);
            }

        } catch (e) {
            showEditFeedback(e?.message || 'Error al guardar.');
        }
    };

    // Mostrar modal
    backdrop.style.display = 'flex';
}

function closeEditPerfilModal(){
    const backdrop = document.getElementById('flora-edit-backdrop');
    if (backdrop) backdrop.style.display = 'none';
}

function setEditValue(id, val){
    const el = document.getElementById(id);
    if (!el) return;
    el.value = (val ?? '');
}
function showEditFeedback(msg, ok=false){
    const fb = document.getElementById('flora-edit-feedback');
    fb.style.display = 'inline-block';
    fb.textContent = msg;
    fb.style.borderColor = ok ? '#bbf7d0' : '#f0d8b7';
    fb.style.background = ok ? '#ecfdf5' : '#fffaf3';
}

/* ==================== PESTAÑA: Predicción de Rendimiento ==================== */

function initRendimientoTab(){
    // Mapa pequeño
    const lat = Number(@json($apiario->latitud));
    const lon = Number(@json($apiario->longitud));
    const hasCoords = Number.isFinite(lat) && Number.isFinite(lon);
    const mapR = L.map('map-rend', { zoomControl:true });
    const sat = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}');
    const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'&copy; OpenStreetMap' });
    sat.addTo(mapR);
    L.control.layers({ 'Satélite': sat, 'OSM': osm }).addTo(mapR);

    const beeIcon = L.icon({ iconUrl:'/img/apiario.webp', iconSize:[32,32], iconAnchor:[16,16], popupAnchor:[0,-22] });
    if (hasCoords){
        L.marker([lat,lon],{icon:beeIcon})
            .addTo(mapR)
            .bindPopup(`<b>{{ $apiario->nombre }}</b><br>Colmenas: {{ $apiario->num_colmenas ?? '—' }}<br>${lat.toFixed(6)}, ${lon.toFixed(6)}`);
        mapR.setView([lat,lon], 14);
    }else{
        mapR.setView([-33.45, -70.6667], 6);
    }

    // Cálculo de proyección
    const BTN  = document.getElementById('btn-calc-rend');
    const inputs = Array.from(document.querySelectorAll('#rend-hist-table .rend-input'));

    // Factores (puedes ajustar estos % desde backend si quieres)
    const fClima  = +15;   // %
    const fFlora  = +10;   // %
    const fSequia =  -5;   // %
    const net     = fClima + fFlora + fSequia;

    document.getElementById('rf-clima').textContent  = `${fClima>0?'+':''}${fClima}%`;
    document.getElementById('rf-div').textContent    = `${fFlora>0?'+':''}${fFlora}%`;
    document.getElementById('rf-sequia').textContent = `${fSequia>0?'+':''}${fSequia}%`;
    document.getElementById('rf-net').textContent    = `${net>0?'+':''}${net}%`;

    function calc(){
        // promedio simple de temporadas con valor
        const vals = inputs.map(i => parseFloat(i.value)).filter(v => Number.isFinite(v) && v>=0);
        const base = vals.length ? (vals.reduce((a,b)=>a+b,0)/vals.length) : 0;
        const ajustado = base * (1 + (net/100)); // aplica factores

        const colmenas = Number(@json($apiario->num_colmenas ?? 0)) || 0;
        const kgApiario = ajustado * colmenas;
        const tambores = kgApiario / 300; // 1 tambor = 300 kg

        document.getElementById('rk-kg-col').textContent   = `${ajustado.toFixed(1)} kg`;
        document.getElementById('rk-kg-apiario').textContent = `${kgApiario.toLocaleString('es-CL',{maximumFractionDigits:0})}`;
        document.getElementById('rk-tambores').textContent = `${tambores.toFixed(1)}`;
    }

    BTN?.addEventListener('click', calc);
    // cálculo inicial por comodidad
    calc();
}
</script>
@endsection
