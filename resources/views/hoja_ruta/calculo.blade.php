{{-- resources/views/hoja_ruta/calculo.blade.php --}}
@extends('layouts.app')

@section('title','Hoja de Ruta • Cálculo de Combustible')

@section('content')
 @include('hoja_ruta.partials.subnav')

<!-- Leaflet CSS (para el mapa) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>

{{-- Banner honeycomb / estilos compartidos --}}
<link rel="stylesheet" href="{{ asset('css/components/home-user/zonificacion.css') }}">

<style>
/* ====== Tema para Cálculo de Ruta ====== */
:root{
  --bg:#020818;
  --bg-2:#050f1f;
  --ink:#f9fbff;
  --ink-d:#c7d3df;
  --muted:#8c9db0;
  --border:#1d2a3a;
  --border-soft:#253649;
  --brand:#1ee2a4;
  --brand-2:#36c6ff;
  --warn:#facc15;
  --danger:#ef4444;
  --card:#050f1f;
  --card-soft:#071524;
  --shadow:0 18px 40px rgba(0,0,0,.5);
}

/* contenedor principal de esta vista */
.zonificacion-container{
  padding-inline: 24px;
  padding-bottom: 40px;
}

/* “bloque” oscuro que envuelve todo el módulo */
.container-hr{
  max-width:1200px;
  margin:22px auto 0;
  padding:22px 22px 30px;
  background:radial-gradient(circle at top left,#0b1626 0,#020818 55%,#01040a 100%);
  border-radius:24px;
  border:1px solid rgba(255,255,255,.03);
  box-shadow:var(--shadow);
  position:relative;
  overflow:hidden;
}
.container-hr::before{
  content:"";
  position:absolute;
  inset:-40%;
  background:
    radial-gradient(circle at 0 0, rgba(54,198,255,.09) 0, transparent 55%),
    radial-gradient(circle at 100% 20%, rgba(30,226,164,.08) 0, transparent 50%);
  opacity:.9;
  pointer-events:none;
}

/* grid principal */
.hr-row{
  position:relative;
  z-index:1;
  display:grid;
  grid-template-columns:1fr;
  gap:20px;
}
@media(min-width:1024px){
  .hr-row{ grid-template-columns:2fr 1fr; }
}

/* tarjetas */
.card{
  background:linear-gradient(145deg, var(--card-soft), var(--card));
  border-radius:18px;
  padding:20px;
  border:1px solid var(--border-soft);
  box-shadow:0 10px 24px rgba(0,0,0,.55);
  backdrop-filter:blur(14px);
}
.card h2{
  color:var(--ink);
  font-size:1.05rem;
  margin:0 0 6px;
}
.card p.help{
  color:var(--muted);
  font-size:.86rem;
  margin:.2rem 0 .65rem;
}

/* pares clave/valor */
.kv{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:7px 0;
}
.kv + .kv{ border-top:1px dashed var(--border-soft); }
.kv .k{ color:var(--ink-d); font-size:.93rem; }
.kv .v{ color:var(--ink); font-weight:700; }

/* botones */
.btn{
  border:1px solid transparent;
  color:#fff;
  background:linear-gradient(135deg,var(--brand),var(--brand-2));
  padding:10px 14px;
  border-radius:999px;
  cursor:pointer;
  font-size:.9rem;
  font-weight:500;
  box-shadow:0 8px 18px rgba(0,0,0,.4);
  transition:.15s transform,.15s box-shadow,.15s filter;
}
.btn:hover{
  filter:brightness(1.05);
  transform:translateY(-1px);
  box-shadow:0 12px 24px rgba(0,0,0,.55);
}
.btn.secondary{
  background:#111c2b;
  border-color:#273447;
  box-shadow:none;
}
.btn.secondary:hover{
  background:#182435;
}
.btn.ghost{
  background:transparent;
  border-color:#273447;
  color:var(--ink);
  box-shadow:none;
}
.btn.ghost:hover{
  background:#111a28;
}
.btn.danger{ background:var(--danger); }
.btn.small{
  padding:6px 10px;
  border-radius:12px;
  font-size:.85rem;
}
.badge{
  display:inline-flex;
  align-items:center;
  gap:5px;
  font-size:.75rem;
  padding:3px 9px;
  border-radius:999px;
  background:rgba(250,204,21,.12);
  color:#fde68a;
  border:1px solid rgba(250,204,21,.5);
}

/* inputs / selects */
.inp, .sel{
  width:100%;
  background:#050c16;
  border:1px solid var(--border-soft);
  color:var(--ink);
  border-radius:12px;
  padding:9px 11px;
  outline:none;
  font-size:.9rem;
}
.inp::placeholder,
.sel::placeholder{ color:#64748b; }
.inp:focus, .sel:focus{
  border-color:#2b8f86;
  box-shadow:0 0 0 1px #2b8f86,0 0 0 6px rgba(43,143,134,.22);
}
.label{
  color:var(--ink-d);
  font-size:.84rem;
  margin-bottom:6px;
  display:block;
}

/* tramos */
.tramo{
  display:flex;
  gap:10px;
  align-items:flex-start;
  padding:10px 10px 12px;
  border-radius:14px;
  background:radial-gradient(circle at top left,#101c2a 0,#050c16 58%);
  border:1px solid rgba(148,163,184,.22);
}
.tramo .namecol{
  flex:1;
  display:flex;
  flex-direction:column;
  gap:8px;
}
.tramo .kmwrap{
  display:flex;
  gap:6px;
  align-items:center;
}
.tramo .unit{
  color:var(--muted);
  font-size:.88rem;
}
.tramo .del{
  background:#111827;
  border:1px solid rgba(248,250,252,.12);
  color:#e2e8f0;
}
.tramo .del:hover{
  background:#1f2937;
}
.tramo .tools{
  display:flex;
  gap:10px;
  align-items:center;
  flex-wrap:wrap;
}
.tramo .time-output{
  color:#72f1e8;
  font-weight:600;
  font-size:.82rem;
}

/* header interno (título + botones) */
.header{
  position:relative;
  z-index:1;
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  margin-bottom:18px;
}
.header h1{
  color:var(--ink);
  font-size:1.45rem;
  margin:0;
}
.header p{
  color:var(--muted);
  margin:3px 0 4px;
  font-size:.9rem;
}
.meta{
  display:flex;
  gap:8px;
  align-items:center;
  color:var(--muted);
  font-size:.82rem;
}

/* sticky derecha */
.sticky{
  position:sticky;
  top:18px;
}
.divider{
  height:1px;
  background:rgba(148,163,184,.22);
  margin:12px 0;
}
.mini{
  color:var(--muted);
  font-size:.79rem;
}

/* grids auxiliares */
.row2{
  display:grid;
  grid-template-columns:repeat(1,1fr);
  gap:12px;
}
@media(min-width:768px){
  .row2{ grid-template-columns:repeat(2,1fr); }
}
.row3{
  display:grid;
  grid-template-columns:repeat(1,1fr);
  gap:12px;
}
@media(min-width:768px){
  .row3{ grid-template-columns:repeat(3,1fr); }
}

/* mapa */
.map-wrap{
  height:420px;
  border-radius:16px;
  overflow:hidden;
  border:1px solid var(--border-soft);
}
.leaflet-container{
  background:#020817;
}

/* botones de combustible */
.fuel-buttons{
  display:flex;
  flex-wrap:wrap;
  gap:6px;
  margin-top:6px;
}
.fuel-btn{
  padding:6px 11px;
  font-size:.78rem;
  border-radius:999px;
  border:1px solid #273447;
  background:#020817;
  color:var(--ink-d);
  cursor:pointer;
  transition:.15s background,.15s transform,.15s box-shadow,.15s color;
}
.fuel-btn:hover{
  background:#0b1728;
  color:var(--ink);
  transform:translateY(-1px);
  box-shadow:0 8px 16px rgba(0,0,0,.4);
}
.fuel-btn.active{
  background:linear-gradient(135deg,var(--brand),var(--brand-2));
  border-color:transparent;
  color:#0b1020;
}

/* resultados a la derecha */
.sticky .card h2{
  font-size:1.02rem;
  margin-bottom:6px;
}
.sticky .card{
  border:1px solid rgba(110,231,183,.25);
  box-shadow:0 16px 30px rgba(15,118,110,.35);
}
#resTotal{
  color:#72f1e8;
  font-size:1.6rem;
}

/* texto de mini disclaimer */
.sticky .mini{
  font-size:.76rem;
  line-height:1.4;
}
</style>

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
<script>
(function(){
  const $  = (s)=>document.querySelector(s);
  const $$ = (s)=>Array.from(document.querySelectorAll(s));
  const money=(n)=>n.toLocaleString('es-CL',{style:'currency', currency:'CLP', maximumFractionDigits:0});
  const fmtDuration=(sec)=>{
    if(!sec || sec<=0) return '—';
    const s = Math.round(sec);
    const h = Math.floor(s/3600);
    const m = Math.floor((s%3600)/60);
    return h>0 ? `${h} h ${m} min` : `${m} min`;
  };

  const tpl=$('#tplTramo').content, list=$('#tramosList');
  const ida=$('#chkIdaVuelta'), viajes=$('#numViajes');
  const tipo=$('#tipoVehiculo'), kml=$('#kml'), precio=$('#precioLitro');
  const extra=$('#factorExtraPct'), hRal=$('#horasRalenti'), lph=$('#consumoRalenti'), peajes=$('#peajes');

  const outTot=$('#distanciaTotalKm'), outAdj=$('#resDistanciaAjustada'),
        outL=$('#resLitros'), outC=$('#resCombustible'), outP=$('#resPeajes'), outT=$('#resTotal'),
        outTime=$('#resTiempo');

  // ---------- Mapa ----------
  let map, routeLayer;
  function initMap(){
    if(map) return;
    map = L.map('routeMap', { zoomControl: true }).setView([-33.45, -70.66], 6); // Chile
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy: OpenStreetMap contributors'
    }).addTo(map);
    routeLayer = L.layerGroup().addTo(map);
    setTimeout(()=> map.invalidateSize(), 250);
  }
  function clearMap(){
    if(routeLayer) routeLayer.clearLayers();
  }
  function drawRouteOnMap(data){
    initMap();
    if(!data || !data.geometry || !data.geometry.coordinates) return;
    const coords = data.geometry.coordinates.map(([lon,lat]) => [lat,lon]);
    const poly = L.polyline(coords, {weight:5, opacity:.9}).addTo(routeLayer);
    L.marker([data.from.lat, data.from.lon]).addTo(routeLayer).bindTooltip('Origen').openTooltip();
    L.marker([data.to.lat, data.to.lon]).addTo(routeLayer).bindTooltip('Destino');
    map.fitBounds(poly.getBounds(), {padding:[20,20]});
  }

  // ---------- Tramos ----------
  function addTramo(name='', km=0, from='', to=''){
    const node = document.importNode(tpl,true);
    const nm   = node.querySelector('.name');
    const val  = node.querySelector('.tramo-km');
    const del  = node.querySelector('.del');
    const fInp = node.querySelector('.tramo-from');
    const tInp = node.querySelector('.tramo-to');
    const btnT = node.querySelector('.btn-time');
    const chkK = node.querySelector('.chk-autokm');
    const out  = node.querySelector('.time-output');

    nm.value  = name;
    val.value = km;
    fInp.value = from;
    tInp.value = to;

    val.addEventListener('input', recalc);
    del.addEventListener('click', (e)=>{
      e.preventDefault();
      e.currentTarget.closest('.tramo').remove();
      recalc();
    });

    btnT.addEventListener('click', async (e)=>{
      e.preventDefault();
      const tramoEl = e.currentTarget.closest('.tramo');
      const fromTxt = fInp.value.trim();
      const toTxt   = tInp.value.trim();
      if(!fromTxt || !toTxt){
        out.textContent='Falta origen/destino';
        return;
      }

      btnT.disabled = true;
      btnT.textContent = 'Calculando…';
      out.textContent = '…';

      try{
        const url = `/api/route-time?geom=1&from=${encodeURIComponent(fromTxt)}&to=${encodeURIComponent(toTxt)}`;
        const res = await fetch(url);
        const data = await res.json();

        if(!data.ok){
          out.textContent = data.error || 'Error de cálculo';
        } else {
          tramoEl.dataset.duration   = data.duration_s;
          tramoEl.dataset.distanceKm = (data.distance_m/1000).toFixed(1);
          out.textContent = `Tiempo: ${fmtDuration(data.duration_s)} • Ruta: ${(data.distance_m/1000).toFixed(1)} km`;

          if(chkK.checked){
            val.value = (data.distance_m/1000).toFixed(1);
          }
          drawRouteOnMap(data);
          recalc();
        }
      }catch(err){
        out.textContent = 'Error de red';
      }finally{
        btnT.disabled = false;
        btnT.textContent = '⏱ Calcular tiempo';
      }
    });

    list.appendChild(node);
    recalc();
  }

  function sumKm(){
    const kms = $$('.tramo-km')
      .map(i=>parseFloat(i.value||'0'))
      .reduce((a,b)=>a+b,0);
    let total = kms;
    if(ida.checked) total*=2;
    total *= Math.max(1, parseInt(viajes.value||'1',10));
    return total;
  }

  function sumDurationSeconds(){
    const base = $$('.tramo')
      .map(t=>parseFloat(t.dataset.duration||'0'))
      .reduce((a,b)=>a+b,0);
    let factor = Math.max(1, parseInt(viajes.value||'1',10));
    if(ida.checked) factor *= 2;
    return base * factor;
  }

  function recalc(){
    const opt = tipo.options[tipo.selectedIndex];
    if(tipo.value!=='personalizado'){
      kml.value = parseFloat(opt.dataset.kml||'10').toFixed(1);
    }

    const kmBase = sumKm();
    outTot.textContent = `${kmBase.toFixed(1)} km`;

    const adj = kmBase * (1 + Math.max(0, parseFloat(extra.value||'0'))/100);
    const rend = Math.max(0.1, parseFloat(kml.value||'0'));
    let litros = adj / rend;

    litros += Math.max(0, parseFloat(lph.value||'0')) * Math.max(0, parseFloat(hRal.value||'0'));

    const costoComb   = Math.round(litros * Math.max(0, parseFloat(precio.value||'0')));
    const costoPeajes = Math.max(0, parseFloat(peajes.value||'0'));

    outAdj.textContent = `${adj.toFixed(1)} km`;
    outL.textContent   = `${litros.toFixed(2)} L`;
    outC.textContent   = money(costoComb);
    outP.textContent   = money(costoPeajes);
    outT.textContent   = money(costoComb + costoPeajes);

    const secs = sumDurationSeconds();
    outTime.textContent = secs>0 ? fmtDuration(secs) : '—';
  }

  // ---------- Botones de combustible ----------
  function initFuelButtons(){
    const fuelBtns = $$('.fuel-btn');
    fuelBtns.forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const price = parseFloat(btn.dataset.price || '0');
        if(price > 0){
          // usa el elemento global por id (como ya lo hacía tu código)
          precioLitro.value = Math.round(price);
        }
        fuelBtns.forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        recalc();
      });
    });
  }

  // events
  [ida, viajes, tipo, kml, precioLitro, extra, hRal, lph, peajes]
    .forEach(el=>el.addEventListener('input', recalc));

  $('#btnAddTramo').addEventListener('click',(e)=>{
    e.preventDefault();
    addTramo('',0,'','');
  });

  $('#btnClearMap').addEventListener('click', (e)=>{
    e.preventDefault();
    clearMap();
  });

  $('#btnReset').addEventListener('click',(e)=>{
    e.preventDefault();
    list.innerHTML='';
    clearMap();
    addTramo('Tramo 1',0,'','');
    ida.checked=false;
    viajes.value=1;
    tipo.value='camioneta';
    kml.value=9;
    precioLitro.value=1350;
    extra.value=0;
    hRal.value=0;
    lph.value=0.8;
    peajes.value=0;
    // reset botones combustible
    $$('.fuel-btn').forEach((b,i)=>{
      b.classList.toggle('active', i===0);
    });
    recalc();
  });

  $('#btnEjemplo').addEventListener('click', async (e)=>{
    e.preventDefault();
    list.innerHTML='';
    clearMap();
    addTramo('Santiago → Valparaíso',116,'Santiago, Chile','Valparaíso, Chile');
    addTramo('Valparaíso → Rancagua',150,'Valparaíso, Chile','Rancagua, Chile');
    addTramo('Rancagua → Curicó',122,'Rancagua, Chile','Curicó, Chile');
    ida.checked=false;
    viajes.value=1;
    tipo.value='camioneta';
    kml.value=9;
    precioLitro.value=1350;
    extra.value=5;
    hRal.value=0.3;
    lph.value=0.8;
    peajes.value=9200;
    // dejamos 93 activa como referencia
    $$('.fuel-btn').forEach((b,i)=>{
      b.classList.toggle('active', i===0);
    });
    recalc();
  });

  $('#btnCopiar').addEventListener('click', async ()=>{
    const text = [
      `Distancia total: ${outTot.textContent}`,
      `Distancia ajustada: ${outAdj.textContent}`,
      `Litros estimados: ${outL.textContent}`,
      `Combustible: ${outC.textContent}`,
      `Peajes: ${outP.textContent}`,
      `Tiempo estimado total: ${outTime.textContent}`,
      `TOTAL: ${outT.textContent}`
    ].join('\n');
    try{
      await navigator.clipboard.writeText(text);
      alert('Resumen copiado');
    }catch(e){}
  });

  // estado inicial
  initMap();
  initFuelButtons();
  addTramo('Tramo 1',0,'','');
  recalc();
})();
</script>
@endpush
