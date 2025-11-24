// INDICADOR 2 - PRODUCTIVIDAD POR APIARIO
window.inicializarGraficosPorApiario = function () {

    const visitas       = window.dataVisitas || [];
    const inspecciones  = window.dataVisitasInspecciones || [];
    const apiariosData  = window.dataApiarios || [];

    const apiarioNameById = Object.fromEntries(
        apiariosData.map(a => [a.id, a.name])
    );

    visitas.forEach(v => {
        if (v.apiario_id && v.apiario && !apiarioNameById[v.apiario_id]) {
            apiarioNameById[v.apiario_id] = v.apiario;
        }
    });

    const getApiarioNameById = (id) =>
        apiarioNameById[id] || (id ? `Apiario ${id}` : "Sin nombre");

    const getApiarioNameFromVisita = (visita) => {
        if (!visita) return "Sin nombre";
        if (visita.apiario_id) return getApiarioNameById(visita.apiario_id);
        if (visita.apiario)    return visita.apiario;
        return "Sin nombre";
    };

    // Relacionar inspecciones -> visita
    const mapaVisitas = Object.fromEntries(visitas.map(v => [v.id, v]));
    inspecciones.forEach(ins => { ins.visita = mapaVisitas[ins.visita_id] || null; });

    /* ========================================================
                    DISTRIBUCION ESTADO COLMENAS 
    ======================================================== */
    (function () {

        const data = {};
        inspecciones.forEach(ins => {
            const apiario = getApiarioNameFromVisita(ins.visita);
            if (!data[apiario]) data[apiario] = { activas: 0, enfermas: 0, muertas: 0 };

            data[apiario].activas += Number(ins.num_colmenas_activas || 0);
            data[apiario].enfermas += Number(ins.num_colmenas_enfermas || 0);
            data[apiario].muertas += Number(ins.num_colmenas_muertas || 0);
        });

        const apiarios = Object.keys(data);

        const activas  = apiarios.map(a => data[a].activas);
        const enfermas = apiarios.map(a => data[a].enfermas);
        const muertas  = apiarios.map(a => data[a].muertas);

        const option = {
            title: {
                text: "Estado de Colmenas por Inspección",
                left: "center",
                top: 0,
                textStyle: { fontSize: 16, fontWeight: "bold" }
            },

            tooltip: {
                trigger: 'axis',
                axisPointer: { type: 'shadow' },
                borderWidth: 2,
                borderColor: '#10b981',
            },

            legend: {
                bottom: 0,
                textStyle: { color: '#64748b', fontSize: 11 }
            },

            grid: { left: '8%', right: '5%', bottom: '15%', top: '18%', containLabel: true },

            xAxis: {
                type: 'category',
                data: apiarios,
                axisLabel: {
                    color: '#64748b',
                    fontSize: 10,
                    rotate: apiarios.length > 5 ? 45 : 0,
                    formatter: v => v.length > 12 ? v.slice(0, 12) + '…' : v
                }
            },

            yAxis: {
                type: 'value',
                axisLabel: { color: '#64748b', fontSize: 12 },
                splitLine: { lineStyle: { color: '#e2e8f0' } },
                name: "Colmenas",
            },

            series: [
                {
                    name: 'Activas',
                    type: 'bar',
                    stack: 'total',
                    barWidth: '60%',
                    itemStyle: {
                        color: '#10b981',
                        borderRadius: [8, 8, 8, 8]
                    },
                    label: {
                        show: true,
                        position: 'inside',
                        fontSize: 11,
                        color: '#1e293b',
                        fontWeight: 'bold',
                        formatter: p => p.value > 0 ? p.value : ''
                    },
                    data: activas
                },
                {
                    name: 'Enfermas',
                    type: 'bar',
                    stack: 'total',
                    barWidth: '60%',
                    itemStyle: {
                        color: '#64748b',
                        borderRadius: [8, 8, 8, 8]
                    },
                    label: {
                        show: true,
                        position: 'inside',
                        fontSize: 11,
                        color: '#1e293b',
                        fontWeight: 'bold',
                        formatter: p => p.value > 0 ? p.value : ''
                    },
                    data: enfermas
                },
                {
                    name: 'Muertas',
                    type: 'bar',
                    stack: 'total',
                    barWidth: '60%',
                    itemStyle: {
                        color: '#ef4444',
                        borderRadius: [8, 8, 8, 8]
                    },
                    label: {
                        show: true,
                        position: 'inside',
                        fontSize: 11,
                        color: '#1e293b',
                        fontWeight: 'bold',
                        formatter: p => p.value > 0 ? p.value : ''
                    },
                    data: muertas
                }
            ],

            animationEasing: 'elasticOut',
            animationDuration: 1500
        };

        registrarGrafico("por-apiario", "colmenasPorEstadoChart", option);
    })();

    /* ========================================================
                    INDICE DE MORTALIDAD
    ======================================================== */
    (function () {

        const fechas = inspecciones.map(i => i.created_at);
        const mortalidad = inspecciones.map(i =>
            i.num_colmenas_totales
                ? ((i.num_colmenas_muertas / i.num_colmenas_totales) * 100).toFixed(2)
                : 0
        );

        const option = {
            title: {
                text: "Índice de Mortalidad de Colmenas (%)",
                left: "center",
                top: 0,
                textStyle: { fontSize: 16, fontWeight: "bold" }
            },

            legend: {
                bottom: 0,
                data: ["Índice de Mortalidad"],
                textStyle: { color: "#1e293b" }
            },

            tooltip: {
                trigger: 'axis',
                borderWidth: 2,
                borderColor: '#ef4444',
                formatter: params => {
                    const p = params[0];
                    return `
                        <span style="display:inline-block;margin-right:4px;
                            width:10px;height:10px;border-radius:50%;
                            background:#ef4444"></span>
                        <b>Índice de Mortalidad</b><br>
                        ${p.axisValue}<br>
                        Mortalidad: <b>${p.value}%</b>
                    `;
                }
            },

            grid: { left: '5%', right: '5%', bottom: '12%', top: '18%', containLabel: true },

            xAxis: {
                type: 'category',
                data: fechas,
                axisLabel: { color: '#64748b', rotate: 45 }
            },

            yAxis: {
                type: 'value',
                max: 100,
                axisLabel: { color: '#64748b', formatter: v => v + '%' },
                splitLine: { lineStyle: { color: '#e2e8f0' } }
            },

            series: [{
                name: 'Índice de Mortalidad',
                type: 'line',
                smooth: true,
                data: mortalidad,
                symbol: 'circle',
                symbolSize: 8,
                itemStyle: { color: '#ef4444' },
                lineStyle: { width: 3, color: '#ef4444' },
                areaStyle: { color: 'rgba(239,68,68,0.15)' },
                label: {
                    show: true,
                    position: 'top',
                    fontSize: 12,
                    color: '#1e293b',
                    formatter: v => v.value + '%'
                }
            }],

            animationDuration: 1500
        };

        registrarGrafico("por-apiario", "indiceMortalidadChart", option);
    })();


    /* ========================================================
                COLMENAS MUERTAS POR INSPECCION
    ======================================================== */
    (function () {

        const fechas  = inspecciones.map(i => i.created_at);
        const muertas = inspecciones.map(i => Number(i.num_colmenas_muertas || 0));

        const option = {
            title: {
                text: "Colmenas Muertas por Inspección",
                left: "center",
                top: 0,
                textStyle: { fontSize: 16, fontWeight: "bold" }
            },

            tooltip: {
                trigger: 'axis',
                borderWidth: 2,
                borderColor: '#ef4444',
                axisPointer: { type: 'shadow' }
            },

            legend : { bottom: 0, textStyle: { color: "#64748b", fontSize: 11 } },

            grid: { left: '5%', right: '5%', bottom: '12%', top: '18%', containLabel: true },

            xAxis: {
                type: 'category',
                data: fechas,
                axisLabel: { color: '#64748b', rotate: 45 }
            },

            yAxis: {
                type: 'value',
                axisLabel: { color: '#64748b' },
                splitLine: { lineStyle: { color: '#e2e8f0' } },
                name: "Colmenas",
            },

            series: [{
                name: 'Muertas',
                type: 'bar',
                data: muertas,
                barWidth: '70%',
                itemStyle: {
                    color: '#ef4444',
                    borderRadius: [10, 10, 0, 0],
                    shadowBlur: 8,
                    shadowColor: 'rgba(0,0,0,0.15)',
                    shadowOffsetY: 4
                },
                label: {
                    show: true,
                    position: 'top',
                    fontSize: 14,
                    color: '#1e293b',
                    fontWeight: 'bold',
                    formatter: v => v.value
                }
            }]
        };

        registrarGrafico("por-apiario", "tasaMortalidadChart", option);
    })();

    /* ========================================================
                    TASA COLMENAS ENFERMAS
    ======================================================== */
    (function () {

        const fechas = inspecciones.map(i => i.created_at);
        const enfermas = inspecciones.map(i =>
            i.num_colmenas_totales
                ? ((i.num_colmenas_enfermas / i.num_colmenas_totales) * 100).toFixed(2)
                : 0
        );

        const option = {
            title: {
                text: "Tasa de Colmenas Enfermas (%)",
                left: "center",
                top: 0,
                textStyle: { fontSize: 16, fontWeight: "bold" }
            },

            legend: {
                bottom: 0,
                data: ["% Colmenas Enfermas"],
                textStyle: { color: "#1e293b" }
            },

            tooltip: {
                trigger: 'axis',
                borderWidth: 2,
                borderColor: '#f59e0b',
                formatter: params => {
                    const p = params[0];
                    return `
                        <span style="display:inline-block;margin-right:4px;
                            width:10px;height:10px;border-radius:50%;
                            background:#f59e0b"></span>
                        <b>Tasa Colmenas Enfermas</b><br>
                        ${p.axisValue}<br>
                        Enfermas: <b>${p.value}%</b>
                    `;
                }
            },

            grid: { left: '5%', right: '5%', bottom: '12%', top: '18%', containLabel: true },

            xAxis: {
                type: 'category',
                data: fechas,
                axisLabel: { color: '#64748b', rotate: 45 }
            },

            yAxis: {
                type: 'value',
                max: 100,
                axisLabel: { color: '#64748b', formatter: v => v + '%' },
                splitLine: { lineStyle: { color: '#e2e8f0' } }
            },

            series: [{
                name: '% Colmenas Enfermas',
                type: 'line',
                smooth: true,
                data: enfermas,
                symbol: 'circle',
                symbolSize: 8,
                itemStyle: { color: '#f59e0b' },
                lineStyle: { width: 3, color: '#f59e0b' },
                areaStyle: { color: 'rgba(245,158,11,0.15)' },
                label: {
                    show: true,
                    position: 'top',
                    fontSize: 12,
                    color: '#1e293b',
                    formatter: v => v.value + '%'
                }
            }]
        };

        registrarGrafico("por-apiario", "tasaEnfermasChart", option);
    })();

    /* ========================================================
                    TIPOS DE VISITAS POR APIARIO 
    ======================================================== */
    (function () {

        const visitasPorApiarioYTipo = {};
        const tiposVisita = new Set();

        visitas.forEach(v => {
            const apiario = v.apiario_id ? getApiarioNameById(v.apiario_id) : (v.apiario || "Sin nombre");
            const tipo    = v.tipo_visita || "Sin especificar";
            tiposVisita.add(tipo);

            if (!visitasPorApiarioYTipo[apiario]) visitasPorApiarioYTipo[apiario] = {};
            visitasPorApiarioYTipo[apiario][tipo] =
                (visitasPorApiarioYTipo[apiario][tipo] || 0) + 1;
        });

        const apiarios = Object.keys(visitasPorApiarioYTipo);

        const tiposArray = Array.from(tiposVisita);

        const coloresTipos = {
            'Visita General'        : '#f59e0b',
            'Inspección de Visita'  : '#3b82f6',
            'Uso de Medicamentos'   : '#10b981',
            'Alimentación'          : '#ec4899',
            'Inspección de Reina'   : '#8b5cf6',
            'Sin especificar'       : '#94a3b8'
        };

        const series = tiposArray.map(tipo => ({
            name: tipo,
            type: 'bar',
            stack: 'total',
            barWidth: '60%',
            itemStyle: {
                color: coloresTipos[tipo] || '#6366f1',
                borderRadius: [6, 6, 6, 6],
                shadowBlur: 8,
                shadowColor: 'rgba(0,0,0,0.15)',
                shadowOffsetY: 4
            },
            label: {
                show: true,
                fontSize: 14,
                fontWeight: 'bold',
                color: '#1e293b',
                formatter: p => p.value > 0 ? p.value : ''
            },
            data: apiarios.map(a => visitasPorApiarioYTipo[a][tipo] || 0)
        }));

        const option = {
            title: {
                text: "Tipos de Visitas por Apiario",
                left: "center",
                top: 0,
                textStyle: { fontSize: 16, fontWeight: "bold" }
            },

            tooltip: {
                trigger: 'axis',
                axisPointer: { type: 'shadow' },
                borderWidth: 2,
                borderColor: '#3b82f6'
            },

            legend: {
                data: tiposArray,
                bottom: 0,
                textStyle: { color: '#64748b', fontSize: 11 }
            },

            grid: { left: '5%', right: '5%', bottom: '15%', top: '12%', containLabel: true },

            xAxis: {
                type: 'value',
                axisLabel: { color: '#64748b' },
                splitLine: { lineStyle: { color: '#e2e8f0' } }
            },

            yAxis: {
                type: 'category',
                data: apiarios,
                axisLabel: {
                    color: '#64748b',
                    fontSize: 11
                }
            },

            series
        };

        registrarGrafico("por-apiario", "tipoVisitasPorApiarioChart", option);
    })();

    /* ========================================================
                TIPOS DE TRATAMIENTOS POR APIARIO
    ======================================================== */
    (function () {

        // Filtro de visitas con uso de medicamentos
        const tratamientos = visitas.filter(v => v.tipo_visita === "Uso de Medicamentos");

        const mapa = {};

        tratamientos.forEach(v => {
            const apiario = v.apiario_id
                ? getApiarioNameById(v.apiario_id)
                : (v.apiario || "Sin nombre");

            const tipo = v.motivo_tratamiento || v.motivo || "Sin tipo";

            if (!mapa[apiario]) mapa[apiario] = {};
            mapa[apiario][tipo] = (mapa[apiario][tipo] || 0) + 1;
        });

        const apiarios = Object.keys(mapa);

        const tiposTratamientos = [...new Set(
            tratamientos.map(v => v.motivo_tratamiento || v.motivo || "Sin tipo")
        )];

        const palette = [
            "#8b5cf6","#10b981","#ec4899","#f59e0b",
            "#14b8a6","#f97316","#6366f1",
        ];

        const series = tiposTratamientos.map((tipo, i) => ({
            name: tipo,
            type: 'bar',
            stack: 'total',
            barWidth: '70%',
            itemStyle: {
                color: palette[i % palette.length],
                borderRadius: [8, 8, 8, 8],
                shadowBlur: 8,
                shadowColor: 'rgba(0,0,0,0.15)',
                shadowOffsetY: 4
            },
            label: {
                show: true,
                fontWeight: 'bold',
                position: 'inside',
                fontSize: 14,
                color: '#1e293b',
                formatter: p => p.value > 0 ? p.value : ''
            },
            data: apiarios.map(a => mapa[a][tipo] || 0)
        }));

        const option = {
            title: {
                text: "Tipos de Tratamientos por Apiario",
                left: "center",
                top: 0,
                textStyle: { fontSize: 16, fontWeight: "bold" }
            },

            tooltip: {
                trigger: 'axis',
                axisPointer: { type: 'shadow' },
                borderWidth: 2,
                borderColor: '#10b981'
            },

            legend: {
                bottom: 0,
                textStyle: { color: '#64748b', fontSize: 11 }
            },

            grid: { left: '8%', right: '8%', bottom: '10%', top: '18%', containLabel: true },

            xAxis: {
                type: 'category',
                data: apiarios,
                axisLabel: {
                    color: '#64748b',
                    rotate: apiarios.length > 5 ? 30 : 0
                }
            },

            yAxis: {
                type: 'value',
                axisLabel: { color: '#64748b' },
                splitLine: { lineStyle: { color: '#e2e8f0' } }
            },

            series
        };

        registrarGrafico("por-apiario", "tipoTratamientoPorApiarioChart", option);

    })();

    /* ========================================================
                    TRATAMIENTOS CONTRA VARROA 
    ======================================================== */
    (function () {

        const registros = window.dataPresenciaVarroa || [];

        const varroa = registros.filter(r =>
            r.tratamiento &&
            r.fecha_aplicacion
        );

        const mapa = {};
        const fechasSet = new Set();
        const tratamientosSet = new Set();

        varroa.forEach(r => {
            // Normalizar fecha a solo YYYY-MM-DD
            const fecha = (r.fecha_aplicacion || '').slice(0, 10) || 'Sin fecha';
            const tratamiento = r.tratamiento || 'Sin tratamiento';

            fechasSet.add(fecha);
            tratamientosSet.add(tratamiento);

            if (!mapa[fecha]) mapa[fecha] = {};
            if (!mapa[fecha][tratamiento]) mapa[fecha][tratamiento] = new Set();

            // Uso de colmena + visita para evitar doble conteo
            const claveColmena = `${r.colmena_id || ''}-${r.visita_id || ''}`;
            mapa[fecha][tratamiento].add(claveColmena);
        });

        const fechas = Array.from(fechasSet).sort();           
        const tratamientos = Array.from(tratamientosSet);     

        const palette = [
            "#3b82f6","#10b981","#ef4444","#f59e0b","#8b5cf6",
            "#14b8a6","#f97316","#6366f1","#84cc16","#ec4899",
            "#64748b","#0ea5e9"
        ];

        const series = tratamientos.map((trat, i) => ({
            name: trat,
            type: 'bar',
            stack: 'total',
            barWidth: '70%',
            itemStyle: {
                color: palette[i % palette.length],
                borderRadius: [6, 6, 0, 0],
                shadowBlur: 8,
                shadowColor: 'rgba(0,0,0,0.25)',
                shadowOffsetY: 4
            },
            label: {
                show: true,
                position: 'top',
                fontSize: 14,
                fontWeight: 'bold',
                color: '#1e293b',
                formatter: p => p.value > 0 ? p.value : ''
            },
            data: fechas.map(f => {
                const setColmenas =
                    mapa[f] && mapa[f][trat]
                        ? mapa[f][trat]
                        : null;
                return setColmenas ? setColmenas.size : 0;
            })
        }));

        const option = {
            title: {
                text: "Tratamientos contra Varroa",
                left: "center",
                top: 0,
                textStyle: { fontSize: 16, fontWeight: "bold" }
            },

            tooltip: {
                trigger: 'axis',
                axisPointer: { type: 'shadow' },
                borderWidth: 2,
                borderColor: '#3b82f6',
                formatter: params => {
                    let total = 0;
                    params.forEach(p => total += p.value || 0);
                    let html = `<b>${params[0].axisValue}</b><br/>`;
                    params.forEach(p => {
                        if (p.value > 0) {
                            html += `${p.marker} ${p.seriesName}: <b>${p.value}</b> colmenas<br/>`;
                        }
                    });
                    return html;
                }
            },

            legend: {
                bottom: 0,
                textStyle: { color: '#64748b', fontSize: 11 }
            },

            grid: { left: '8%', right: '8%', bottom: '10%', top: '18%', containLabel: true },

            xAxis: {
                type: 'category',
                data: fechas,
                axisLabel: {
                    color: '#64748b',
                    rotate: 45,
                }
            },

            yAxis: {
                type: 'value',
                name: 'Colmenas tratadas',
                nameLocation: 'middle',
                nameGap: 45,
                axisLabel: { color: '#64748b' },
                splitLine: { lineStyle: { color: '#e2e8f0' } }
            },

            series
        };

        registrarGrafico("por-apiario", "tratamientosVarroaChart", option);

    })();

    /* ========================================================
                    PERIODO DE RESGUARDO
    ======================================================== */
    (function () {

        const registros = window.dataPresenciaVarroa || [];

        /* Formato corto mes + día */
        function md(date) {
            return new Date(date).toLocaleDateString("es-ES", {
                month: "short",
                day: "numeric"
            });
        }

        const filas = [];

        registros.forEach(r => {

            const tratamiento = r.tratamiento || "Tratamiento";

            const inicio = new Date(r.fecha_aplicacion).getTime();
            if (isNaN(inicio)) return;

            const dias = Number(r.periodo_carencia);

            const fin = inicio + dias * 86400000;

            filas.push({
                tratamiento,
                start: inicio,
                end: fin,
                label: `${md(inicio)} → ${md(fin)}`
            });
        });

        const tratamientos = [...new Set(filas.map(f => f.tratamiento))];

        const data = filas.map(f => [
            tratamientos.indexOf(f.tratamiento), 
            f.start,
            f.end,
            f.label,
            f.tratamiento
        ]);

        const option = {
            title: {
                text: "Periodo de Resguardo Sanitario",
                left: "center",
                top: 5,
                textStyle: { fontSize: 16, fontWeight: "bold" }
            },

            legend: {
                bottom: 0,
                data: ["Periodo de Resguardo"],
                textStyle: { color: "#1e293b" }
            },

            grid: { left: "16%", right: "5%", top: "12%", bottom: "18%" },

            tooltip: {
                borderWidth: 2,
                borderColor: "#3b82f6",
                formatter: p => `
                    <b>${p.value[4]}</b><br>
                    ${md(p.value[1])} → ${md(p.value[2])}
                `
            },

            xAxis: {
                type: "time",
                axisLabel: { color: "#64748b" }
            },

            yAxis: {
                type: "category",
                data: tratamientos,

                axisLabel: {
                    color: "#1e293b",
                    fontSize: 13,
                    fontWeight: "bold",

                    /* mostrar solo ultima palabra */
                    formatter: v => {
                        const parts = v.split(" ");
                        return parts.length > 1 ? parts.slice(1).join(" ") : parts[0];
                    }
                }
            },

            series: [{
                name: "Periodo de Resguardo",
                type: "custom",
                encode: { x: [1, 2], y: 0 },
                data,

                renderItem(params, api) {
                    const cat = api.value(0);
                    const start = api.value(1);
                    const end = api.value(2);
                    const label = api.value(3);

                    const s = api.coord([start, cat]);
                    const e = api.coord([end, cat]);

                    const width = Math.max(6, e[0] - s[0]);
                    const height = 26;

                    return {
                        type: "group",
                        children: [
                            {
                                type: "rect",
                                shape: {
                                    x: s[0],
                                    y: s[1] - height / 2,
                                    width,
                                    height
                                },
                                style: {
                                    fill: "#3b82f6",
                                    stroke: "#1e3a8a",
                                    lineWidth: 1.5
                                }
                            },
                            {
                                type: "text",
                                style: {
                                    x: s[0] + width / 2,
                                    y: s[1],
                                    text: label,
                                    fontSize: 12,
                                    fontWeight: 500,
                                    textAlign: "center",      
                                    textVerticalAlign: "middle" 
                                }
                            }
                        ]
                    };
                }
            }]
        };

        registrarGrafico("por-apiario", "resguardoSanitarioChart", option);

    })();

    /* ========================================================
                    NUTRICION POR APIARIO 
    ======================================================== */
    (function () {

        const registros = window.dataEstadoNutricional || [];

        const mapa = {};

        registros.forEach(r => {

            const fecha = new Date(r.fecha_aplicacion);
            const mes = fecha.toLocaleString("es-ES", { month: "short", year: "numeric" });

            if (!mapa[mes]) mapa[mes] = {};
            mapa[mes][r.objetivo] = (mapa[mes][r.objetivo] || 0) + 1;
        });

        const meses = Object.keys(mapa);
        const objetivos = [...new Set(registros.map(r => r.objetivo))];

        const palette = ["#3b82f6","#10b981","#f59e0b","#ef4444","#8b5cf6"];

        const series = objetivos.map((obj, i) => ({
            name: obj,
            type: "bar",
            stack: "total",
            barWidth: '60%',
            itemStyle: {
                color: palette[i % palette.length],
                borderRadius: [6, 6, 6, 6],
                shadowBlur: 8,
                shadowColor: 'rgba(20,0,0,0.15)',
                shadowOffsetY: 4
            },
            label: {
                show: true,
                position: 'inside',
                fontSize: 14,
                fontWeight: 'bold',
                color: '#1e293b',
                formatter: p => p.value > 0 ? p.value : ''
            },
            data: meses.map(m => mapa[m][obj] || 0)
        }));

        const option = {
            title: { 
                text: "Nutrición por Apiario", 
                left: "center",
                top: 0,
                textStyle: { fontSize: 16, fontWeight: "bold" }
            },

            tooltip: { 
                trigger: "axis",
                axisPointer: { type: 'shadow' },
                borderWidth: 2,
                borderColor: '#10b981',
            },

            legend: {
                bottom: 0,
                textStyle: { color: '#64748b', fontSize: 11 }
            },

            grid: { left: '8%', right: '8%', bottom: '10%', top: '12%', containLabel: true },

            xAxis: { type: "category", data: meses },

            yAxis: {
                type: "value",
                name: "Colmenas",
                axisLabel: { color: "#64748b" },
                splitLine: { lineStyle: { color: "#e2e8f0" } }
            },

            series
        };

        registrarGrafico("por-apiario", "nutricionPorApiarioChart", option);

    })();

    /* ========================================================
                TIPO DE ALIMENTO UTILIZADO POR APIARIO 
    ======================================================== */
    (function () {

        const registros = window.dataEstadoNutricional || [];

        // Extrae el numero desde dosificacion
        function extraerNumero(texto) {
            if (!texto) return 0;
            const match = texto.toString().match(/[\d.]+/);
            return match ? Number(match[0]) : 0;
        }

        const mapa = {};

        // Para evitar duplicados por colmena
        const visitKeys = new Set();

        registros.forEach(r => {

            const fecha = (r.fecha_aplicacion || '').slice(0, 10);

            // Clave unica de visita + fecha + tipo
            const key = `${r.visita_id}-${fecha}-${r.tipo_alimentacion}`;

            // Evitar procesar multiples colmenas del mismo tratamiento
            if (visitKeys.has(key)) return;
            visitKeys.add(key);

            if (!mapa[fecha]) mapa[fecha] = {};

            // Extraer dosificacion
            const cantidad = extraerNumero(r.dosificacion || r.dosifiacion);

            mapa[fecha][r.tipo_alimentacion] =
                (mapa[fecha][r.tipo_alimentacion] || 0) + cantidad;
        });

        const fechas = Object.keys(mapa).sort();
        const insumos = [...new Set(registros.map(r => r.tipo_alimentacion))];

        const palette = ["#10b981","#3b82f6","#f59e0b","#ef4444","#8b5cf6"];

        const series = insumos.map((insumo, i) => ({
            name: insumo,
            type: "bar",
            stack: "total",
            barWidth: '70%',
            itemStyle: {
                color: palette[i % palette.length],
                borderRadius: [10, 10, 0, 0],
                shadowBlur: 8,
                shadowColor: 'rgba(0,0,0,0.15)',
                shadowOffsetY: 4
            },
            label: {
                show: true,
                position: 'inside',
                fontSize: 14,
                fontWeight: 'bold',
                color: '#1e293b',
                formatter: p => p.value > 0 ? p.value : ''
            },
            data: fechas.map(f => mapa[f][insumo] || 0)
        }));

        const option = {
            title: { 
                text: "Tipo de Alimento Utilizado", 
                left: "center",
                top: 0,
                textStyle: { fontSize: 16, fontWeight: "bold" }
            },

            tooltip: { 
                trigger: "axis",
                axisPointer: { type: 'shadow' },
                borderWidth: 2,
                borderColor: '#10b981',
            },

            legend: {
                bottom: 0,
                textStyle: { color: '#64748b', fontSize: 11 }
            },

            grid: { left: '8%', right: '8%', bottom: '10%', top: '12%', containLabel: true },

            xAxis: {
                type: "category",
                data: fechas,
                axisLabel: { rotate: fechas.length > 6 ? 35 : 0 }
            },

            yAxis: {
                type: "value",
                name: "Cantidad de insumo",
                nameLocation: "middle",
                nameGap: 40
            },

            series
        };

        registrarGrafico("por-apiario", "tipoAlimentoPorApiarioChart", option);

    })();
    
};