{{-- resources/views/hoja_ruta/explorador.blade.php --}}
@extends('layouts.app')

@section('title', 'Hoja de ruta • Explorador')

@section('content')
    @include('hoja_ruta.partials.subnav')

    {{-- Fuente + estilos compartidos del módulo (banner honeycomb, etc.) --}}
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet"
          href="{{ asset('css/components/home-user/zonificacion.css') }}">

    <div class="zonificacion-container">

        {{-- ========== BANNER HONEYCOMB (MANTENIDO) ========== --}}
        <div class="honeycomb-header">
            <div class="honeycomb-overlay"></div>
            <div class="header-content">
                <h1 class="zonificacion-title">Explorador de Zonas</h1>
                <p class="zonificacion-subtitle">
                    Mapa interactivo de regiones: pasa el mouse sobre una zona para ver su nombre.
                </p>
            </div>
        </div>

        {{-- ========== ESTILOS ESPECÍFICOS DEL EXPLORADOR ========== --}}
        <style>
            .explorador-grid{
                display:grid;
                grid-template-columns: minmax(260px, 320px) 1fr;
                gap:18px;
                margin-top:18px;
            }
            @media (max-width: 900px){
                .explorador-grid{
                    grid-template-columns: 1fr;
                }
            }

            .explorador-card{
                border-radius:18px;
                padding:16px 18px;
                background: linear-gradient(180deg, rgba(255,255,255,0.05), rgba(9,16,26,0.95));
                border:1px solid rgba(255,255,255,0.06);
                box-shadow: 0 18px 45px rgba(0,0,0,0.45);
                color:#e5edf5;
            }

            .explorador-card h2{
                margin:0 0 8px;
                font-size:18px;
                font-weight:600;
            }
            .explorador-card p{
                margin:0 0 12px;
                font-size:13px;
                color:#a0b4c6;
            }

            .info-box{
                margin-top:10px;
                padding:10px 12px;
                border-radius:12px;
                background:rgba(6,18,30,0.9);
                border:1px solid rgba(255,255,255,0.06);
            }
            .info-label{
                font-size:11px;
                text-transform:uppercase;
                letter-spacing:0.06em;
                color:#7f93a8;
                margin-bottom:4px;
            }
            .info-value{
                font-size:15px;
                font-weight:600;
                color:#f1f7ff;
            }
            .info-subvalue{
                font-size:12px;
                color:#9fb3c7;
                margin-top:2px;
            }

            .explorador-map-wrapper{
                position:relative;
                border-radius:18px;
                padding:14px;
                background:
                    radial-gradient(900px 500px at 0% 0%, #132c45 0, transparent 60%),
                    radial-gradient(700px 500px at 110% 10%, #091727 0, transparent 60%),
                    #050a12;
                border:1px solid rgba(255,255,255,0.06);
                box-shadow:0 20px 45px rgba(0,0,0,0.65);
                overflow:hidden;
            }

            .explorador-map-title{
                font-size:13px;
                font-weight:600;
                color:#e3edf7;
                margin:0 0 6px;
            }
            .explorador-map-subtitle{
                font-size:11px;
                color:#8ea2b7;
                margin:0 0 10px;
            }

            .explorador-map-inner{
                position:relative;
                border-radius:14px;
                padding:10px;
                background: radial-gradient(circle at 20% 0%, #10263b 0, transparent 55%),
                            radial-gradient(circle at 110% 30%, #122c44 0, transparent 55%);
                border:1px solid rgba(255,255,255,0.06);
                min-height:520px;
                display:flex;
                align-items:center;
                justify-content:center;
            }

            /* El <object> que carga tu chile.svg */
            #chileMap{
                width:100%;
                max-width:520px;
                height:auto;
                display:block;
            }

            /* Tooltip flotante sobre el mapa */
            .map-tooltip{
                position:absolute;
                display:none;
                pointer-events:none;
                transform:translate(-50%, -120%);
                padding:6px 8px;
                font-size:11px;
                border-radius:8px;
                background:rgba(6,18,30,0.92);
                color:#eaf3ff;
                border:1px solid rgba(255,255,255,0.08);
                box-shadow:0 10px 24px rgba(0,0,0,0.55);
                white-space:nowrap;
                z-index:10;
            }
            .map-tooltip::after{
                content:"";
                position:absolute;
                left:50%;
                bottom:-5px;
                transform:translateX(-50%) rotate(45deg);
                width:8px;
                height:8px;
                background:inherit;
                border-left:1px solid rgba(255,255,255,0.08);
                border-bottom:1px solid rgba(255,255,255,0.08);
            }

            .map-legend{
                position:absolute;
                left:12px;
                bottom:12px;
                padding:6px 8px;
                font-size:11px;
                border-radius:10px;
                background:rgba(4,12,22,0.9);
                color:#9fb3c7;
                border:1px solid rgba(255,255,255,0.08);
            }
            .map-legend span{
                font-weight:600;
                color:#f3fbff;
            }

            /* ===== Lista de comunas ===== */
            .comunas-list{
                margin-top:6px;
                padding-left:14px;
                max-height:200px;
                overflow:auto;
                font-size:12px;
                color:#e5edf5;
            }
            .comunas-list li + li{
                margin-top:2px;
            }
            .comuna-muted{
                color:#9fb3c7;
                font-style:italic;
            }

            @media (max-width: 900px){
                .explorador-map-inner{
                    min-height:420px;
                }
            }
        </style>

        {{-- ========== CONTENIDO PRINCIPAL: PANEL + MAPA ========== --}}
        <div class="explorador-grid">
            {{-- Panel izquierdo --}}
            <section class="explorador-card">
                <h2>Panel de información</h2>
                <p>
                    Usa este panel para ver detalles básicos de la región sobre la que pasas el mouse.
                    Más adelante aquí podemos conectar tu base de datos (perfiles de región, flora, etc.).
                </p>

                <div class="info-box">
                    <div class="info-label">Región actual</div>
                    <div id="regionName" class="info-value">
                        Pasa el cursor sobre una región del mapa
                    </div>
                    <div id="regionCode" class="info-subvalue">
                        ID: —
                    </div>
                </div>

                <div class="info-box" style="margin-top:10px;">
                    <div class="info-label">Estado</div>
                    <div class="info-subvalue" id="hoverHint">
                        Sin región seleccionada. Al hacer clic sobre una región, más adelante podremos abrir
                        un detalle con comunas, floraciones, apiarios, etc.
                    </div>
                </div>

                {{-- NUEVO: lista de comunas --}}
                <div class="info-box" style="margin-top:10px;">
                    <div class="info-label">Comunas de la región</div>
                    <ul id="comunasList" class="comunas-list">
                        <li class="comuna-muted">
                            Haz clic en una región del mapa para cargar sus comunas.
                        </li>
                    </ul>
                </div>
            </section>

            {{-- Mapa SVG a la derecha --}}
            <section class="explorador-map-wrapper">
                <div class="explorador-map-title">Mapa de Chile (SVG propio)</div>
                <p class="explorador-map-subtitle">
                    Archivo <code>chile.svg</code> cargado desde tu carpeta <code>public/maps</code>.
                </p>

                <div class="explorador-map-inner" id="mapContainer">
                    {{-- Tooltip flotante --}}
                    <div id="mapTooltip" class="map-tooltip">Región</div>

                    {{-- IMPORTANTE: onload llama a initChileMap(this) --}}
                    <object
                        id="chileMap"
                        data="{{ asset('maps/chile.svg') }}"
                        type="image/svg+xml"
                        aria-label="Mapa de Chile"
                        onload="initChileMap(this)">
                    </object>

                    <div class="map-legend">
                        <span>Tip:</span> pasa el mouse sobre las zonas para ver el nombre de la región.
                    </div>
                </div>
            </section>
        </div>
    </div> {{-- /zonificacion-container --}}

    {{-- ========== SCRIPT: HACER EL SVG INTERACTIVO + CARGAR COMUNAS ========== --}}
    <script>
        (function(){
            const mapBox     = document.getElementById('mapContainer');
            const tooltip    = document.getElementById('mapTooltip');
            const regionName = document.getElementById('regionName');
            const regionCode = document.getElementById('regionCode');
            const hoverHint  = document.getElementById('hoverHint');
            const comunasList = document.getElementById('comunasList');

            // base de la API en Laravel
            const BASE_REGION_URL = "{{ url('/hoja-de-ruta/api/region') }}";

            function setComunasMessage(text){
                if (!comunasList) return;
                comunasList.innerHTML = '';
                const li = document.createElement('li');
                li.className = 'comuna-muted';
                li.textContent = text;
                comunasList.appendChild(li);
            }

            function renderComunas(comunas){
                if (!comunasList) return;
                comunasList.innerHTML = '';
                comunas.forEach(c => {
                    const li = document.createElement('li');
                    li.textContent = c.nombre;
                    comunasList.appendChild(li);
                });
            }

            function resetInfo(){
                regionName.textContent = 'Pasa el cursor sobre una región del mapa';
                regionCode.textContent = 'ID: —';
                tooltip.style.display  = 'none';
                hoverHint.textContent  = 'Sin región seleccionada. Pasa el mouse para ver nombres.';
                setComunasMessage('Haz clic en una región del mapa para cargar sus comunas.');
            }

            // Hacemos la función global para usarla en onload del <object>
            window.initChileMap = function(obj){
                const svgDoc = obj.contentDocument || obj.getSVGDocument();
                if (!svgDoc){
                    console.warn('No se pudo acceder al contenido de chile.svg');
                    return;
                }

                const svgRoot = svgDoc.documentElement;

                // ====== ZOOM DENTRO DEL SVG (igual que tenías) ======
                svgRoot.removeAttribute('width');
                svgRoot.removeAttribute('height');
                svgRoot.setAttribute('viewBox', '430 0 200 708');
                svgRoot.setAttribute('preserveAspectRatio', 'xMidYMid meet');

                // ====== ESTILOS DE REGIONES ======
                const styleEl = svgDoc.createElementNS('http://www.w3.org/2000/svg', 'style');
                styleEl.textContent = `
                    path[title]{
                        fill:#1f2f48;
                        stroke:#9fb3c7;
                        stroke-width:0.7;
                        cursor:pointer;
                        transition: fill 0.15s ease, stroke 0.15s ease, stroke-width 0.15s ease;
                    }
                    path[title].is-hovered{
                        fill:#1ee2a4;
                        stroke:#ffffff;
                        stroke-width:1.2;
                    }
                `;
                svgRoot.appendChild(styleEl);

                const regions = svgDoc.querySelectorAll('path[title]');

                regions.forEach(function(regionPath){
                    regionPath.addEventListener('mouseenter', function (event) {
                        const title = regionPath.getAttribute('title') || 'Zona sin nombre';
                        const id    = regionPath.id || '';

                        regionName.textContent = title;
                        regionCode.textContent = id ? ('ID: ' + id) : 'ID: —';
                        hoverHint.textContent  = 'Región bajo el cursor. Haz clic para ver las comunas.';

                        regions.forEach(r => r.classList.remove('is-hovered'));
                        regionPath.classList.add('is-hovered');

                        tooltip.style.display = 'block';
                        tooltip.textContent   = title;
                    });

                    regionPath.addEventListener('mousemove', function (event) {
                        const rect = mapBox.getBoundingClientRect();
                        const x = event.clientX - rect.left;
                        const y = event.clientY - rect.top;

                        tooltip.style.left = x + 'px';
                        tooltip.style.top  = (y - 10) + 'px';
                    });

                    regionPath.addEventListener('mouseleave', function () {
                        tooltip.style.display = 'none';
                        hoverHint.textContent = 'Sin región seleccionada. Pasa el mouse para ver nombres.';
                        regionPath.classList.remove('is-hovered');
                    });

                    regionPath.addEventListener('click', function () {
                        const title = regionPath.getAttribute('title') || 'Zona sin nombre';
                        const id    = regionPath.id || '';

                        hoverHint.textContent = 'Cargando comunas de: ' + title +
                            (id ? (' (ID: ' + id + ')') : '') + '…';

                        if (!id){
                            setComunasMessage('Esta región no tiene un ID en el SVG.');
                            return;
                        }

                        setComunasMessage('Cargando comunas…');

                        fetch(`${BASE_REGION_URL}/${encodeURIComponent(id)}`)
                            .then(r => {
                                if (!r.ok) throw new Error('HTTP ' + r.status);
                                return r.json();
                            })
                            .then(data => {
                                hoverHint.textContent = 'Región cargada: ' + (data.region?.nombre || title);

                                if (!data.comunas || !data.comunas.length){
                                    setComunasMessage('Sin comunas registradas para esta región en la base de datos.');
                                    return;
                                }

                                renderComunas(data.comunas);
                            })
                            .catch(err => {
                                console.error(err);
                                hoverHint.textContent = 'Error al cargar comunas desde el servidor.';
                                setComunasMessage('No se pudieron cargar las comunas.');
                            });
                    });
                });
            };

            resetInfo();
        })();
    </script>
@endsection
