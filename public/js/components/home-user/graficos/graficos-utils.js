/********************************************
 * UTILIDADES GLOBALES PARA DASHBOARD 
 ********************************************/

window.chartInstances = window.chartInstances || {};
window.modalChartInstances = window.modalChartInstances || [];

/* Animacion */
function mostrarAnimados() {
    document.querySelectorAll(".animate").forEach(el => {
        el.style.opacity = "1";
        el.style.transform = "translateY(0)";
    });
}
document.addEventListener("DOMContentLoaded", mostrarAnimados);


/* Destruir todos los gráficos pertenecientes a un indicador */
window.destruirGraficosIndicador = function (indicador) {
    if (window.chartInstances[indicador]) {
        window.chartInstances[indicador].forEach(c => c.dispose());
        window.chartInstances[indicador] = [];
    }
};

/* Mostrar loader del modal */
window.mostrarModalLoader = function () {
    const loader = document.getElementById("modalLoader");
    if (!loader) return;
    loader.classList.remove("d-none");
};

/* Ocultar loader del modal */ 
window.ocultarModalLoader = function () {
    const loader = document.getElementById("modalLoader");
    if (!loader) return;

    loader.style.opacity = "0";

    setTimeout(() => {
        loader.classList.add("d-none");
        loader.style.opacity = "1";
    }, 350);
};

/*----------------------------------------------------------
    Funcion para registrar graficos segun indicador
-----------------------------------------------------------*/
window.registrarGrafico = function (indicador, elementId, option) {
    const el = document.getElementById(elementId);
    if (!el) return;

    window.chartInstances[indicador] = window.chartInstances[indicador] || [];

    const chart = echarts.init(el);
    chart.setOption(option);

    // Guardamos el titulo para posibles usos futuros
    chart._chartTitle = option.title?.text || "";

    window.chartInstances[indicador].push(chart);
    return chart;
};

/*----------------------------------------------------------
    Resize global
-----------------------------------------------------------*/
window.resizeAllCharts = function () {
    Object.values(window.chartInstances).forEach(grupo => {
        (grupo || []).forEach(chart => chart.resize());
    });

    (window.modalChartInstances || []).forEach(chart => chart.resize());
};
window.addEventListener("resize", window.resizeAllCharts);

/*--------------------------------------------------------------------
    MODAL: animacion + render dinamico + paginacion
---------------------------------------------------------------------*/
window.mostrarGraficosEnModal = function (indicador) {

    const contenedor = document.getElementById("contenedorModalGraficos");
    contenedor.innerHTML = "";

    // limpiar instancias previas del modal
    (window.modalChartInstances || []).forEach(c => c.dispose());
    window.modalChartInstances = [];

    const charts = window.chartInstances[indicador] || [];

    /* PAGINACION */
    const porPagina = 4;
    let paginaActual = 1;
    const totalPaginas = Math.ceil(charts.length / porPagina) || 1;

    function renderPagina() {
        contenedor.innerHTML = "";
        window.modalChartInstances.forEach(c => c.dispose());
        window.modalChartInstances = [];

        const inicio = (paginaActual - 1) * porPagina;
        const paginaCharts = charts.slice(inicio, inicio + porPagina);

        paginaCharts.forEach((chartOriginal, index) => {
            const wrapper = document.createElement("div");

            const esUltimo = index === paginaCharts.length - 1;
            const esImpar = paginaCharts.length % 2 === 1;

            if (esUltimo && esImpar) {
                wrapper.className = "col-md-12 mb-4 animate-modal";
            } else {
                wrapper.className = "col-md-6 mb-4 animate-modal";
            }

            wrapper.style.opacity = 0;
            wrapper.style.transform = "scale(1)";
            wrapper.style.transition = "all 0.4s ease";

            wrapper.innerHTML = `
                <div class="export-chart-wrapper p-2 text-center">
                    <div id="modalChart${index}" style="height:300px;width:100%;"></div>
                </div>
            `;

            contenedor.appendChild(wrapper);

            setTimeout(() => {
                wrapper.style.opacity = 1;
                wrapper.style.transform = "scale(1)";
            }, 80 * index);

            const chartDiv = document.getElementById(`modalChart${index}`);
            const modalChart = echarts.init(chartDiv);
            modalChart.setOption(chartOriginal.getOption());

            window.modalChartInstances.push(modalChart);
        });

        document.getElementById("paginadorGraficos").innerHTML =
            `<button class="btn btn-secondary btn-sm me-2" ${paginaActual === 1 ? "disabled" : ""}
                onclick="paginaAnterior()">Anterior</button>
             <span>Página ${paginaActual} de ${totalPaginas}</span>
             <button class="btn btn-secondary btn-sm ms-2" ${paginaActual === totalPaginas ? "disabled" : ""}
                onclick="paginaSiguiente()">Siguiente</button>`;
    }

    window.paginaAnterior = () => {
        if (paginaActual > 1) {
            paginaActual--;
            renderPagina();
        }
    };

    window.paginaSiguiente = () => {
        if (paginaActual < totalPaginas) {
            paginaActual++;
            renderPagina();
        }
    };

    renderPagina();
};

