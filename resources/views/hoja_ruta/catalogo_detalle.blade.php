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

    <link rel="stylesheet"
          href="{{ asset('css/components/home-user/zonificacion.css') }}">

    {{-- ✅ Estilos específicos del detalle del catálogo --}}
    <link rel="stylesheet"
          href="{{ asset('css/components/home-user/hoja-ruta-catalogo-detalle.css') }}">

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
                        {{-- Las imágenes de la flor están en public/img/flora,
                             y en BD guardas algo como "flora/avellano.png" --}}
                        <img
                            src="{{ asset('img/'.$species->image_path) }}"
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
                        <div class="phenology-chart-wrapper"
                             id="phenologyWrapper"
                             data-phenology='@json($phenology)'>
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

                            {{-- Las imágenes de mapa ahora van en public/img/flora_maps
                                 y en BD guardas "flora_maps/mapa_avellano.png", etc. --}}
                            <img
                                src="{{ asset('img/'.$species->map_image_path) }}"
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
@endsection

@push('scripts')
    <script src="{{ asset('js/hoja_ruta/catalogo_detalle.js') }}"></script>
@endpush
