// public/js/hoja_ruta/catalogo_detalle.js

(function () {
    console.log('catalogo_detalle.js cargado');

    document.addEventListener('DOMContentLoaded', function () {
        // ====== TABS (Descripción / Taxonomía / IAA / Estacionalidad / Mapa) ======
        const buttons = document.querySelectorAll('.species-tab-btn');
        const contents = document.querySelectorAll('.tab-content');

        if (buttons.length && contents.length) {
            buttons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const tab = btn.getAttribute('data-tab');

                    // activar / desactivar botones
                    buttons.forEach(b => b.classList.toggle('active', b === btn));

                    // mostrar / ocultar contenidos
                    contents.forEach(c => {
                        c.classList.toggle('active', c.id === 'tab-' + tab);
                    });
                });
            });
        }

        // ====== GRÁFICO DE ESTACIONALIDAD (Chart.js) ======
        const wrapper = document.getElementById('phenologyWrapper');
        const canvas = document.getElementById('phenologyChart');

        // Necesitamos que exista el wrapper, el canvas y Chart.js cargado
        if (!wrapper || !canvas || typeof Chart === 'undefined') {
            return;
        }

        let phenology = null;
        const phenologyAttr = wrapper.getAttribute('data-phenology');
        if (phenologyAttr) {
            try {
                phenology = JSON.parse(phenologyAttr);
            } catch (e) {
                console.warn('No se pudo parsear phenology JSON:', e);
            }
        }

        if (!phenology) {
            return;
        }

        const months = phenology.months || [
            'ENE','FEB','MAR','ABR','MAY','JUN',
            'JUL','AGO','SEP','OCT','NOV','DIC'
        ];

        const datasetsConfig = [
            {
                key: 'flowers',
                label: 'Flores',
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239,68,68,0.18)'
            },
            {
                key: 'no_flowers',
                label: 'Sin flores ni frutos',
                borderColor: '#f97316',
                backgroundColor: 'rgba(249,115,22,0.18)'
            },
            {
                key: 'buds',
                label: 'Botones florales',
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34,197,94,0.18)'
            },
            {
                key: 'fruits',
                label: 'Frutas o semillas',
                borderColor: '#0ea5e9',
                backgroundColor: 'rgba(14,165,233,0.18)'
            },
            {
                key: 'no_notes',
                label: 'Sin anotación',
                borderColor: '#6b7280',
                backgroundColor: 'rgba(107,114,128,0.18)'
            }
        ];

        const datasets = datasetsConfig
            .filter(cfg => Array.isArray(phenology[cfg.key]))
            .map(cfg => ({
                label: cfg.label,
                data: phenology[cfg.key],
                borderColor: cfg.borderColor,
                backgroundColor: cfg.backgroundColor,
                tension: 0.3,
                fill: true,
                pointRadius: 3
            }));

        if (!datasets.length) {
            return;
        }

        const ctx = canvas.getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        title: {
                            display: true,
                            text: 'Observaciones'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Meses'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                }
            }
        });
    });
})();
