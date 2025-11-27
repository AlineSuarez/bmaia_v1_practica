{{-- resources/views/hoja_ruta/calculo.blade.php --}}
@extends('layouts.app')

@section('title','Hoja de Ruta • Cálculo de Combustible')

@section('content')
 @include('hoja_ruta.partials.subnav')

<!-- Leaflet CSS (para el mapa) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>

{{-- Banner honeycomb / estilos compartidos --}}
<link rel="stylesheet" href="{{ asset('css/components/home-user/zonificacion.css') }}">
{{-- Banner honeycomb / estilos compartidos --}}
<link rel="stylesheet" href="{{ asset('css/components/home-user/zonificacion.css') }}">

{{-- CSS específico de Cálculo de Ruta --}}
<link rel="stylesheet" href="{{ asset('css/components/home-user/hoja-ruta-calculo.css') }}">



<div class="zonificacion-container">
  {{-- Banner honeycomb --}}
  <div class="honeycomb-header">
      <div class="honeycomb-overlay"></div>
      <div class="header-content">
          <h1 class="zonificacion-title">Cálculo de Ruta</h1>
          <p class="zonificacion-subtitle">
              Estima combustible, tiempo y costos para tus recorridos con mayor precisión.
          </p>
      </div>
  </div>

  <div class="container-hr">
    {{-- Header interno --}}
    <div class="header">
      <div>
        <h1>Cálculo de Ruta</h1>
        <p>Estima combustible, peajes y costo total según distancia y vehículo.</p>
        <div class="meta">
          <span class="badge">
            <span>●</span> Beta
          </span>
          <span>Estimaciones aproximadas</span>
        </div>
      </div>
      <div style="display:flex; gap:8px">
        <button id="btnReset" class="btn ghost">Reiniciar</button>
        <button id="btnEjemplo" class="btn secondary">Cargar ejemplo</button>
      </div>
    </div>

    <div class="hr-row">
      {{-- IZQUIERDA --}}
      <div class="space-y">
        {{-- Tramos --}}
        <div class="card">
          <div class="header" style="margin-bottom:10px">
            <div>
              <h2>Puntos / Tramos</h2>
              <p class="help">
                Agrega tramos (km), selecciona ida/vuelta y viajes totales.
                Ahora puedes calcular <b>tiempo de ruta</b> por tramo y dibujar la ruta en el mapa.
              </p>
            </div>
            <button id="btnAddTramo" class="btn small">+ Agregar tramo</button>
          </div>

          <div id="tramosList" class="space-y-rows" style="display:flex; flex-direction:column; gap:10px"></div>

          <div class="divider"></div>
          <div class="row2">
            <label class="label" style="display:flex; align-items:center; gap:10px">
              <input id="chkIdaVuelta" type="checkbox" style="scale:1.15"> Contar ida y vuelta
            </label>
            <label>
              <span class="label">Viajes</span>
              <input id="numViajes" type="number" min="1" value="1" class="inp" />
            </label>
          </div>
          <div class="kv" style="margin-top:6px">
            <div class="k">Distancia total</div>
            <div class="v" id="distanciaTotalKm">0 km</div>
          </div>
        </div>

        {{-- Mapa --}}
        <div class="card">
          <div class="header" style="margin-bottom:10px">
            <div>
              <h2>Mapa de ruta</h2>
              <p class="help">Se dibuja la última ruta calculada (puedes acumular varias). Usa “Limpiar mapa” para borrar.</p>
            </div>
            <div style="display:flex; gap:8px">
              <button id="btnClearMap" class="btn small ghost">Limpiar mapa</button>
            </div>
          </div>
          <div id="routeMap" class="map-wrap"></div>
          <p class="mini" style="margin-top:8px">
            © OpenStreetMap contributors | Tiles por OSM | Ruta por OSRM (sin tráfico en tiempo real).
          </p>
        </div>

        {{-- Vehículo --}}
        <div class="card">
          <h2>Vehículo y combustible</h2>
          <div class="row3" style="margin-top:10px">
            <label>
              <span class="label">Tipo de vehículo</span>
              <select id="tipoVehiculo" class="sel">
                <option value="camioneta" data-kml="9">Camioneta (≈ 9 km/L)</option>
                <option value="camion34" data-kml="6">Camión 3/4 (≈ 6 km/L)</option>
                <option value="camion" data-kml="4.5">Camión (≈ 4.5 km/L)</option>
                <option value="auto" data-kml="13">Auto (≈ 13 km/L)</option>
                <option value="moto" data-kml="28">Moto (≈ 28 km/L)</option>
                <option value="personalizado" data-kml="10">Personalizado…</option>
              </select>
            </label>

            <label>
              <span class="label">Rendimiento (km/L)</span>
              <input id="kml" type="number" step="0.1" min="0.1" value="9" class="inp">
              <span class="mini">Se auto-ajusta con el vehículo (editable).</span>
            </label>

            <label>
              <span class="label">Precio combustible (CLP / L)</span>
              <input id="precioLitro" type="number" step="1" min="0" value="1350" class="inp">
              <span class="mini">Referencia; puedes cambiarlo manualmente.</span>
            </label>
          </div>

          {{-- Botones rápidos de combustible --}}
          <div style="margin-top:10px">
            <span class="mini">Atajo de tipo de combustible (valores de referencia):</span>
            <div class="fuel-buttons">
              {{-- Valores de ejemplo, ajústalos a lo que tú quieras --}}
              <button type="button"
                      class="fuel-btn active"
                      data-type="93"
                      data-price="1350">
                Gasolina 93
              </button>
              <button type="button"
                      class="fuel-btn"
                      data-type="95"
                      data-price="1390">
                Gasolina 95
              </button>
              <button type="button"
                      class="fuel-btn"
                      data-type="97"
                      data-price="1430">
                Gasolina 97
              </button>
              <button type="button"
                      class="fuel-btn"
                      data-type="diesel"
                      data-price="1100">
                Diésel
              </button>
            </div>
          </div>

          <div class="row3" style="margin-top:12px">
            <label>
              <span class="label">Factor carga/terreno (%)</span>
              <input id="factorExtraPct" type="number" step="1" min="0" value="0" class="inp">
              <span class="mini">Sobreconsumo: peso, ripio, A/C, viento…</span>
            </label>
            <label>
              <span class="label">Ralentí (horas)</span>
              <input id="horasRalenti" type="number" step="0.1" min="0" value="0" class="inp">
              <span class="mini">Parado con motor encendido.</span>
            </label>
            <label>
              <span class="label">Consumo ralentí (L/h)</span>
              <input id="consumoRalenti" type="number" step="0.1" min="0" value="0.8" class="inp">
            </label>
          </div>

          <div class="row2" style="margin-top:12px">
            <label>
              <span class="label">Peajes (CLP)</span>
              <input id="peajes" type="number" step="1" min="0" value="0" class="inp">
            </label>
          </div>
        </div>
      </div>

      {{-- DERECHA --}}
      <div class="sticky">
        <div class="card">
          <h2>Resultados</h2>
          <div class="kv"><div class="k">Distancia ajustada</div><div class="v" id="resDistanciaAjustada">0 km</div></div>
          <div class="kv"><div class="k">Litros estimados</div><div class="v" id="resLitros">0.00 L</div></div>
          <div class="kv"><div class="k">Combustible</div><div class="v" id="resCombustible">$0</div></div>
          <div class="kv"><div class="k">Peajes</div><div class="v" id="resPeajes">$0</div></div>
          <div class="kv"><div class="k">Tiempo estimado total</div><div class="v" id="resTiempo">—</div></div>
          <div class="divider"></div>
          <div class="kv">
            <div class="k" style="font-weight:700;color:var(--ink)">TOTAL</div>
            <div class="v" id="resTotal">$0</div>
          </div>
          <div style="display:flex; gap:8px; margin-top:14px">
            <button id="btnCopiar" class="btn" style="flex:1">Copiar resumen</button>
          </div>
          <p class="mini" style="margin-top:10px">
            *Son estimaciones; puede variar por tráfico, pendientes, clima y carga. OSRM no incluye tráfico en tiempo real.
          </p>
        </div>
      </div>
    </div>

    <template id="tplTramo">
      <div class="tramo">
        <div class="namecol">
          <input type="text" placeholder="Nombre del tramo (opcional)" class="inp name">
          <div class="row2">
            <input type="text" placeholder="Origen (ej: Santiago, CL)" class="inp tramo-from">
            <input type="text" placeholder="Destino (ej: Valparaíso, CL)" class="inp tramo-to">
          </div>
          <div class="tools mini">
            <button class="btn small ghost btn-time" title="Calcular tiempo con OSRM">⏱ Calcular tiempo</button>
            <label style="display:flex; align-items:center; gap:6px">
              <input type="checkbox" class="chk-autokm"> Usar km de la ruta
            </label>
            <span class="time-output">—</span>
          </div>
        </div>
        <div class="kmwrap">
          <input type="number" step="0.1" min="0" value="0" class="inp tramo-km" style="width:120px">
          <span class="unit">km</span>
          <button class="btn small del" title="Eliminar">✕</button>
        </div>
      </div>
    </template>
  </div> {{-- /.container-hr --}}
</div> {{-- /.zonificacion-container --}}

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

@endsection

@push('scripts')

@endpush
