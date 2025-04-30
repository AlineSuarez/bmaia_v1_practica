document.addEventListener("DOMContentLoaded", function () {
    // Activar animaciones al cargar
    document.querySelectorAll(".animate").forEach((el) => {
        el.style.opacity = "1";
    });

    // Mantener la funcionalidad original exactamente como estaba
    const apiariosNames = dataApiarios.map((item) => item.name);
    const colmenasCount = dataApiarios.map((item) => item.count);
    const seasons = dataApiarios.map((item) => item.season);

    const colmenasCtx = document
        .getElementById("colmenasChart")
        .getContext("2d");
    const apiariosColors = apiariosNames.map(
        () =>
            `rgba(${Math.floor(Math.random() * 256)}, ${Math.floor(
                Math.random() * 256
            )}, ${Math.floor(Math.random() * 256)}, 0.7)`
    );

    new Chart(colmenasCtx, {
        type: "bar",
        data: {
            labels: apiariosNames,
            datasets: [
                {
                    label: "Cantidad de Colmenas",
                    data: colmenasCount,
                    backgroundColor: apiariosColors,
                    borderColor: apiariosColors.map((color) =>
                        color.replace("0.7", "1")
                    ),
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return `Colmenas: ${context.raw}`;
                        },
                    },
                },
            },
        },
    });

    const temporadasCtx = document
        .getElementById("temporadasChart")
        .getContext("2d");
    const seasonCounts = seasons.reduce((acc, season) => {
        acc[season] = (acc[season] || 0) + 1;
        return acc;
    }, {});

    new Chart(temporadasCtx, {
        type: "pie",
        data: {
            labels: Object.keys(seasonCounts),
            datasets: [
                {
                    data: Object.values(seasonCounts),
                    backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56"],
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const label = context.label || "";
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce(
                                (acc, data) => acc + data,
                                0
                            );
                            const percentage = Math.round(
                                (value / total) * 100
                            );
                            return `${label}: ${value} apiarios (${percentage}%)`;
                        },
                    },
                },
            },
        },
    });

    // Implementar los gráficos faltantes usando los mismos datos
    // Visitas por Apiario (gráfico de barras)
    const visitasApiariosCtx = document
        .getElementById("visitasApiariosChart")
        .getContext("2d");

    // Crear datos de ejemplo basados en los apiarios existentes
    const visitasData = {};
    apiariosNames.forEach((name) => {
        // Asignar un número aleatorio de visitas a cada apiario
        visitasData[name] = Math.floor(Math.random() * 10) + 1;
    });

    new Chart(visitasApiariosCtx, {
        type: "bar",
        data: {
            labels: apiariosNames,
            datasets: [
                {
                    label: "Número de Visitas",
                    data: Object.values(visitasData),
                    backgroundColor: apiariosColors,
                    borderColor: apiariosColors.map((color) =>
                        color.replace("0.7", "1")
                    ),
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return `Visitas: ${context.raw}`;
                        },
                    },
                },
            },
        },
    });

    // Tipos de Visitas (gráfico de dona)
    const tiposVisitasCtx = document
        .getElementById("tiposVisitasChart")
        .getContext("2d");

    // Datos de ejemplo para tipos de visitas
    const tiposVisitas = {
        Revisión: 8,
        Alimentación: 5,
        Cosecha: 3,
        Tratamiento: 4,
        Mantenimiento: 2,
    };

    new Chart(tiposVisitasCtx, {
        type: "doughnut",
        data: {
            labels: Object.keys(tiposVisitas),
            datasets: [
                {
                    data: Object.values(tiposVisitas),
                    backgroundColor: [
                        "#FF6384",
                        "#36A2EB",
                        "#FFCE56",
                        "#4BC0C0",
                        "#9966FF",
                    ],
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const label = context.label || "";
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce(
                                (acc, data) => acc + data,
                                0
                            );
                            const percentage = Math.round(
                                (value / total) * 100
                            );
                            return `${label}: ${value} visitas (${percentage}%)`;
                        },
                    },
                },
            },
        },
    });

    // Inicializar tooltips
    document.querySelectorAll(".chart-help").forEach((el) => {
        el.addEventListener("mouseenter", function () {
            const tooltip =
                this.parentElement.querySelector(".tooltip-content");
            tooltip.style.opacity = "1";
            tooltip.style.visibility = "visible";
        });

        el.addEventListener("mouseleave", function () {
            const tooltip =
                this.parentElement.querySelector(".tooltip-content");
            tooltip.style.opacity = "0";
            tooltip.style.visibility = "hidden";
        });
    });
});
