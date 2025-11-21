// INDICADOR 3 - PLAN DE TRABAJO ANUAL
window.inicializarGraficosPlanAnual = function () {
    const subtareas = window.dataSubtareas || [];
    const tareasGenerales = window.dataTareasGenerales || [];

    /* ========================================================
                    ESTADO DE TAREAS
    ======================================================== */

    const t_completadas = subtareas.filter(t => t.estado === 'Completada').length;
    const t_progreso    = subtareas.filter(t => t.estado === 'En progreso').length;
    const t_pendientes  = subtareas.filter(t => t.estado === 'Pendiente').length;
    const t_vencidas    = subtareas.filter(t => {
        if (!t.fecha_limite) return false;
        return new Date(t.fecha_limite) < new Date() && t.estado !== "Completada";
    }).length;

    const dataEstadoTareas = [
        { value: t_completadas, name: 'Completadas', itemStyle: { color: '#10b981' } },
        { value: t_progreso,    name: 'En progreso', itemStyle: { color: '#f59e0b' } },
        { value: t_pendientes,  name: 'Pendientes',  itemStyle: { color: '#3b82f6' } },
        { value: t_vencidas,    name: 'Vencidas',    itemStyle: { color: '#ef4444' } }
    ];

    const optionEstadoTareas = {
        title: {
            text: "Estado de Tareas",
            left: "center",
            top: 0,
            textStyle: { fontSize: 16, fontWeight: "bold" }
        },

        tooltip: {
            trigger: 'item',
            borderWidth: 2,
        },

        legend: { 
            bottom: 0,
            icon: 'circle'
        },

        series: [{
            type: 'pie',
            radius: ['30%', '60%'],
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
                fontWeight: 'bold',
                color: '#1e293b',
            },
            data: dataEstadoTareas
        }]
    };

    registrarGrafico("plan-anual", "estadoTareasChart", optionEstadoTareas);

    /* ========================================================
                CUPLIMIENTO DEL PLAN DE TRABAJO
    ======================================================== */

    const limpiarNombreFase = nombre => {
        if (!nombre) return "Fase";

        if (nombre.includes(" - ")) {
            nombre = nombre.split(" - ")[1].trim();
        }

        nombre = nombre.toLowerCase();
        nombre = nombre.charAt(0).toUpperCase() + nombre.slice(1);

        return nombre.length > 28 ? nombre.substring(0, 28) + "…" : nombre;
    };

    const grupos = subtareas.reduce((acc, t) => {
        const id = t.tarea_general_id || 0;
        if (!acc[id]) acc[id] = [];
        acc[id].push(t);
        return acc;
    }, {});

    let fases = Object.keys(grupos).map(id => {
        const fase = tareasGenerales.find(t => t.id == id);
        return limpiarNombreFase(fase ? fase.nombre : `Fase ${id}`);
    });

    let porcentajes = Object.values(grupos).map(grupo => {
        const total = grupo.length;
        const completadas = grupo.filter(s => s.estado === "Completada").length;
        return total === 0 ? 0 : Math.round((completadas / total) * 100);
    });

    fases.reverse();
    porcentajes.reverse();

    const optionCumplimientoPlan = {
        title: {
            text: "Cumplimiento del Plan de Trabajo",
            left: "center",
            top: 5,
            textStyle: { fontSize: 16, fontWeight: "bold" }
        },

        tooltip: { 
            trigger: 'axis',
            borderColor: '#10b981',
            borderWidth: 2,
            axisPointer: { type: 'shadow' }
        },

        legend: {
            data: ['% Cumplimiento'],
            top: 30,
            left: 'center'
        },

        grid: {
            left: 180,
            right: 20,
            top: 70,
            bottom: 20
        },

        xAxis: {
            type: 'value',
            max: 100,
            axisLabel: { formatter: '{value}%' }
        },

        yAxis: { type: 'category', data: fases },

        series: [{
            name: '% Cumplimiento',
            type: 'bar',
            data: porcentajes,
            barWidth: 25,
            itemStyle: { 
                color: '#10b981', 
                borderRadius: [0, 10, 10, 0], 
                shadowBlur: 8,
                shadowColor: 'rgba(20,0,0,0.15)',
                shadowOffsetY: 4 
            },
            label: {
                show: true,
                position: 'right',
                formatter: '{c}%',
                fontSize: 14,
                fontWeight: 'bold'
            }
        }]
    };

    registrarGrafico("plan-anual", "cumplimientoPlanChart", optionCumplimientoPlan);

};