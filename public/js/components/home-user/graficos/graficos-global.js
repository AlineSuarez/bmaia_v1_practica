// INDICADOR 1 - PRODUCTIVIDAD GLOBAL 
window.inicializarGraficosGlobal = function () {
    const apiarios = window.dataApiarios || [];
    const visitas = window.dataVisitas || [];

    /* ========================================================
                    NUMERO DE COLMENAS POR APIARIO
    ======================================================== */

    const nombresApiarios = apiarios.map(a => a.name);
    const numColmenas = apiarios.map(a => a.count);

    const optionColmenas = {
        title: {
            text: "Número de Colmenas por Apiario",
            left: "center",
            top: 0,
            textStyle: { fontSize: 16, fontWeight: "bold" }
        },

        tooltip: {
            trigger: 'axis',
            borderWidth: 2,
            borderColor: '#f59e0b',
            axisPointer: { type: 'shadow',
                shadowStyle: { color: 'rgba(251, 191, 36, 0.2)'},
            },
        },

        grid: { left: '5%', right: '5%', bottom: '8%', top: '18%', containLabel: true },

        legend : { bottom: 0, textStyle: { color: "#64748b", fontSize: 11 } },

        xAxis: {
            type: 'category',
            data: nombresApiarios,
            axisLabel: {
                color: '#64748b',
                fontSize: 10,
                rotate: nombresApiarios.length > 5 ? 45 : 0,
                formatter: value => value.length > 10 ? value.slice(0, 10) + '…' : value
            },
           
        },

        yAxis: {
            type: 'value',
            splitLine: { lineStyle: { color: '#e2e8f0', } },
            axisLabel: { color: '#64748b', fontSize: 12 }
        },

        series: [{
            name: 'Colmenas',
            type: 'bar',
            data: numColmenas,
            barWidth: '70%',
            itemStyle: {
                color: '#f59e0b',
                borderRadius: [10, 10, 0, 0],
                shadowBlur: 10,
                shadowColor: 'rgba(245, 158, 11, 0.3)',
                shadowOffsetY: 5
            },
            label: {
                show: true,
                position: 'top',
                fontSize: 14,
                fontWeight: 'bold'
            }
        }],

        animationEasing: 'elasticOut',
        animationDuration: 1500
    };

    registrarGrafico("global", "colmenasPorApiarioChart", optionColmenas);

    /* ========================================================
                    ACTIVIDAD DE APIARIOS
    ======================================================== */
    const actividades = {};
    apiarios.forEach(a => {
        actividades[a.actividad || "Sin dato"] = (actividades[a.actividad] || 0) + 1;
    });

    const dataActividad = Object.keys(actividades).map((key, index) => ({
        name: key,
        value: actividades[key],
        itemStyle: {
            color: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'][index % 5],
        }
    }));

    const optionActividad = {
        title: {
            text: "Tipo de Actividad de los Apiarios",
            left: "center",
            top: 0,
            textStyle: { fontSize: 16, fontWeight: "bold" }
        },

        tooltip: {
            trigger: 'item',
            borderWidth: 2,
            textStyle: { fontSize: 14 }
        },

        legend: {
            bottom: 0,
            textStyle: { color: '#64748b', fontSize: 12 },
            icon: 'circle'
        },

        series: [{
            type: 'pie',
            radius: ['35%', '65%'],
            padAngle: 5,
            minAngle: 8,
            itemStyle: {
                borderRadius: 8,
                borderColor: '#fff',
                borderWidth: 2
            },
            labelLine: { show: true, length: 15, length2: 10, smooth: true },
            label: {
                show: true,
                formatter: '{d}%',
                fontSize: 14,
                color: "#1e293b",
                fontWeight: "bold"
            },
            data: dataActividad
        }]
    };

    registrarGrafico("global", "tipoActividadApiariosChart", optionActividad);

    /* ========================================================
                    VISITAS POR APIARIO
    ======================================================== */
    const visitasTotalesPorApiario = {};

    visitas.forEach(v => {
        const api = v.apiario || "Sin nombre";
        visitasTotalesPorApiario[api] = (visitasTotalesPorApiario[api] || 0) + 1;
    });

    // Nuevo nombre para evitar conflicto
    const apiariosVisTotales = Object.keys(visitasTotalesPorApiario);
    const valoresVisTotales = apiariosVisTotales.map(api => visitasTotalesPorApiario[api]);

    const optionVisitasTotales = {
        title: {
            text: "Total de Visitas por Apiario",
            left: "center",
            top: 0,
            textStyle: { fontSize: 16, fontWeight: "bold" }
        },

        legend: {
            bottom: 0,
            textStyle: { color: "#64748b", fontSize: 12 }
        },

        tooltip: {
            trigger: 'item',
            borderWidth: 2,
            borderColor: "#3b82f6",
        },

        grid: { left: '8%', right: '8%', bottom: '10%', top: '18%', containLabel: true },

        xAxis: {
            type: 'value',
            axisLabel: { color: '#64748b' },
            splitLine: { lineStyle: { color: '#e2e8f0' } }
        },

        yAxis: {
            type: 'category',
            data: apiariosVisTotales,
            axisLabel: { color: '#64748b' }
        },

        series: [{
            name: "Visitas Totales",
            type: 'bar',
            data: valoresVisTotales,
            barWidth: '60%',
            itemStyle: {
                color: '#3b82f6',
                borderRadius: [6, 6, 6, 6],
                shadowBlur: 8,
                shadowColor: 'rgba(0,0,0,0.15)',
                shadowOffsetY: 4
            },
            label: {
                show: true,
                position: 'right',
                fontSize: 14,
                fontWeight: 'bold',
                color: '#1e293b'
            }
        }]
    };

    registrarGrafico("global", "visitasPorApiarioChart", optionVisitasTotales);

    /* ========================================================
                    TRATAMIENTOS POR APIARIO
    ======================================================== */
    const tratamientosPorApiario = {};
    visitas
        .filter(v => v.tipo_visita === "Uso de Medicamentos")
        .forEach(v => {
            tratamientosPorApiario[v.apiario] =
                (tratamientosPorApiario[v.apiario] || 0) + 1;
        });

    const nombresApiariosTrat = Object.keys(tratamientosPorApiario);
    const cantidadTratamientos = Object.values(tratamientosPorApiario);

    const optionTratamientos = {
        title: {
            text: "Total de Tratamientos por Apiario",
            left: "center",
            top: 0,
            textStyle: { fontSize: 16, fontWeight: "bold" }
        },

        tooltip: {
            trigger: 'axis',
            borderColor: '#10b981',
            borderWidth: 2,
            axisPointer: { type: 'shadow' }
        },

        legend : { bottom: 0, textStyle: { color: "#64748b", fontSize: 11 } },

        grid: { left: '8%', right: '8%', bottom: '10%', top: '18%', containLabel: true },

        xAxis: {
            type: 'category',
            data: nombresApiariosTrat,
            axisLabel: { color: '#64748b' }
        },

        yAxis: { 
            type: 'value', 
            axisLabel: { color: '#64748b' },
            splitLine: { lineStyle: { color: '#e2e8f0', } },
        },

        series: [{
            name: 'Tratamientos',
            type: 'bar',
            data: cantidadTratamientos,
            barWidth: '70%',
            itemStyle: {
                color: "#10b981",
                borderRadius: [10, 10, 0, 0],
                shadowBlur: 8,
                shadowColor: 'rgba(20,0,0,0.15)',
                shadowOffsetY: 4
            },
            label: {
                show: true,
                position: 'top',
                fontSize: 14,
                fontWeight: 'bold'
            },
        }],
    };

    registrarGrafico("global", "tratamientosPorApiarioChart", optionTratamientos);

};
