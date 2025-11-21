// INDICADOR 4 - MOVIMIENTO DE COLMENAS
window.inicializarGraficosMovimiento = function () {

    const data = window.dataMovimientosColmenas || [];
    const apiarios = window.dataApiarios || [];

    const apiarioMap = {};
    apiarios.forEach(a => { apiarioMap[a.id] = a.name; });

    /* PALETA GENERAL */
    const palette = [
        "#3b82f6","#10b981","#ef4444","#f59e0b","#8b5cf6",
        "#14b8a6","#f97316","#6366f1","#84cc16","#ec4899",
        "#64748b","#0ea5e9"
    ];

    /* ========================================================
                        MOTIVO DE MOVIMIENTO
    ======================================================== */
    (function () {

        const motivos = [...new Set(data.map(d => d.motivo_movimiento || "Sin motivo"))];
        const motivoCounts = motivos.map(
            m => data.filter(x => (x.motivo_movimiento || "Sin motivo") === m).length
        );

        const option = {
            title: {
                text: "Motivos de Movimiento",
                left: "center",
                top: 0,
                textStyle: { fontSize: 16, fontWeight: "bold" }
            },

            tooltip: {
                trigger: "item",
                borderWidth: 2,
                textStyle: { fontSize: 14 },
            },

            legend: {
                bottom: 0,
                icon: "circle",
                textStyle: { color: "#64748b", fontSize: 12 }
            },

            series: [{
                type: "pie",
                radius: ["35%", "65%"],
                padAngle: 5,
                minAngle: 8,
                itemStyle: {
                    borderRadius: 8,
                    borderColor: "#fff",
                    borderWidth: 2
                },
                labelLine: { show: true, length: 15, length2: 10, smooth: true },
                label: {
                    show: true,
                    formatter: "{d}%",
                    fontWeight: "bold",
                    fontSize: 14,
                    color: "#1e293b"
                },
                data: motivos.map((m, i) => ({
                    name: m,
                    value: motivoCounts[i],
                    itemStyle: { color: palette[i % palette.length] }
                }))
            }]
        };

        registrarGrafico("movimiento-colmenas", "movimientosMotivoChart", option);
    })();

    /* ========================================================
                COLMENAS TRANSPORTADAS POR APIARIO
    ======================================================== */
    (function () {

        const destinos = [...new Set(data.map(m => m.apiario_destino_id))];
        const counts = destinos.map(id => data.filter(m => m.apiario_destino_id === id).length);

        const option = {
            title: {
                text: "Cantidad de Colmenas Transportadas",
                left: "center",
                top: 0,
                textStyle: { fontSize: 16, fontWeight: "bold" }
            },

            tooltip: {
                trigger: "axis",
                borderWidth: 2,
                borderColor: "#10b981",
                axisPointer: { type: "shadow" }
            },

            legend: {
                bottom: 0,
                data: ["Colmenas transportadas"],
                textStyle: { color: "#64748b" }
            },

            grid: { left: "8%", right: "8%", bottom: "10%", top: "18%", containLabel: true },

            xAxis: {
                type: "category",
                data: destinos.map(id => apiarioMap[id] ?? `Apiario ${id}`),
                axisLabel: {
                    color: "#64748b",
                    rotate: destinos.length > 6 ? 30 : 0
                }
            },

            yAxis: {
                type: "value",
                axisLabel: { color: "#64748b" },
                splitLine: { lineStyle: { color: "#e2e8f0" } }
            },

            series: [{
                name: "Colmenas transportadas",
                type: "bar",
                data: counts,
                barWidth: "70%",
                itemStyle: {
                    color: params => palette[params.dataIndex % palette.length],
                    borderRadius: [10, 10, 0, 0],
                    shadowBlur: 8,
                    shadowColor: "rgba(0,0,0,0.15)",
                    shadowOffsetY: 4
                },
                label: {
                    show: true,
                    position: "top",
                    fontSize: 14,
                    fontWeight: "bold",
                }
            }]
        };

        registrarGrafico("movimiento-colmenas", "colmenasTransportadasChart", option);
    })();

    /* ========================================================
                    EFICIENCIA DEL MOVIMIENTO
    ======================================================== */

    function haversine(lat1, lon1, lat2, lon2) {
        const R = 6371, toRad = d => d * Math.PI / 180;
        const dLat = toRad(lat2 - lat1), dLon = toRad(lon2 - lon1);
        const a = Math.sin(dLat / 2) ** 2 +
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
            Math.sin(dLon / 2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    (function () {

        const grouped = {};

        data.filter(m => m.fecha_inicio_mov && m.fecha_termino_mov).forEach(m => {

            const sd = new Date(m.fecha_inicio_mov);
            const ed = new Date(m.fecha_termino_mov);
            const dias = Math.max(1, Math.ceil((ed - sd) / 86400000));
            const motivo = m.motivo_movimiento || "Sin motivo";

            // Filtro para agrupar colmenas por movimientos
            const key = `${m.apiario_destino_id}-${motivo}-${m.fecha_inicio_mov}-${m.fecha_termino_mov}`;

            if (!grouped[key]) {

                const aO = apiarios.find(a => a.id == m.apiario_origen_id);
                const aD = apiarios.find(a => a.id == m.apiario_destino_id);

                let distancia = null;
                if (aO && aD && aO.latitud && aO.longitud && aD.latitud && aD.longitud) {
                    distancia = haversine(aO.latitud, aO.longitud, aD.latitud, aD.longitud);
                }

                grouped[key] = {
                    apiario: apiarioMap[m.apiario_destino_id] ?? `Apiario ${m.apiario_destino_id}`,
                    dias,
                    motivo,
                    distancia,
                    colmenas: 0
                };
            }

            grouped[key].colmenas++;
        });

        const bubbleData = Object.values(grouped);

        const coloresMotivo = {
            "Polinización": "#3b82f6",
            "Producción": "#10b981",
        };

        const maxCols = Math.max(...bubbleData.map(d => d.colmenas), 1);
        const sizeFor = c => 10 + (c / maxCols) * 40;

        const series = Object.keys(coloresMotivo).map(motivo => ({
            name: motivo,
            type: "scatter",
            data: bubbleData.filter(d => d.motivo === motivo).map(d => ({
                value: [d.dias, d.apiario],
                ...d,
                symbolSize: sizeFor(d.colmenas)
            })),
            itemStyle: { color: coloresMotivo[motivo] },
            label: {
                show: true,
                formatter: p => p.data.distancia != null ? `${p.data.distancia.toFixed(1)} km` : "",
                position: "top",
                fontSize: 11,
                color: "#334155"
            }
        }));

        const option = {
            title: {
                text: "Eficiencia en Movimiento de Colmenas",
                left: "center",
                top: 0,
                textStyle: { fontSize: 16, fontWeight: "bold" }
            },

            tooltip: {
                borderWidth: 2,
                borderColor: "#3b82f6",
                formatter: p => `
                    <b>${p.data.apiario}</b><br>
                    Motivo: ${p.data.motivo}<br>
                    Días: ${p.data.dias}<br>
                    Colmenas: ${p.data.colmenas}<br>
                    ${p.data.distancia != null ? `Distancia: <b>${p.data.distancia.toFixed(1)} km</b>` : "Sin datos"}
                `
            },

            legend: {
                bottom: 0,
                orient: "horizontal",
                icon: "circle",
                itemWidth: 12,
                itemHeight: 12,
                data: Object.keys(coloresMotivo),
                textStyle: { color: "#64748b" }
            },

            grid: { top: 60, right: 40, bottom: 80, left: 120 },

            xAxis: {
                name: "Días",
                nameLocation: "middle",
                nameGap: 30,
                type: "value",
                min: 0,
                axisLabel: { color: "#64748b" },
                splitLine: { lineStyle: { color: "#e2e8f0" } }
            },

            yAxis: {
                name: "Apiario destino",
                type: "category",
                data: [...new Set(bubbleData.map(d => d.apiario))],
                axisLabel: { color: "#64748b" }
            },

            series
        };

        registrarGrafico("movimiento-colmenas", "eficienciaMovimientoChart", option);
    })();


    /* ========================================================
                    ACTIVIDADES DE POLINIZACION
    ======================================================== */
    (function () {
        const dataPol = data.filter(m => m.motivo_movimiento === "Polinización");

        const cultivos = [...new Set(dataPol.map(m => m.cultivo || "Sin cultivo"))];
        const counts = cultivos.map(
            c => dataPol.filter(x => (x.cultivo || "Sin cultivo") === c).length
        );

        const option = {
            title: {
                text: "Actividades de Polinización por Cultivo",
                left: "center",
                top: 0,
                textStyle: { fontSize: 16, fontWeight: "bold" }
            },

            tooltip: {
                trigger: "axis",
                borderWidth: 2,
                borderColor: "#10b981",
                axisPointer: { type: "shadow" }
            },

            legend: {
                bottom: 0,
                data: ["Colmenas por cultivo"],
                textStyle: { color: "#64748b" }
            },

            grid: { left: "8%", right: "8%", bottom: "10%", top: "18%", containLabel: true },

            xAxis: {
                type: "category",
                data: cultivos,
                axisLabel: { color: "#475569", rotate: cultivos.length > 5 ? 30 : 0 }
            },

            yAxis: {
                type: "value",
                axisLabel: { color: "#64748b" },
                splitLine: { lineStyle: { color: "#e2e8f0" } }
            },

            series: [{
                name: "Colmenas por cultivo",
                type: "bar",
                data: counts,
                barWidth: "70%",
                itemStyle: {
                    color: p => palette[p.dataIndex % palette.length],
                    borderRadius: [10, 10, 0, 0],
                    shadowBlur: 8,
                    shadowColor: "rgba(0,0,0,0.15)",
                    shadowOffsetY: 4
                },
                label: {
                    show: true,
                    position: "top",
                    fontSize: 14,
                    fontWeight: "bold",
                }
            }]
        };

        registrarGrafico("movimiento-colmenas", "polinizacionChart", option);
    })();

    /* ========================================================
                        DURACION POLINIZACION 
    ======================================================== */
    function formatearMD(date) {
        return date.toLocaleDateString("es-ES", {
            month: "short",
            day: "numeric"
        });
    }

    (function () {

        const dataPol = data.filter(m =>
            m.motivo_movimiento === "Polinización" &&
            m.fecha_inicio_mov &&
            m.fecha_termino_mov
        );

        const gantt = dataPol.map(m => {
            const s = new Date(m.fecha_inicio_mov);
            const e = new Date(m.fecha_termino_mov);
            return {
                cultivo: m.cultivo || "Sin cultivo",
                start: s.getTime(),
                end: e.getTime(),
                days: Math.ceil((e - s) / 86400000),
                label: `${formatearMD(s)} → ${formatearMD(e)}`
            };
        });

        const cultivosY = [...new Set(gantt.map(d => d.cultivo))];
        const minX = Math.min(...gantt.map(d => d.start));
        const maxX = Math.max(...gantt.map(d => d.end));

        const palette = [
            "#3b82f6","#10b981","#f59e0b","#ef4444","#8b5cf6",
            "#14b8a6","#f97316","#6366f1","#84cc16","#ec4899",
            "#64748b","#0ea5e9"
        ];
        const colorMap = {};
        cultivosY.forEach((c, i) => { colorMap[c] = palette[i % palette.length]; });

        function renderItem(params, api) {
            const catIdx = api.value(0);
            const start  = api.value(1);
            const end    = api.value(2);
            const label  = api.value(4);
            const cultivo = api.value(5);

            const s = api.coord([start, catIdx]);
            const e = api.coord([end, catIdx]);

            const width  = Math.max(4, e[0] - s[0]);
            const height = 28;

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
                            fill: colorMap[cultivo],
                            shadowColor: "rgba(20,0,0,0.15)"
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

        const seriesData = gantt.map(d => [
            cultivosY.indexOf(d.cultivo),
            d.start,
            d.end,
            d.days,
            d.label,
            d.cultivo
        ]);

        const option = {
            title: {
                text: "Duración de Servicios de Polinización",
                left: "center",
                top: 5,
                textStyle: { fontSize: 16, fontWeight: "bold" }
            },

            tooltip: {
                borderWidth: 2,
                borderColor: "#2563eb",
                formatter: p => `
                    <b>${p.value[5]}</b><br>
                    ${formatearMD(new Date(p.value[1]))} → ${formatearMD(new Date(p.value[2]))}
                `
            },

            legend: {
                bottom: 0,
                data: ["Duración Polinización"],
                textStyle: { color: "#1e293b" }
            },

            grid: { top: 70, right: 20, bottom: 60, left: 130 },

            xAxis: {
                type: "time",
                min: minX,
                max: maxX,
                axisLabel: { color: "#64748b" }
            },

            yAxis: {
                type: "category",
                data: cultivosY,
                axisLabel: { color: "#64748b" }
            },

            series: [{
                name: "Duración Polinización",
                type: "custom",
                renderItem,
                encode: { x: [1, 2], y: 0 },
                data: seriesData
            }]
        };

        registrarGrafico("movimiento-colmenas", "duracionPolinizacionChart", option);

    })();

};
