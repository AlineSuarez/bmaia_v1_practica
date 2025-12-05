function mostrarAnimados() {
    document.querySelectorAll(".animate").forEach((el) => {
        el.style.opacity = "1";
    });
}

document.addEventListener("DOMContentLoaded", mostrarAnimados);
document.addEventListener("visibilitychange", function () {
    if (!document.hidden) {
        mostrarAnimados();
    }
});
window.addEventListener("focus", mostrarAnimados);

document.addEventListener("DOMContentLoaded", function () {
    // Activar animaciones al cargar
    document.querySelectorAll(".animate").forEach((el) => {
        el.style.opacity = "1";
    });

    // Desactivar plugin datalabels globalmente
    if (typeof Chart !== 'undefined') {
        // Desregistrar el plugin datalabels completamente
        if (Chart.registry && Chart.registry.plugins) {
            const datalabelsPlugin = Chart.registry.plugins.get('datalabels');
            if (datalabelsPlugin) {
                Chart.unregister(datalabelsPlugin);
            }
        }

        // También desactivarlo en defaults por si acaso
        if (Chart.defaults && Chart.defaults.plugins && Chart.defaults.plugins.datalabels) {
            Chart.defaults.plugins.datalabels.display = false;
        }
    }

    // Verificar que los datos existan
    if (typeof dataApiarios === 'undefined' || typeof dataVisitas === 'undefined') {
        console.error('Datos no disponibles');
        return;
    }

    // ==========================================
    // GRÁFICO 1: COLMENAS POR APIARIO
    // ==========================================
    const colmenasCtx = document.getElementById("colmenasChart");
    if (colmenasCtx && dataApiarios.nombres && dataApiarios.nombres.length > 0) {
        const apiariosColors = dataApiarios.nombres.map(
            () =>
                `rgba(${Math.floor(Math.random() * 256)}, ${Math.floor(
                    Math.random() * 256
                )}, ${Math.floor(Math.random() * 256)}, 0.7)`
        );

        new Chart(colmenasCtx.getContext("2d"), {
            type: "bar",
            data: {
                labels: dataApiarios.nombres,
                datasets: [
                    {
                        label: "Cantidad de Colmenas",
                        data: dataApiarios.colmenas,
                        backgroundColor: apiariosColors,
                        borderColor: apiariosColors.map((color) =>
                            color.replace("0.7", "1")
                        ),
                        borderWidth: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
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
    } else {
        if (colmenasCtx) {
            colmenasCtx.parentElement.innerHTML = '<div style="padding: 40px; text-align: center; color: #64748b;"><i class="fas fa-chart-bar" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; display: block;"></i><p style="margin: 0; font-size: 1rem; font-weight: 600;">No hay datos de colmenas disponibles</p></div>';
        }
    }

    // ==========================================
    // GRÁFICO 2: TEMPORADAS DE PRODUCCIÓN
    // ==========================================
    const temporadasCtx = document.getElementById("temporadasChart");
    if (temporadasCtx && dataApiarios.temporadas && Object.keys(dataApiarios.temporadas).length > 0) {
        const temporadasLabels = Object.keys(dataApiarios.temporadas);
        const temporadasValues = Object.values(dataApiarios.temporadas);

        new Chart(temporadasCtx.getContext("2d"), {
            type: "pie",
            data: {
                labels: temporadasLabels,
                datasets: [
                    {
                        data: temporadasValues,
                        backgroundColor: [
                            "#FF6384",
                            "#36A2EB",
                            "#FFCE56",
                            "#4BC0C0",
                            "#9966FF",
                            "#FF9F40"
                        ],
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
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
                                return `${label}: ${value} (${percentage}%)`;
                            },
                        },
                    },
                },
            },
        });
    } else {
        if (temporadasCtx) {
            temporadasCtx.parentElement.innerHTML = '<div style="padding: 40px; text-align: center; color: #64748b;"><i class="fas fa-chart-pie" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; display: block;"></i><p style="margin: 0; font-size: 1rem; font-weight: 600;">No hay datos de temporadas disponibles</p></div>';
        }
    }

    // ==========================================
    // GRÁFICO 3: VISITAS POR APIARIO
    // ==========================================
    const visitasApiariosCtx = document.getElementById("visitasApiariosChart");
    if (visitasApiariosCtx && dataVisitas.apiarios && dataVisitas.apiarios.length > 0) {
        new Chart(visitasApiariosCtx.getContext("2d"), {
            type: "bar",
            data: {
                labels: dataVisitas.apiarios,
                datasets: [
                    {
                        label: "Número de Visitas",
                        data: dataVisitas.cantidades,
                        backgroundColor: "rgba(54, 162, 235, 0.7)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
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
    } else {
        if (visitasApiariosCtx) {
            visitasApiariosCtx.parentElement.innerHTML = '<div style="padding: 40px; text-align: center; color: #64748b;"><i class="fas fa-calendar-check" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; display: block;"></i><p style="margin: 0; font-size: 1rem; font-weight: 600;">No hay datos de visitas disponibles</p></div>';
        }
    }

    // ==========================================
    // GRÁFICO 4: TIPOS DE VISITAS
    // ==========================================
    const tiposVisitasCtx = document.getElementById("tiposVisitasChart");
    if (tiposVisitasCtx && dataVisitas.tipos && dataVisitas.tipos.length > 0) {
        new Chart(tiposVisitasCtx.getContext("2d"), {
            type: "doughnut",
            data: {
                labels: dataVisitas.tipos,
                datasets: [
                    {
                        data: dataVisitas.totales,
                        backgroundColor: [
                            "#FF6384",
                            "#36A2EB",
                            "#FFCE56",
                            "#4BC0C0",
                            "#9966FF",
                            "#FF9F40"
                        ],
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
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
                                return `${label}: ${value} (${percentage}%)`;
                            },
                        },
                    },
                },
            },
        });
    } else {
        if (tiposVisitasCtx) {
            tiposVisitasCtx.parentElement.innerHTML = '<div style="padding: 40px; text-align: center; color: #64748b;"><i class="fas fa-clipboard-list" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; display: block;"></i><p style="margin: 0; font-size: 1rem; font-weight: 600;">No hay datos de tipos de visitas disponibles</p></div>';
        }
    }

    // ==========================================
    // NUEVOS GRÁFICOS SEGÚN PDF
    // ==========================================

    // GRÁFICO 5: ACTIVIDADES DE APIARIOS (PIE CHART)
    const actividadesCtx = document.getElementById("actividadesChart");
    if (actividadesCtx && typeof actividadesData !== 'undefined' && actividadesData.length > 0) {
        new Chart(actividadesCtx.getContext("2d"), {
            type: "pie",
            data: {
                labels: actividadesData.map(item => item.objetivo_produccion || 'Sin especificar'),
                datasets: [{
                    data: actividadesData.map(item => item.total),
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || "";
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((acc, data) => acc + data, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    } else if (actividadesCtx) {
        actividadesCtx.parentElement.innerHTML = '<div style="padding: 40px; text-align: center; color: #64748b;"><i class="fas fa-chart-pie" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; display: block;"></i><p style="margin: 0; font-size: 1rem; font-weight: 600;">No hay datos de actividades disponibles</p></div>';
    }

    // GRÁFICO 6: DISTRIBUCIÓN DE COLMENAS POR REGIÓN (BAR CHART VERTICAL)
    const regionesCtx = document.getElementById("regionesChart");
    if (regionesCtx && typeof regionesData !== 'undefined' && regionesData.length > 0) {
        // Calcular el total de colmenas para los porcentajes (convertir a número)
        const totalColmenasRegiones = regionesData.reduce((sum, item) => sum + parseInt(item.total_colmenas), 0);

        // Pre-calcular los porcentajes
        const regionesPercentages = regionesData.map(item => {
            return ((parseInt(item.total_colmenas) / totalColmenasRegiones) * 100).toFixed(1);
        });

        // Plugin personalizado para dibujar porcentajes
        const percentagePlugin = {
            id: 'percentageLabels',
            afterDatasetsDraw: function(chart) {
                const ctx = chart.ctx;

                chart.data.datasets.forEach(function(dataset, i) {
                    const meta = chart.getDatasetMeta(i);

                    if (!meta.hidden) {
                        meta.data.forEach(function(element, index) {
                            // Obtener el porcentaje pre-calculado
                            const percentage = parseFloat(regionesPercentages[index]);
                            const percentageText = percentage + '%';

                            // Configurar el texto
                            ctx.font = 'bold 13px Arial';
                            ctx.textAlign = 'center';

                            const x = element.x;
                            let y;
                            let color;

                            // Si el porcentaje es menor a 15%, mostrar arriba de la barra
                            if (percentage < 15) {
                                // Posición arriba de la barra
                                y = element.y - 10;
                                color = '#475569'; // Color gris oscuro para texto fuera de la barra
                                ctx.textBaseline = 'bottom';
                            } else {
                                // Posición dentro de la barra (centro)
                                y = element.y + (element.height / 2);
                                color = '#ffffff'; // Color blanco para texto dentro de la barra
                                ctx.textBaseline = 'middle';
                            }

                            ctx.fillStyle = color;
                            ctx.fillText(percentageText, x, y);
                        });
                    }
                });
            }
        };

        new Chart(regionesCtx.getContext("2d"), {
            type: "bar",
            data: {
                labels: regionesData.map(item => {
                    // Remover el prefijo "Región de " o "Región del "
                    let nombre = item.region || '';
                    nombre = nombre.replace(/^Región\s+(de\s+|del\s+)/i, '');
                    return nombre;
                }),
                datasets: [{
                    label: 'Total Colmenas',
                    data: regionesData.map(item => parseInt(item.total_colmenas)),
                    backgroundColor: '#3b82f6',
                    borderColor: '#2563eb',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 300,
                            callback: function(value, index, ticks) {
                                return value;
                            }
                        },
                        suggestedMax: function(context) {
                            const maxValue = Math.max(...context.chart.data.datasets[0].data);
                            return Math.ceil(maxValue / 300) * 300;
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const percentage = regionesPercentages[context.dataIndex];
                                return `Colmenas: ${value} (${percentage}%)`;
                            }
                        }
                    },
                    datalabels: {
                        display: false
                    }
                }
            },
            plugins: [percentagePlugin]
        });
    } else if (regionesCtx) {
        regionesCtx.parentElement.innerHTML = '<div style="padding: 40px; text-align: center; color: #64748b;"><i class="fas fa-map-marked-alt" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; display: block;"></i><p style="margin: 0; font-size: 1rem; font-weight: 600;">No hay datos de regiones disponibles</p></div>';
    }

    // GRÁFICO 7: TIPO DE ALIMENTO UTILIZADO (PIE CHART)
    const alimentosCtx = document.getElementById("alimentosChart");
    if (alimentosCtx && typeof tiposAlimentoData !== 'undefined' && tiposAlimentoData.length > 0) {
        new Chart(alimentosCtx.getContext("2d"), {
            type: "pie",
            data: {
                labels: tiposAlimentoData.map(item => item.tipo_alimentacion || 'Sin especificar'),
                datasets: [{
                    data: tiposAlimentoData.map(item => parseFloat(item.total_kg)),
                    backgroundColor: ['#f59e0b', '#10b981', '#3b82f6', '#ef4444', '#8b5cf6']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || "";
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((acc, data) => acc + data, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value.toFixed(1)} kg (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    } else if (alimentosCtx) {
        alimentosCtx.parentElement.innerHTML = '<div style="padding: 40px; text-align: center; color: #64748b;"><i class="fas fa-drumstick-bite" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; display: block;"></i><p style="margin: 0; font-size: 1rem; font-weight: 600;">No hay datos de tipos de alimento disponibles</p></div>';
    }

    // GRÁFICO 8: MOVIMIENTOS DE COLMENAS POR MOTIVO (PIE CHART)
    const movimientosCtx = document.getElementById("movimientosChart");
    if (movimientosCtx && typeof movimientosData !== 'undefined' && movimientosData.length > 0) {
        new Chart(movimientosCtx.getContext("2d"), {
            type: "pie",
            data: {
                labels: movimientosData.map(item => {
                    // Usar motivo_movimiento en lugar de tipo_movimiento
                    return item.motivo_movimiento || 'Otro';
                }),
                datasets: [{
                    data: movimientosData.map(item => item.total),
                    backgroundColor: ['#3b82f6', '#f59e0b', '#10b981', '#ef4444']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || "";
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((acc, data) => acc + data, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    } else if (movimientosCtx) {
        movimientosCtx.parentElement.innerHTML = '<div style="padding: 40px; text-align: center; color: #64748b;"><i class="fas fa-truck-moving" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; display: block;"></i><p style="margin: 0; font-size: 1rem; font-weight: 600;">No hay datos de movimientos disponibles</p></div>';
    }

    // GRÁFICO 9: DISTRIBUCIÓN DE APICULTORES POR REGIÓN (BAR CHART VERTICAL)
    const apicultoresCtx = document.getElementById("apicultoresChart");
    if (apicultoresCtx && typeof apicultoresData !== 'undefined' && apicultoresData.length > 0) {
        // Calcular el total de apicultores para los porcentajes (convertir a número)
        const totalApicultoresRegiones = apicultoresData.reduce((sum, item) => sum + parseInt(item.total_apicultores), 0);

        // Pre-calcular los porcentajes
        const apicultoresPercentages = apicultoresData.map(item => {
            return ((parseInt(item.total_apicultores) / totalApicultoresRegiones) * 100).toFixed(1);
        });

        // Plugin personalizado para dibujar porcentajes
        const apicultoresPercentagePlugin = {
            id: 'apicultoresPercentageLabels',
            afterDatasetsDraw: function(chart) {
                const ctx = chart.ctx;

                chart.data.datasets.forEach(function(dataset, i) {
                    const meta = chart.getDatasetMeta(i);

                    if (!meta.hidden) {
                        meta.data.forEach(function(element, index) {
                            // Obtener el porcentaje pre-calculado
                            const percentage = parseFloat(apicultoresPercentages[index]);
                            const percentageText = percentage + '%';

                            // Configurar el texto
                            ctx.font = 'bold 13px Arial';
                            ctx.textAlign = 'center';

                            const x = element.x;
                            let y;
                            let color;

                            // Si el porcentaje es menor a 15%, mostrar arriba de la barra
                            if (percentage < 15) {
                                // Posición arriba de la barra
                                y = element.y - 10;
                                color = '#475569'; // Color gris oscuro para texto fuera de la barra
                                ctx.textBaseline = 'bottom';
                            } else {
                                // Posición dentro de la barra (centro)
                                y = element.y + (element.height / 2);
                                color = '#ffffff'; // Color blanco para texto dentro de la barra
                                ctx.textBaseline = 'middle';
                            }

                            ctx.fillStyle = color;
                            ctx.fillText(percentageText, x, y);
                        });
                    }
                });
            }
        };

        new Chart(apicultoresCtx.getContext("2d"), {
            type: "bar",
            data: {
                labels: apicultoresData.map(item => {
                    // Remover el prefijo "Región de " o "Región del "
                    let nombre = item.region || '';
                    nombre = nombre.replace(/^Región\s+(de\s+|del\s+)/i, '');
                    return nombre;
                }),
                datasets: [{
                    label: 'Total Apicultores',
                    data: apicultoresData.map(item => parseInt(item.total_apicultores)),
                    backgroundColor: '#10b981',
                    borderColor: '#059669',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 10,
                            callback: function(value, index, ticks) {
                                return value;
                            }
                        },
                        suggestedMax: function(context) {
                            const maxValue = Math.max(...context.chart.data.datasets[0].data);
                            return Math.ceil(maxValue / 10) * 10;
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const percentage = apicultoresPercentages[context.dataIndex];
                                return `Apicultores: ${value} (${percentage}%)`;
                            }
                        }
                    },
                    datalabels: {
                        display: false
                    }
                }
            },
            plugins: [apicultoresPercentagePlugin]
        });
    } else if (apicultoresCtx) {
        apicultoresCtx.parentElement.innerHTML = '<div style="padding: 40px; text-align: center; color: #64748b;"><i class="fas fa-users" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; display: block;"></i><p style="margin: 0; font-size: 1rem; font-weight: 600;">No hay datos de apicultores disponibles</p></div>';
    }
});
