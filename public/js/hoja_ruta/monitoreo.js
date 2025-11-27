(function () {
    console.log('monitoreo.js cargado');

    document.addEventListener('DOMContentLoaded', function () {
        const root = document.getElementById('climate-root');
        if (!root) return;

        // ===== Leer datos desde atributos data-* inyectados por Blade =====
        const parseJSONAttr = (attr, fallback) => {
            const raw = root.getAttribute(attr);
            if (!raw) return fallback;
            try {
                return JSON.parse(raw);
            } catch (e) {
                console.warn('No se pudo parsear', attr, e);
                return fallback;
            }
        };

        const CL_LABELS    = parseJSONAttr('data-labels', []);
        const CL_TEMP_MEAN = parseJSONAttr('data-temp-mean', []);
        const CL_TEMP_MAX  = parseJSONAttr('data-temp-max', []);
        const CL_PRECIP    = parseJSONAttr('data-precip', []);
        const CL_WIND      = parseJSONAttr('data-wind', []);
        const CURRENT_ZONE = root.getAttribute('data-current-zone') || '';

        const hasLabels = Array.isArray(CL_LABELS) && CL_LABELS.length > 0;

        // ===== Pronóstico 5 días con Open-Meteo =====
        async function loadForecast5d() {
            const container = document.getElementById('forecast-days');
            const sub = document.getElementById('forecast-sub');
            if (!container || !CURRENT_ZONE) return;

            try {
                sub.textContent = 'Cargando pronóstico…';

                // 1) Geocodificar nombre de la zona
                const geoRes = await fetch(
                    'https://geocoding-api.open-meteo.com/v1/search?name=' +
                    encodeURIComponent(CURRENT_ZONE) +
                    '&count=1&language=es&format=json'
                );
                const geo = await geoRes.json();
                if (!geo.results || !geo.results.length) throw new Error('Sin coordenadas');

                const { latitude, longitude } = geo.results[0];

                // 2) Pronóstico diario 5 días
                const fcRes = await fetch(
                    'https://api.open-meteo.com/v1/forecast' +
                    '?latitude=' + latitude +
                    '&longitude=' + longitude +
                    '&daily=weathercode,temperature_2m_max,temperature_2m_min,precipitation_sum' +
                    '&timezone=auto&forecast_days=5'
                );
                const fc = await fcRes.json();
                const daily = fc.daily;
                if (!daily || !daily.time || !daily.time.length) throw new Error('Sin datos');

                const dayNames = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

                container.innerHTML = '';
                sub.textContent = 'Zona aproximada: ' + CURRENT_ZONE;

                const mapWeatherCode = (code) => {
                    if (code === 0) return '☀️';
                    if (code === 1 || code === 2) return '🌤️';
                    if (code === 3) return '☁️';
                    if (code === 45 || code === 48) return '🌫️';
                    if (code >= 51 && code <= 67) return '🌧️';
                    if (code >= 71 && code <= 77) return '❄️';
                    if (code >= 80 && code <= 82) return '🌦️';
                    if (code >= 95) return '⛈️';
                    return 'ℹ️';
                };

                for (let i = 0; i < daily.time.length && i < 5; i++) {
                    const d = new Date(daily.time[i]);
                    const name = dayNames[d.getDay()];
                    const max = daily.temperature_2m_max[i];
                    const min = daily.temperature_2m_min[i];
                    const prec = daily.precipitation_sum[i];
                    const code = daily.weathercode[i];

                    const el = document.createElement('div');
                    el.className = 'forecast-day';
                    el.innerHTML = `
                        <div class="f-day-name">${name}</div>
                        <div class="f-day-icon">${mapWeatherCode(code)}</div>
                        <div class="f-day-temp">${Math.round(max)}° / ${Math.round(min)}°</div>
                        <div class="f-day-prec">${prec.toFixed(1)} mm</div>
                    `;
                    container.appendChild(el);
                }

            } catch (e) {
                if (sub) sub.textContent = 'Pronóstico no disponible para esta zona.';
            }
        }

        // Cargamos siempre el pronóstico (aunque no haya datos históricos)
        loadForecast5d();

        if (!hasLabels) return;

        // ==== helpers numéricos ====
        const cleanArray = arr =>
            (arr || []).filter(v => v !== null && !Number.isNaN(v));

        const avg = arr => {
            const vals = cleanArray(arr);
            if (!vals.length) return null;
            return vals.reduce((a, b) => a + b, 0) / vals.length;
        };

        const sum = arr => {
            const vals = cleanArray(arr);
            if (!vals.length) return null;
            return vals.reduce((a, b) => a + b, 0);
        };

        const maxWithIndex = arr => {
            const vals = arr || [];
            let maxVal = null;
            let idx = -1;
            vals.forEach((v, i) => {
                if (v === null || Number.isNaN(v)) return;
                if (maxVal === null || v > maxVal) {
                    maxVal = v;
                    idx = i;
                }
            });
            return { value: maxVal, index: idx };
        };

        const minWithIndex = arr => {
            const vals = arr || [];
            let minVal = null;
            let idx = -1;
            vals.forEach((v, i) => {
                if (v === null || Number.isNaN(v)) return;
                if (minVal === null || v < minVal) {
                    minVal = v;
                    idx = i;
                }
            });
            return { value: minVal, index: idx };
        };

        function describeDate(label) {
            if (!label) return '';
            const d = new Date(label);
            if (!Number.isNaN(d.getTime())) {
                const today = new Date();
                const sameDay =
                    d.getFullYear() === today.getFullYear() &&
                    d.getMonth() === today.getMonth() &&
                    d.getDate() === today.getDate();

                if (sameDay) return ' (hoy)';
            }
            return ' (' + label + ')';
        }

        // Convertimos viento a km/h (si viene en m/s)
        const WIND_KMH = (CL_WIND || []).map(v =>
            (v !== null && !Number.isNaN(v)) ? v * 3.6 : null
        );

        // ==== Pintar KPIs ====
        const kpiTempMean     = document.getElementById('kpi-temp-mean');
        const kpiTempMeanSub  = document.getElementById('kpi-temp-mean-sub');
        const kpiTempMax      = document.getElementById('kpi-temp-max');
        const kpiTempMaxSub   = document.getElementById('kpi-temp-max-sub');
        const kpiTempMin      = document.getElementById('kpi-temp-min');
        const kpiTempMinSub   = document.getElementById('kpi-temp-min-sub');
        const kpiPrecip       = document.getElementById('kpi-precip');
        const kpiPrecipSub    = document.getElementById('kpi-precip-sub');
        const kpiWind         = document.getElementById('kpi-wind');

        const { value: maxTemp, index: idxMax } = maxWithIndex(CL_TEMP_MAX);
        const { value: minTemp, index: idxMin } = minWithIndex(CL_TEMP_MEAN);
        const labelMax = idxMax >= 0 ? CL_LABELS[idxMax] : null;
        const labelMin = idxMin >= 0 ? CL_LABELS[idxMin] : null;

        const avgTemp = avg(CL_TEMP_MEAN);
        if (kpiTempMean && avgTemp !== null) {
            kpiTempMean.textContent = avgTemp.toFixed(1) + ' °C';
        }
        if (kpiTempMeanSub) {
            kpiTempMeanSub.textContent = 'Promedio diario último año';
        }

        if (kpiTempMax && maxTemp !== null) {
            kpiTempMax.textContent = maxTemp.toFixed(1) + ' °C';
        }
        if (kpiTempMaxSub && maxTemp !== null) {
            kpiTempMaxSub.textContent =
                'Pico de calor' + describeDate(labelMax);
        }

        if (kpiTempMin && minTemp !== null) {
            kpiTempMin.textContent = minTemp.toFixed(1) + ' °C';
        }
        if (kpiTempMinSub && minTemp !== null) {
            kpiTempMinSub.textContent =
                'Día más frío' + describeDate(labelMin);
        }

        const totalPrecip = sum(CL_PRECIP);
        if (kpiPrecip && totalPrecip !== null) {
            kpiPrecip.textContent = totalPrecip.toFixed(0) + ' mm';
        }

        const { value: maxWind } = maxWithIndex(WIND_KMH);
        if (kpiWind && maxWind !== null) {
            kpiWind.textContent = maxWind.toFixed(0) + ' km/h';
        }

        if (kpiPrecipSub && totalPrecip !== null) {
            kpiPrecipSub.textContent =
                'Suma anual aproximada (' + totalPrecip.toFixed(0) + ' mm)';
        }

        // ==== series derivadas para nuevos gráficos ====
        const PRECIP_ACC = [];
        let acc = 0;
        (CL_PRECIP || []).forEach(v => {
            if (v !== null && !Number.isNaN(v)) acc += v;
            PRECIP_ACC.push(acc);
        });

        const TEMP_MEAN_SMOOTH = (CL_TEMP_MEAN || []).map((_, i) => {
            let count = 0, s = 0;
            const start = Math.max(0, i - 6); // ventana 7 días
            for (let j = start; j <= i; j++) {
                const v = CL_TEMP_MEAN[j];
                if (v !== null && !Number.isNaN(v)) {
                    s += v;
                    count++;
                }
            }
            return count ? s / count : null;
        });

        // ==== Resumen climático (texto y chips) ====
        const summaryTextEl  = document.getElementById('climate-summary-text');
        const summaryTagsEl  = document.getElementById('climate-summary-tags');

        function buildClimateSummary() {
            if (!summaryTextEl || !summaryTagsEl) return;

            const rainyDays = (CL_PRECIP || []).filter(v =>
                v !== null && !Number.isNaN(v) && v > 0.1
            ).length;
            const avgWind   = avg(WIND_KMH);

            let text = 'En el último año registrado para ' + CURRENT_ZONE + ', ' +
                'la temperatura media fue de ' +
                (avgTemp !== null ? avgTemp.toFixed(1) + ' °C' : '—') + ', ';

            if (maxTemp !== null) {
                text += 'con un máximo de ' + maxTemp.toFixed(1) + ' °C' +
                    describeDate(labelMax) + ' y ';
            } else {
                text += 'con un máximo no determinado y ';
            }

            if (minTemp !== null) {
                text += 'un mínimo de ' + minTemp.toFixed(1) + ' °C' +
                    describeDate(labelMin) + '. ';
            } else {
                text += 'un mínimo no determinado. ';
            }

            if (totalPrecip !== null) {
                text += 'En total se acumularon alrededor de ' +
                    totalPrecip.toFixed(0) + ' mm de lluvia en ' +
                    rainyDays + ' días con precipitación registrada. ';
            }

            if (maxWind !== null) {
                text += 'La racha de viento más intensa alcanzó aproximadamente ' +
                    maxWind.toFixed(0) + ' km/h.';
            }

            summaryTextEl.textContent = text;

            // Chips
            summaryTagsEl.innerHTML = '';
            if (totalPrecip !== null) {
                let chipClass = 'summary-chip';
                let label = 'Patrón hídrico intermedio';

                if (totalPrecip < 200) {
                    chipClass += ' summary-chip-dry';
                    label = 'Patrón más bien seco';
                } else if (totalPrecip > 800) {
                    chipClass += ' summary-chip-wet';
                    label = 'Patrón más bien húmedo';
                }

                const chip = document.createElement('span');
                chip.className = chipClass;
                chip.innerHTML = '<i class="fa-solid fa-droplet"></i>' + label;
                summaryTagsEl.appendChild(chip);
            }

            if (avgTemp !== null) {
                const chip = document.createElement('span');
                chip.className = 'summary-chip';
                chip.innerHTML =
                    '<i class="fa-solid fa-temperature-half"></i>' +
                    'Media anual: ' + avgTemp.toFixed(1) + ' °C';
                summaryTagsEl.appendChild(chip);
            }

            if (rainyDays) {
                const chip = document.createElement('span');
                chip.className = 'summary-chip summary-chip-wet';
                chip.innerHTML =
                    '<i class="fa-solid fa-cloud-rain"></i>' +
                    rainyDays + ' días con lluvia';
                summaryTagsEl.appendChild(chip);
            }

            if (avgWind !== null) {
                const chip = document.createElement('span');
                chip.className = 'summary-chip summary-chip-windy';
                chip.innerHTML =
                    '<i class="fa-solid fa-wind"></i>' +
                    'Viento medio máx.: ' + avgWind.toFixed(0) + ' km/h';
                summaryTagsEl.appendChild(chip);
            }
        }

        buildClimateSummary();

        // ==== Helpers para gráficos ====
        function makeLineChart(canvasId, label, data, colorLine, colorFill, unit) {
            const canvas = document.getElementById(canvasId);
            if (!canvas || typeof Chart === 'undefined') return;
            const ctx = canvas.getContext('2d');

            const gradient = ctx.createLinearGradient(0, 0, 0, 220);
            gradient.addColorStop(0, colorFill);
            gradient.addColorStop(1, 'rgba(255,255,255,0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: CL_LABELS,
                    datasets: [{
                        label: label,
                        data: data,
                        borderColor: colorLine,
                        backgroundColor: gradient,
                        tension: 0.25,
                        pointRadius: 0,
                        borderWidth: 2,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#111827',
                            borderColor: '#4b5563',
                            borderWidth: 1,
                            titleColor: '#e5e7eb',
                            bodyColor: '#f9fafb',
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                label: ctx => {
                                    const v = ctx.parsed.y;
                                    if (v === null || Number.isNaN(v)) return '';
                                    return v.toFixed(1) + ' ' + unit;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: false,
                            grid: { display: false }
                        },
                        y: {
                            grid: { color: 'rgba(148,163,184,0.18)', drawBorder: false },
                            ticks: { color: '#6b7280' }
                        }
                    }
                }
            });
        }

        function makeBarChart(canvasId, label, data, colorBar, unit) {
            const canvas = document.getElementById(canvasId);
            if (!canvas || typeof Chart === 'undefined') return;
            const ctx = canvas.getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: CL_LABELS,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: colorBar,
                        borderColor: colorBar,
                        borderWidth: 1,
                        borderRadius: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#111827',
                            borderColor: '#4b5563',
                            borderWidth: 1,
                            titleColor: '#e5e7eb',
                            bodyColor: '#f9fafb',
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                label: ctx => {
                                    const v = ctx.parsed.y;
                                    if (v === null || Number.isNaN(v)) return '';
                                    return v.toFixed(1) + ' ' + unit;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: false,
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(148,163,184,0.18)', drawBorder: false },
                            ticks: { color: '#6b7280' }
                        }
                    }
                }
            });
        }

        // ==== Dibujar gráficos ====
        // Temperaturas
        makeLineChart(
            'chart-temp-mean',
            '°C promedio',
            CL_TEMP_MEAN,
            'rgba(249,115,22,1)',
            'rgba(249,115,22,0.35)',
            '°C'
        );

        makeLineChart(
            'chart-temp-max',
            '°C máxima',
            CL_TEMP_MAX,
            'rgba(239,68,68,1)',
            'rgba(239,68,68,0.30)',
            '°C'
        );

        // Precipitaciones (mm)
        makeBarChart(
            'chart-precip',
            'Precipitación',
            CL_PRECIP,
            'rgba(59,130,246,0.80)',
            'mm'
        );

        // Viento (km/h) usando la serie convertida
        makeBarChart(
            'chart-wind',
            'Viento máx.',
            WIND_KMH,
            'rgba(16,185,129,0.80)',
            'km/h'
        );

        // Precipitación acumulada
        makeLineChart(
            'chart-precip-acc',
            'Prec. acumulada',
            PRECIP_ACC,
            'rgba(37,99,235,1)',
            'rgba(37,99,235,0.25)',
            'mm'
        );

        // Tendencia temperatura (media móvil)
        makeLineChart(
            'chart-temp-mean-smooth',
            'Temp. prom. (7 días)',
            TEMP_MEAN_SMOOTH,
            'rgba(234,88,12,1)',
            'rgba(234,88,12,0.25)',
            '°C'
        );
    });
})();