/*--------------------------------------------------------------------
    Modal configurado segun indicador
---------------------------------------------------------------------*/
window.configurarModalPorIndicador = function (indicador) {
    const modal = document.getElementById("modalGraficosIndicador");
    const btnPDFModal = document.getElementById("btnDescargarPDF");

    // Guardamos indicador actual para el modal
    window.indicadorActivo = indicador;
    window.indicadorModalActual = indicador;

    // Evitar añadir multiples listeners
    if (!modal._grafModalHandlersAttached) {

        modal.addEventListener("shown.bs.modal", () => {

            // Mostrar boton PDF del modal
            if (btnPDFModal) btnPDFModal.classList.remove("d-none");
        
            // Mostrar loader antes de generar los graficos
            window.mostrarModalLoader();
        
            const ind = window.indicadorModalActual || window.indicadorActivo;
        
            if (ind) {
                // Render grafico 
                window.mostrarGraficosEnModal(ind);
        
                // Ocultar loader cuando termine de renderizar
                setTimeout(() => {
                    window.resizeAllCharts();
                    window.ocultarModalLoader();
                }, 120);
            }
        });           

        modal.addEventListener("hidden.bs.modal", () => {
            if (btnPDFModal) btnPDFModal.classList.add("d-none");

            // Limpiar instancias del modal
            window.modalChartInstances.forEach(c => c.dispose());
            window.modalChartInstances = [];
            const cont = document.getElementById("contenedorModalGraficos");
            const pag = document.getElementById("paginadorGraficos");
            if (cont) cont.innerHTML = "";
            if (pag) pag.innerHTML = "";
        });

        modal._grafModalHandlersAttached = true;
    }
};

/*----------------------------------------------------------
    Exportacion a PDF 
-----------------------------------------------------------*/
window.exportarGraficosAPDF = function (tituloPDF = "graficos.pdf") {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF("p", "mm", "a4");

    const indicador = window.indicadorModalActual || window.indicadorActivo;
    if (!indicador) return;

    // graficos originales de ese indicador
    const charts = window.chartInstances[indicador] || [];
    if (!charts.length) return;

    const pageWidth = 210;
    const pageHeight = 297;
    const marginLeft = 18;
    const marginRight = 18;
    const marginTop = 20;
    const marginBottom = 20;

    const chartsPerPage = 3;
    const usableHeight = pageHeight - marginTop - marginBottom;
    const spaceBetweenCharts = 15;
    const chartHeight =
        (usableHeight - (chartsPerPage - 1) * spaceBetweenCharts) / chartsPerPage;

    const chartWidth = pageWidth - marginLeft - marginRight;

    let y = marginTop;

    charts.forEach(chart => {

        const img = chart.getDataURL({
            pixelRatio: 2,      
            backgroundColor: "#ffffff"
        });

        if (y + chartHeight > pageHeight - marginBottom) {
            pdf.addPage();
            y = marginTop;
        }

        // Marco alrededor de los graficos
        pdf.setDrawColor(200); 
        pdf.setLineWidth(0.5);
        pdf.rect(marginLeft - 2, y - 2, chartWidth + 4, chartHeight + 4);

        // Imagen del grafico
        pdf.addImage(img, "PNG", marginLeft, y, chartWidth, chartHeight);

        y += chartHeight + spaceBetweenCharts;
    });

    pdf.save(tituloPDF);
};

