{{-- resources/views/hoja_ruta/catalogo_detalle.blade.php --}}
@extends('layouts.app')

@section('title', 'B-MaiA - Catálogo de Flora')

@section('content')
    @include('hoja_ruta.partials.subnav')

    @php
        // Decodificamos phenology si existe (JSON en la BD)
        // Formato sugerido:
        // {
        //   "months": ["ENE","FEB",...,"DIC"],
        //   "flowers": [...],
        //   "no_flowers": [...],
        //   "buds": [...],
        //   "fruits": [...],
        //   "no_notes": [...]
        // }
        $phenology = $species->phenology
            ? json_decode($species->phenology, true)
            : null;
    @endphp

    <style>
        .zonificacion-container {
            padding: 20px 24px 32px;
        }

        .catalogo-filtros-card {
            margin-top: 20px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
            padding: 14px 16px 18px;
            border: 1px solid #e5e7eb;
        }

        .catalogo-filtros-row {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .catalogo-filtros-row input[type="text"] {
            flex: 1 1 260px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            padding: 10px 12px;
            font-size: 14px;
        }

        .catalogo-select {
            min-width: 180px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
            font-size: 14px;
            background-color: #f9fafb;
        }

        .catalogo-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }

        .btn-reset-filtros {
            font-size: 13px;
            color: #2563eb;
            background: transparent;
            border: none;
            padding: 6px 10px;
            cursor: pointer;
        }

        .btn-ver-listado {
            background: #22c55e;
            color: #ffffff;
            border-radius: 999px;
            padding: 9px 18px;
            border: none;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
        }

        /* ====== Detalle de especie ====== */

        .species-detail-wrapper {
            margin-top: 24px;
            background: #ffffff;
            border-radius: 16px;
            padding: 22px 24px 28px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(0, 1.8fr);
            gap: 28px;
        }

        .species-name {
            font-size: 26px;
            font-weight: 700;
            color: #15803d;
            margin-bottom: 4px;
        }

        .species-scientific {
            font-size: 18px;
            font-style: italic;
            color: #111827;
        }

        .species-image-main {
            width: 100%;
            border-radius: 18px;
            object-fit: cover;
            max-height: 520px;
            display: block;
        }

        .species-image-placeholder {
            width: 100%;
            border-radius: 16px;
            border: 1px dashed #9ca3af;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            height: 260px;
            font-size: 14px;
        }

        .species-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
            margin-top: 6px;
            flex-wrap: wrap;
        }

        .species-tab-btn {
            border-radius: 999px;
            padding: 7px 16px;
            font-size: 13px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            cursor: pointer;
            transition: all .15s ease;
        }

        .species-tab-btn.active {
            background: #84cc16;
            border-color: #65a30d;
            color: #ffffff;
            font-weight: 600;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .species-section-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .species-text {
            font-size: 14px;
            color: #374151;
            line-height: 1.55;
        }

        .species-meta {
            margin-top: 18px;
            font-size: 13px;
            color: #4b5563;
        }

        .species-meta span {
            display: inline-block;
            margin-right: 12px;
        }

        /* Imagen del mapa */
        .species-map-image {
            width: 100%;
            max-width: 400px;
            border-radius: 14px;
            display: block;
            margin-top: 10px;
            border: 1px solid #e5e7eb;
        }

        /* Contenedor del gráfico de estacionalidad */
        .phenology-chart-wrapper {
            margin-top: 18px;
            background: #f9fafb;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 12px 16px 18px;
            max-width: 720px;
            height: 260px; /* altura fija para que no crezca infinito */
            position: relative;
        }

        .phenology-chart-wrapper canvas {
            width: 100% !important;
            height: 100% !important;
        }

        @media (max-width: 1024px) {
            .species-detail-wrapper {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="zonificacion-container">

        {{-- Card de filtros (decorativa, mantiene el mockup) --}}
        <div class="catalogo-filtros-card">
            <form class="catalogo-search-form" method="GET" action="{{ route('flora.catalogo.index') }}">
                <div class="catalogo-filtros-row">
                    <input
                        type="text"
                        name="q"
                        placeholder="Buscar por Quillay, Eucalipto, etc..."
                        value=""
                    >

                    <select name="nectar" class="catalogo-select">
                        <option value="">Néctar / Polen...</option>
                        <option value="nectar">Néctar</option>
                        <option value="polen">Polen</option>
                        <option value="ambos">Ambos</option>
                    </select>

                    <select name="forma" class="catalogo-select">
                        <option value="">Forma (Árbol/Arbusto)</option>
                        <option value="Árbol">Árbol</option>
                        <option value="Arbusto">Arbusto</option>
                    </select>

                    <select name="nivel" class="catalogo-select">
                        <option value="">Nivel de atracción</option>
                        <option value="Alto">Alto</option>
                        <option value="Medio">Medio</option>
                        <option value="Bajo">Bajo</option>
                    </select>

                    <select name="floracion" class="catalogo-select">
                        <option value="">Época de floración</option>
                        <option value="Primavera">Primavera</option>
                        <option value="Verano">Verano</option>
                        <option value="Otoño">Otoño</option>
                        <option value="Invierno">Invierno</option>
                    </select>
                </div>

                <div class="catalogo-actions">
                    <button type="submit" class="btn-reset-filtros">
                        Limpiar filtros
                    </button>

                    <a href="{{ route('flora.catalogo.index') }}">
                        <button type="button" class="btn-ver-listado">
                            Ver listado completo
                        </button>
                    </a>
                </div>
            </form>
        </div>

        {{-- Detalle de la especie seleccionada --}}
        <div class="species-detail-wrapper">

            {{-- Columna izquierda: nombre + imagen --}}
            <div>
                <div class="species-name">
                    {{ $species->common_name }}
                </div>
                <div class="species-scientific">
                    ({{ $species->scientific_name }})
                </div>

                <div style="margin-top:16px;">
                    @if($species->image_path)
                        {{-- Las imágenes de la flor están en public/flora, y en BD guardas algo como "flora/avellano.png" --}}
                        <img
                            src="{{ asset($species->image_path) }}"
                            alt="{{ $species->common_name }}"
                            class="species-image-main"
                        >
                    @else
                        <div class="species-image-placeholder">
                            Sin imagen disponible para esta especie.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Columna derecha: tabs y contenido --}}
            <div>
                <div class="species-tabs">
                    <button class="species-tab-btn active" data-tab="descripcion" type="button">Descripción</button>
                    <button class="species-tab-btn" data-tab="taxonomia" type="button">Taxonomía</button>
                    <button class="species-tab-btn" data-tab="iaa" type="button">IAA</button>
                    <button class="species-tab-btn" data-tab="estacionalidad" type="button">Estacionalidad</button>
                    <button class="species-tab-btn" data-tab="mapa" type="button">Mapa</button>
                </div>

                {{-- ===== TAB: DESCRIPCIÓN ===== --}}
                <div id="tab-descripcion" class="tab-content active">
                    <div class="species-section-title">Descripción</div>
                    <div class="species-text">
                        @if($species->description)
                            {!! nl2br(e($species->description)) !!}
                        @else
                            <p>
                                Aún no se ha cargado una descripción para
                                <strong>{{ $species->common_name }}</strong>.
                                Puedes completarla en la tabla
                                <code>flora_species</code> (campo <code>description</code>).
                            </p>
                        @endif
                    </div>

                    <div class="species-meta">
                        <span><strong>Familia:</strong> {{ $species->family ?? '—' }}</span>
                        <span><strong>Origen:</strong> {{ $species->origin ?? '—' }}</span>
                        <span><strong>Hábito de crecimiento:</strong> {{ $species->growth_habit ?? '—' }}</span>
                    </div>
                </div>

                {{-- ===== TAB: TAXONOMÍA ===== --}}
                <div id="tab-taxonomia" class="tab-content">
                    <div class="species-section-title">Taxonomía</div>
                    <div class="species-text">
                        <p>
                            Aquí puedes detallar la clasificación taxonómica de
                            <strong>{{ $species->common_name }}</strong>:
                            familia, género, especie y otros niveles que quieras documentar.
                            Por ahora, B-MaiA utiliza principalmente la
                            <strong>familia</strong> y el <strong>nombre científico</strong>
                            guardados en <code>flora_species</code>.
                        </p>
                        <p style="margin-top:8px;">
                            <strong>Familia registrada:</strong> {{ $species->family ?? '—' }}<br>
                            <strong>Nombre científico:</strong> <em>{{ $species->scientific_name }}</em>
                        </p>
                    </div>
                </div>

                {{-- ===== TAB: IAA ===== --}}
                <div id="tab-iaa" class="tab-content">
                    <div class="species-section-title">Índice de Atractividad Apícola (IAA)</div>
                    <div class="species-text">
                        <p>
                            En versiones futuras, aquí se mostrará el
                            <strong>IAA</strong> de {{ $species->common_name }},
                            combinando información sobre tipo de recurso floral
                            (néctar / polen), abundancia y periodo de floración.
                        </p>
                        <p style="margin-top:8px;">
                            Por ahora, puedes usar esta pestaña como recordatorio del
                            valor apícola de la especie y registrar datos
                            adicionales directamente en la tabla
                            <code>flora_species</code> o en módulos futuros.
                        </p>
                    </div>
                </div>

                {{-- ===== TAB: ESTACIONALIDAD ===== --}}
                <div id="tab-estacionalidad" class="tab-content">
                    <div class="species-section-title">Estacionalidad</div>
                    <div class="species-text">
                        <p>
                            Esta sección resume la época de floración de
                            <strong>{{ $species->common_name }}</strong>.
                        </p>
                        <p style="margin-top:8px;">
                            <strong>Época de floración registrada:</strong>
                            {{ $species->flowering_season ?: 'No registrada en flora_species' }}
                        </p>
                        <p style="margin-top:8px;">
                            En el futuro podrás integrar esta información con el
                            módulo de <strong>Monitoreo Histórico</strong> para
                            cruzar clima, floraciones y productividad de miel.
                        </p>
                    </div>

                    @if($phenology)
                        <div class="phenology-chart-wrapper">
                            <canvas id="phenologyChart"></canvas>
                        </div>
                    @else
                        <p style="margin-top:10px; font-size:13px; color:#6b7280;">
                            Aún no se han registrado datos fenológicos (campo
                            <code>phenology</code> en <code>flora_species</code>).
                            Puedes cargarlos como JSON para visualizar el gráfico.
                        </p>
                    @endif
                </div>

                {{-- ===== TAB: MAPA ===== --}}
                <div id="tab-mapa" class="tab-content">
                    <div class="species-section-title">Distribución y mapa</div>
                    <div class="species-text">
                        @if($species->map_image_path)
                            <p>
                                Mapa de distribución aproximada de
                                <strong>{{ $species->common_name }}</strong>.
                            </p>

                            {{-- Las imágenes de mapa irían, por ejemplo, en public/flora_maps
                                 y en BD guardas "flora_maps/avellano_mapa.png" --}}
                            <img
                                src="{{ asset($species->map_image_path) }}"
                                alt="Mapa de distribución de {{ $species->common_name }}"
                                class="species-map-image"
                            >

                            <p style="margin-top:8px;">
                                Esta imagen indica las zonas donde se concentra el
                                crecimiento natural o el uso apícola de la especie.
                            </p>
                        @else
                            <p>
                                En futuras versiones de B-MaiA, aquí se podrá
                                visualizar la <strong>distribución geográfica</strong>
                                de <strong>{{ $species->common_name }}</strong>,
                                integrada con el módulo de <strong>Hoja de Ruta</strong>
                                y los mapas WMS/WFS.
                            </p>
                            <p style="margin-top:8px;">
                                Por ahora esta pestaña funciona como recordatorio del
                                lugar donde conectaremos esa información geoespacial
                                (regiones, comunas y puntos de muestreo).
                            </p>
                            <p style="margin-top:8px; font-size:13px; color:#6b7280;">
                                Si quieres cargar mapas estáticos, guarda la ruta en el campo
                                <code>map_image_path</code> de <code>flora_species</code>.
                            </p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Chart.js para el gráfico de estacionalidad --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Script para tabs + gráfico --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('.species-tab-btn');
            const contents = document.querySelectorAll('.tab-content');

            buttons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const tab = btn.getAttribute('data-tab');

                    // activar / desactivar botones
                    buttons.forEach(b => b.classList.toggle('active', b === btn));

                    // mostrar / ocultar contenidos
                    contents.forEach(c => {
                        c.classList.toggle('active', c.id === 'tab-' + tab);
                    });
                });
            });

            // ====== Gráfico de estacionalidad (sólo si hay datos) ======
            const phenology = @json($phenology);
            const canvas = document.getElementById('phenologyChart');

            if (canvas && phenology) {
                const months = phenology.months || ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];

                const datasetsConfig = [
                    { key: 'flowers',     label: 'Flores',             borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.18)' },
                    { key: 'no_flowers',  label: 'Sin flores ni frutos', borderColor: '#f97316', backgroundColor: 'rgba(249,115,22,0.18)' },
                    { key: 'buds',        label: 'Botones florales',   borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,0.18)' },
                    { key: 'fruits',      label: 'Frutas o semillas',  borderColor: '#0ea5e9', backgroundColor: 'rgba(14,165,233,0.18)' },
                    { key: 'no_notes',    label: 'Sin anotación',      borderColor: '#6b7280', backgroundColor: 'rgba(107,114,128,0.18)' },
                ];

                const datasets = datasetsConfig
                    .filter(cfg => Array.isArray(phenology[cfg.key]))
                    .map(cfg => ({
                        label: cfg.label,
                        data: phenology[cfg.key],
                        borderColor: cfg.borderColor,
                        backgroundColor: cfg.backgroundColor,
                        tension: 0.3,
                        fill: true,
                        pointRadius: 3
                    }));

                new Chart(canvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                },
                                title: {
                                    display: true,
                                    text: 'Observaciones'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Meses'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                });
            }
        });
    </script>
@endsection
