// INDICADOR 5 — GESTION POR APIARIO 
window.inicializarGraficosGestion = function () {

    const visitas = window.dataVisitas || [];
    const inspecciones = window.dataVisitasInspecciones || [];
    const apiariosData = window.dataApiarios || [];
    const movimientos = window.dataMovimientosColmenas || [];

    // Mapear id_apiario → nombre_apiario para acceso rápido
    const apiarioNameById = Object.fromEntries(
        apiariosData.map(a => [a.id, a.name])
    );

    const getApiarioName = id => apiarioNameById[id] || `Apiario ${id}`;

    /* ========================================================
                    ANALISIS DE MANEJOS POR ESTACION
    ======================================================== */

    const alimentaciones = window.dataEstadoNutricional || [];

    // Contadores agrupados por estacion del año 
    // para varios tipos de actividades (inspecciones, movimientos, etc.)
    const estaciones = {
        Verano: { inspecciones: 0, generales: 0, tratamientos: 0, movimientos: 0, alimentacion: 0 },
        Otoño: { inspecciones: 0, generales: 0, tratamientos: 0, movimientos: 0, alimentacion: 0 },
        Invierno: { inspecciones: 0, generales: 0, tratamientos: 0, movimientos: 0, alimentacion: 0 },
        Primavera: { inspecciones: 0, generales: 0, tratamientos: 0, movimientos: 0, alimentacion: 0 }
    };

    // Clasificar un mes (1–12) dentro de una estación del año
    const getEstacion = mes => {
        if ([12,1,2].includes(mes)) return "Verano";
        if ([3,4,5].includes(mes)) return "Otoño";
        if ([6,7,8].includes(mes)) return "Invierno";
        return "Primavera";
    };

    // Contabilizar por estacion el tipo de visita realizada
    visitas.forEach(v => {
        const fecha = new Date(v.fecha_visita);
        if (isNaN(fecha)) return;

        const est = getEstacion(fecha.getMonth() + 1);

        switch (v.tipo_visita) {
            case "Inspección de Visita":
                estaciones[est].inspecciones++;
                break;
            case "Visita General":
                estaciones[est].generales++;
                break;
            case "Uso de Medicamentos":
                estaciones[est].tratamientos++;
                break;
        }
    });

    // Filtro Movimientos 
    const movAgr = {};
    movimientos.forEach(m => {
        if (!m.fecha_movimiento) return;
        const key = `${m.apiario_origen_id}-${m.apiario_destino_id}-${m.fecha_movimiento}`;
        if (!movAgr[key]) movAgr[key] = m;
    });

    Object.values(movAgr).forEach(m => {
        const fecha = new Date(m.fecha_movimiento);
        const est = getEstacion(fecha.getMonth() + 1);
        estaciones[est].movimientos++;
    });

    //Filtro Alimentacion 
    const alimentacionKeys = new Set();
    alimentaciones.forEach(a => {
        if (!a.fecha_aplicacion || !a.visita_id) return;

        const fecha = new Date(a.fecha_aplicacion);
        const est = getEstacion(fecha.getMonth() + 1);

        const key = `${a.visita_id}-${a.fecha_aplicacion.slice(0,10)}`;
        if (alimentacionKeys.has(key)) return;
        alimentacionKeys.add(key);

        estaciones[est].alimentacion++;
    });

    const estacionesOrden = ["Verano", "Otoño", "Invierno", "Primavera"];

    function crearGraficoLinea(id, titulo, color, data, extra = {}) {

        if (!document.getElementById(id)) return;
    
        const option = {
            title: {
                text: titulo,
                left: "center",
                top: 0,
                textStyle: { fontSize: 16, fontWeight: "bold" }
            },
    
            legend: {
                bottom: 0,
                textStyle: { color: "#64748b", fontSize: 12 },
                data: [extra.legendName || titulo]
            },
    
            tooltip: {
                trigger: "axis",
                borderWidth: 2,
                borderColor: color,
                backgroundColor: "#ffffff",
                textStyle: { color: "#1e293b", fontSize: 13 },
                formatter: extra.tooltipFormatter || (params => {
                    const p = params[0];
                    return `
                        <b>${p.seriesName}</b><br>
                        Estación: ${p.axisValue}<br>
                        Valor: <b>${p.value}</b>
                    `;
                })
            },
    
            grid: { left: '5%', right: '5%', bottom: '12%', top: '18%', containLabel: true },
    
            xAxis: {
                type: "category",
                data: estacionesOrden,
                axisLabel: { color: "#64748b", fontSize: 12 }
            },
    
            yAxis: {
                type: "value",
                axisLabel: { color: "#64748b" },
                splitLine: { lineStyle: { color: "#e2e8f0" } }
            },
    
            series: [{
                name: extra.legendName || titulo,
                type: "line",
                smooth: true,
                showSymbol: true,
                symbol: 'circle',
                symbolSize: 8,
                itemStyle: { color },
                lineStyle: { color, width: 3 },
                areaStyle: { opacity: 0.25, color },
                data,
                label: {
                    show: true,
                    position: "top",
                    fontSize: 12,
                    color: "#1e293b",
                    formatter: extra.labelFormatter || (v => v.value)
                }
            }]
        };
    
        registrarGrafico("gestion-apiario", id, option);
    }

    crearGraficoLinea(
        "inspeccionesPorEstacionChart",
        "Inspecciones por Estación",
        "#2563eb",
        estacionesOrden.map(e => estaciones[e].inspecciones),
        {
            legendName: "Total Inspecciones",
            tooltipFormatter: params => {
                const p = params[0];
                return `
                    <b>Inspecciones</b><br>
                    Estación: ${p.axisValue}<br>
                    Cantidad: <b>${p.value}</b>
                `;
            }
        }
    );
    
    crearGraficoLinea(
        "visitasGeneralesPorEstacionChart",
        "Visitas Generales por Estación",
        "#10b981",
        estacionesOrden.map(e => estaciones[e].generales),
        {
            legendName: "Visitas Generales",
            tooltipFormatter: params => {
                const p = params[0];
                return `
                    <b>Visitas Generales</b><br>
                    Estación: ${p.axisValue}<br>
                    Cantidad: <b>${p.value}</b>
                `;
            }
        }
    );
    
    crearGraficoLinea(
        "medicamentosPorEstacionChart",
        "Tratamientos por Estación",
        "#8b5cf6",
        estacionesOrden.map(e => estaciones[e].tratamientos),
        {
            legendName: "Tratamientos Aplicados",
            tooltipFormatter: params => {
                const p = params[0];
                return `
                    <b>Tratamientos</b><br>
                    Estación: ${p.axisValue}<br>
                    Aplicados: <b>${p.value}</b>
                `;
            }
        }
    );
    
    crearGraficoLinea(
        "movimientosPorEstacionChart",
        "Movimientos por Estación",
        "#f59e0b",
        estacionesOrden.map(e => estaciones[e].movimientos),
        {
            legendName: "Movimientos Realizados",
            tooltipFormatter: params => {
                const p = params[0];
                return `
                    <b>Movimientos</b><br>
                    Estación: ${p.axisValue}<br>
                    Total: <b>${p.value}</b>
                `;
            }
        }
    );
    
    crearGraficoLinea(
        "alimentacionPorEstacionChart",
        "Alimentación por Estación",
        "#ef4444",
        estacionesOrden.map(e => estaciones[e].alimentacion),
        {
            legendName: "Alimentación Total",
            tooltipFormatter: params => {
                const p = params[0];
                return `
                    <b>Alimentación</b><br>
                    Estación: ${p.axisValue}<br>
                    Eventos: <b>${p.value}</b>
                `;
            }
        }
    );

    /* ========================================================
                    PRODUCTIVIDAD POR APIARIO
    ======================================================== */
    // Calcular % de salud del apiario = colmenas activas / colmenas totales
    // Se agrupa por nombre de apiario
    (function () {

        // Mapa visitas → apiario
        const visitasMap = Object.fromEntries(
            (window.dataVisitas || []).map(v => [v.id, v])
        );

        const dataProd = {};

        inspecciones.forEach(i => {
            const visita = visitasMap[i.visita_id];
            if (!visita) return;

            const api = getApiarioName(visita.apiario_id);
            const total = Number(i.num_colmenas_totales || 0);
            const activas = Number(i.num_colmenas_activas || 0);

            if (!dataProd[api]) dataProd[api] = { total: 0, activas: 0 };

            dataProd[api].total += total;
            dataProd[api].activas += activas;
        });

        const labels = Object.keys(dataProd);
        const values = labels.map(api => {
            const d = dataProd[api];
            return d.total > 0 ? Number(((d.activas / d.total) * 100).toFixed(1)) : 0;
        });

        const option = {
            title: {
                text: "Productividad General",
                left: "center",
                top: 0,
                textStyle: { fontSize: 16, fontWeight: "bold" }
            },

            tooltip: {
                trigger: "item",
                borderWidth: 2,
                borderColor: "#10b981",
                formatter: p => `${p.name}<br><b>${p.value}%</b>`
            },

            legend: {
                bottom: 0,
                data: ["% Salud del Apiario"],
                textStyle: { color: "#64748b", fontSize: 12 }
            },

            grid: { left: 60, right: 40, bottom: 60, top: 70 },

            xAxis: {
                type: "category",
                data: labels,
                axisLabel: {
                    color: "#64748b",
                    fontSize: 12,
                    rotate: labels.length > 5 ? 30 : 0
                }
            },

            yAxis: {
                type: "value",
                name: "% Salud",
                nameLocation: "middle",
                nameGap: 45,
                axisLabel: { color: "#64748b" },
                splitLine: { lineStyle: { color: "#e2e8f0" } }
            },

            series: [{
                name: "% Salud del Apiario",
                type: "bar",
                data: values,
                barWidth: "60%",
                itemStyle: {
                    color: "#10b981",
                    borderRadius: [10, 10, 0, 0],
                    shadowBlur: 8,
                    shadowColor: "rgba(0,0,0,0.15)",
                    shadowOffsetY: 5
                },
                label: {
                    show: true,
                    position: "top",
                    fontWeight: "bold",
                    fontSize: 14,
                    formatter: "{c}%"
                }
            }]
        };

        registrarGrafico("gestion-apiario", "productividadColmenaChart", option);

    })();

    /* ========================================================
                    RIESGOS CLIMATICOS (Open-Meteo)
    ======================================================== */
    // Obtener lat/lon de cada apiario usado en inspecciones
    // Consultar Open-Meteo por temperatura/humedad de cada día
    // Calcular índice de riesgo = temp*0.6 + humedad*0.4
    // Graficar temperatura, humedad y riesgo

    (async function () {

        const inspecciones = window.dataVisitasInspecciones || [];
        const visitas = window.dataVisitas || [];
        const apiarios = window.dataApiarios || [];

        const ubicacionApiario = {};
        apiarios.forEach(a => {
            ubicacionApiario[a.id] = {
                lat: Number(a.latitud || 0),
                lon: Number(a.longitud || 0),
            };
        });

        const visitaMap = Object.fromEntries(visitas.map(v => [v.id, v]));

        const filas = inspecciones
            .map(ins => {
                const visita = visitaMap[ins.visita_id];
                if (!visita || !visita.fecha_visita) return null;

                const ubic = ubicacionApiario[visita.apiario_id];
                if (!ubic || !ubic.lat || !ubic.lon) return null;

                return {
                    fecha: visita.fecha_visita.slice(0, 10), 
                    apiario_id: visita.apiario_id,
                    lat: ubic.lat,
                    lon: ubic.lon
                };
            })
            .filter(f => f);

        // Cache para evitar llamar a la API por la misma fecha/ubicacion
        const cacheClima = {};

        async function obtenerClima(lat, lon, fecha) {

            const key = `${lat}-${lon}-${fecha}`;
            if (cacheClima[key]) return cacheClima[key];

            const url = `
                https://api.open-meteo.com/v1/forecast?
                latitude=${lat}&longitude=${lon}
                &hourly=temperature_2m,relative_humidity_2m
                &start_date=${fecha}&end_date=${fecha}
            `.replace(/\s+/g, '');

            try {
                const res = await fetch(url);
                const data = await res.json();

                const temp = data?.hourly?.temperature_2m?.[12] ?? 0;
                const hum = data?.hourly?.relative_humidity_2m?.[12] ?? 0;

                return cacheClima[key] = { temp, humedad: hum };

            } catch (err) {
                console.error("Error Open-Meteo:", err);
                return { temp: 0, humedad: 0 };
            }
        }

        for (const f of filas) {
            const clima = await obtenerClima(f.lat, f.lon, f.fecha);
            f.temp = clima.temp;
            f.humedad = clima.humedad;
            f.riesgo = Math.round((f.temp * 0.6) + (f.humedad * 0.4));
        }

        const fechas = filas.map(f => f.fecha);     
        const temperaturas = filas.map(f => f.temp);
        const humedades = filas.map(f => f.humedad);
        const riesgos = filas.map(f => f.riesgo);

        const option = {
            title: {
                text: "Riesgos Climáticos",
                left: "center",
                top: 0,
                textStyle: { fontSize: 16, fontWeight: "bold" }
            },

            tooltip: {
                trigger: "axis",
                borderWidth: 2,
                borderColor: "#3b82f6",
                backgroundColor: "#ffffff",
                textStyle: { color: "#1e293b" }
            },

            legend: {
                top: 40,            
                icon: "circle",
                textStyle: { color: "#64748b" },
                data: ["Temperatura (°C)", "Humedad (%)", "Índice de Riesgo"]
            },

            grid: { left: 50, right: 50, top: 80, bottom: 80 },

            xAxis: {
                type: "category",
                data: fechas,
                axisLabel: { rotate: 40, color: "#64748b" }
            },

            yAxis: {
                type: "value",
                axisLabel: { color: "#64748b" }
            },

            series: [
                {
                    name: "Temperatura (°C)",
                    type: "line",
                    smooth: true,
                    symbol: "circle",
                    symbolSize: 8,
                    itemStyle: { color: "#ef4444" },
                    lineStyle: { width: 3, color: "#ef4444" },
                    data: temperaturas,
                    label: { show: true, position: "top", color: "#ef4444" }   
                },
                {
                    name: "Humedad (%)",
                    type: "line",
                    smooth: true,
                    symbol: "circle",
                    symbolSize: 8,
                    itemStyle: { color: "#0ea5e9" },
                    lineStyle: { width: 3, color: "#0ea5e9" },
                    data: humedades,
                    label: { show: true, position: "top", color: "#0ea5e9" }  
                },
                {
                    name: "Índice de Riesgo",
                    type: "line",
                    smooth: true,
                    symbol: "circle",
                    symbolSize: 8,
                    itemStyle: { color: "#d97706" },
                    lineStyle: { width: 3, color: "#d97706", type: "dashed" },
                    data: riesgos,
                    label: { show: true, position: "top", color: "#d97706" }   
                }
            ]
        };

        registrarGrafico("gestion-apiario", "riesgosClimaticosChart", option);

    })();

};