/*----------------------------------------------------------
    Helper: asegurar que TODOS los graficos de TODOS
    los indicadores esten renderizados con ancho > 0
-----------------------------------------------------------*/
function prepararTodosLosGraficosParaExport() {

    const configs = [
        { key: "global",              flag: "graficosGlobalCreados",         init: window.inicializarGraficosGlobal,      domId: "indicador-global" },
        { key: "por-apiario",         flag: "graficosPorApiarioCreados",     init: window.inicializarGraficosPorApiario,  domId: "indicador-por-apiario" },
        { key: "plan-anual",          flag: "graficosPlanAnualCreados",      init: window.inicializarGraficosPlanAnual,   domId: "indicador-plan-anual" },
        { key: "movimiento-colmenas", flag: "graficosMovimientoCreados",     init: window.inicializarGraficosMovimiento,  domId: "indicador-movimiento-colmenas" },
        { key: "gestion-apiario",     flag: "graficosGestionApiarioCreados", init: window.inicializarGraficosGestion,     domId: "indicador-gestion-apiario" },
    ];

    const gruposTocados = [];

    configs.forEach(cfg => {
        const grupo = document.getElementById(cfg.domId);
        if (!grupo) return;

        // Guardamos estilos originales
        const prevDisplay  = grupo.style.display;
        const prevPosition = grupo.style.position;
        const prevLeft     = grupo.style.left;
        const prevWidth    = grupo.style.width;

        gruposTocados.push({
            el: grupo,
            prevDisplay,
            prevPosition,
            prevLeft,
            prevWidth
        });

        grupo.style.display  = "block";
        grupo.style.position = "absolute";
        grupo.style.left     = "-99999px";
        grupo.style.width    = "1400px"; 

        // Si no se habian creado los graficos de ese indicador, se crean ahora
        if (!window[cfg.flag] && typeof cfg.init === "function") {
            cfg.init();
            window[cfg.flag] = true;
        }
    });

    // resize general para ajustar bien los tamaños
    window.resizeAllCharts();

    // función para restaurar los grupos tocados
    return function restaurarGrupos() {
        gruposTocados.forEach(g => {
            g.el.style.display  = g.prevDisplay;
            g.el.style.position = g.prevPosition;
            g.el.style.left     = g.prevLeft;
            g.el.style.width    = g.prevWidth;
        });

        setTimeout(() => window.resizeAllCharts(), 100);
    };
}

/*----------------------------------------------------------
    Exportación a PDF: todos los graficos
    de todos los indicadores
-----------------------------------------------------------*/
window.exportarTodosLosGraficosAPDF = function () {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF("p", "mm", "a4");

    // Asegurarnos de que todos los indicadores tengan sus graficos
    const restaurar = prepararTodosLosGraficosParaExport();

    // Aplanar TODOS los charts de todos los indicadores
    const todosCharts = Object.values(window.chartInstances || {})
        .reduce((acc, arr) => acc.concat(arr || []), [])
        .filter(Boolean);

    if (!todosCharts.length) {
        restaurar();
        return;
    }

    const pageWidth = 210;
    const pageHeight = 297;
    const marginLeft = 18;
    const marginRight = 18;
    const marginTop = 20;
    const marginBottom = 20;

    const chartsPerPage = 3;
    const usableHeight = pageHeight - marginTop - marginBottom;
    const spaceBetweenCharts = 15;
    const chartHeight =
        (usableHeight - (chartsPerPage - 1) * spaceBetweenCharts) / chartsPerPage;

    const chartWidth = pageWidth - marginLeft - marginRight;

    let y = marginTop;

    todosCharts.forEach(chart => {

        const img = chart.getDataURL({
            pixelRatio: 1.5,  
            backgroundColor: "#ffffff"
        });

        if (y + chartHeight > pageHeight - marginBottom) {
            pdf.addPage();
            y = marginTop;
        }

        // Marco alrededor de los graficos
        pdf.setDrawColor(200); 
        pdf.setLineWidth(0.5);
        pdf.rect(marginLeft - 2, y - 2, chartWidth + 4, chartHeight + 4);

        // Imagen del grafico
        pdf.addImage(img, "PNG", marginLeft, y, chartWidth, chartHeight);

        y += chartHeight + spaceBetweenCharts;
    });

    // Restaurar DOM
    restaurar();

    pdf.save("graficos-indicadores.pdf");
};

/*----------------------------------------------------------
            Listeners para los botones de PDF
-----------------------------------------------------------*/
document.addEventListener("DOMContentLoaded", () => {
    const btnModalPDF  = document.getElementById("btnDescargarPDF");
    const btnGlobalPDF = document.getElementById("btnDescargarPDFGlobal");

    if (btnModalPDF) {
        btnModalPDF.addEventListener("click", () => {
            const ind = window.indicadorModalActual || window.indicadorActivo;
            if (!ind && (!window.chartInstances || !Object.keys(window.chartInstances).length)) return;

            window.exportarGraficosAPDF(`graficos-${ind || 'indicador'}.pdf`);
        });
    }

    if (btnGlobalPDF) {
        btnGlobalPDF.addEventListener("click", () => {
            window.exportarTodosLosGraficosAPDF();
        });
    }
});