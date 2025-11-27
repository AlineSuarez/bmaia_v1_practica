
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
