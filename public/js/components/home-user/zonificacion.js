document.addEventListener("DOMContentLoaded", function () {
    window.initZonificacion = function (
        apiariosFijos,
        apiariosBase,
        apiariosTemporales,
        apiariosArchivados,
        apiKey
    ) {
        // Variables de paginación
        const ITEMS_PER_PAGE = 5;
        let currentPages = {
            fijos: 1,
            base: 1,
            temporales: 1,
            archivados: 1,
        };

        // Función para paginar datos
        function paginateData(data, page, itemsPerPage = ITEMS_PER_PAGE) {
            const startIndex = (page - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            return {
                data: data.slice(startIndex, endIndex),
                totalPages: Math.ceil(data.length / itemsPerPage),
                currentPage: page,
                totalItems: data.length,
            };
        }

        // Función para FORZAR la eliminación de scroll vertical
        function forceRemoveVerticalScroll() {
            // Elementos que NO deben tener scroll vertical
            const elementsToFix = [
                ".table-responsive",
                ".tab-content",
                ".view-container",
                ".tabs-container",
                ".apiary-info-section",
                ".zonificacion-container",
            ];

            elementsToFix.forEach((selector) => {
                const elements = document.querySelectorAll(selector);
                elements.forEach((element) => {
                    element.style.overflowY = "visible";
                    element.style.maxHeight = "none";
                    element.style.height = "auto";
                });
            });
        }

        // Función para renderizar tabla paginada - ACTUALIZADA
        function renderPaginatedTable(tabName, apiarios, tableSelector) {
            const paginatedResult = paginateData(
                apiarios,
                currentPages[tabName]
            );
            const tbody = document.querySelector(`${tableSelector} tbody`);

            if (!tbody) return;

            // Limpiar tabla
            tbody.innerHTML = "";

            if (paginatedResult.data.length === 0) {
                const colSpan =
                    tabName === "temporales" || tabName === "archivados"
                        ? 7
                        : 8;
                tbody.innerHTML = `<tr><td colspan="${colSpan}" class="text-center">No hay apiarios ${tabName} registrados</td></tr>`;

                // Forzar eliminación de scroll después de renderizar
                setTimeout(forceRemoveVerticalScroll, 10);
                return;
            }

            // Renderizar filas
            paginatedResult.data.forEach((apiario) => {
                const row = document.createElement("tr");
                row.setAttribute("data-id", apiario.id);
                row.className = `apiary-row apiary-${
                    tabName.slice(0, -1) === "base"
                        ? "base"
                        : tabName.slice(0, -1)
                }`;

                let rowHTML = `
                    <td>${apiario.nombre}</td>
                    <td>
                        <span title="Lat: ${apiario.latitud}, Lon: ${
                    apiario.longitud
                }" class="coordinates-tooltip">
                            ${parseFloat(apiario.latitud).toFixed(
                                5
                            )}, ${parseFloat(apiario.longitud).toFixed(5)}
                        </span>
                    </td>
                    <td>${apiario.num_colmenas}</td>
                `;

                // Añadir columna de fotografía solo para fijos y base
                if (tabName === "fijos" || tabName === "base") {
                    rowHTML += `
                        <td>
                            ${
                                apiario.foto
                                    ? `<img src="storage/${apiario.foto}" alt="Foto Apiario" class="apiary-image">`
                                    : ""
                            }
                        </td>
                    `;
                }

                rowHTML += `
        <td class="temp-data weather-data">Cargando...</td>
        <td class="humidity-data weather-data">Cargando...</td>
        <td class="weather-desc weather-data">Cargando...</td>
    `;

                // Solo agregar columna de acciones si NO es temporales
                if (tabName !== "temporales") {
                    rowHTML += `
        <td>
            <div class="action-buttons">
                <button class="action-btn locate-btn" title="Localizar en mapa"
                    data-lat="${apiario.latitud}" data-lon="${apiario.longitud}">
                    <i class="fa-solid fa-location-crosshairs"></i>
                </button>
            </div>
        </td>
        `;
                }

                row.innerHTML = rowHTML;
                tbody.appendChild(row);
            });

            // Renderizar controles de paginación
            renderPaginationControls(tabName, paginatedResult, "table");

            // Forzar eliminación de scroll después de renderizar
            setTimeout(forceRemoveVerticalScroll, 10);
        }

        // Función para renderizar tarjetas paginadas
        function renderPaginatedCards(tabName, apiarios, cardsSelector) {
            const paginatedResult = paginateData(
                apiarios,
                currentPages[tabName]
            );
            const cardsContainer = document.querySelector(cardsSelector);

            if (!cardsContainer) return;

            // Limpiar contenedor
            cardsContainer.innerHTML = "";

            if (paginatedResult.data.length === 0) {
                cardsContainer.innerHTML = `<p class="text-center">No hay apiarios ${tabName} registrados</p>`;
                return;
            }

            // Renderizar tarjetas
            paginatedResult.data.forEach((apiario) => {
                const card = document.createElement("div");
                card.className = "apiary-card";
                card.setAttribute("data-id", apiario.id);

                card.innerHTML = `
    <div class="card-header">
        <h3>${apiario.nombre}</h3>
        <div class="card-actions">
            ${
                tabName !== "temporales"
                    ? `<button class="card-action locate-btn" title="Localizar en mapa"
                        data-lat="${apiario.latitud}" data-lon="${apiario.longitud}">
                        <i class="fa-solid fa-location-crosshairs"></i>
                    </button>`
                    : ""
            }
        </div>
    </div>
    ${
        (tabName === "fijos" || tabName === "base") && apiario.foto
            ? `
    <div class="card-image">
        <img src="storage/${apiario.foto}" alt="Foto Apiario">
    </div>
`
            : tabName === "fijos" || tabName === "base"
            ? `
    <div class="card-image">
        <div class="no-image">
            <i class="fa-solid fa-image-slash"></i>
        </div>
    </div>
`
            : ""
    }
    <div class="card-body">
        <div class="card-info">
            <div class="info-item">
                <i class="fa-solid fa-location-dot"></i>
                <span title="Lat: ${apiario.latitud}, Lon: ${
                    apiario.longitud
                }" class="coordinates-tooltip">
                    ${parseFloat(apiario.latitud).toFixed(5)}, ${parseFloat(
                    apiario.longitud
                ).toFixed(5)}
                </span>
            </div>
            <div class="info-item">
                <i class="fa-solid fa-cubes-stacked"></i>
                <span>${apiario.num_colmenas} colmenas</span>
            </div>
            <div class="info-item">
                <i class="fa-solid fa-temperature-half"></i>
                <span class="temp-data">Cargando...</span>
            </div>
            <div class="info-item">
                <i class="fa-solid fa-droplet"></i>
                <span class="humidity-data">Cargando...</span>
            </div>
            <div class="info-item">
                <span class="weather-desc">Cargando...</span>
            </div>
        </div>
    </div>
`;

                cardsContainer.appendChild(card);
            });

            // Renderizar controles de paginación
            renderPaginationControls(tabName, paginatedResult, "cards");
        }

        // Función para renderizar controles de paginación
        function renderPaginationControls(tabName, paginatedResult, viewType) {
            const paginationId = `pagination-${tabName}-${viewType}`;
            let paginationContainer = document.getElementById(paginationId);

            // Si no existe el contenedor, crearlo
            if (!paginationContainer) {
                paginationContainer = document.createElement("div");
                paginationContainer.id = paginationId;
                paginationContainer.className = "pagination-container";

                const tabContent = document.querySelector(
                    `.${viewType}-view .tab-content[data-tab="${tabName}"]`
                );
                if (tabContent) {
                    tabContent.appendChild(paginationContainer);
                }
            }

            if (paginatedResult.totalPages <= 1) {
                paginationContainer.innerHTML = "";
                return;
            }

            let paginationHTML = `
                <div class="pagination-info">
                    Mostrando ${
                        (paginatedResult.currentPage - 1) * ITEMS_PER_PAGE + 1
                    } - ${Math.min(
                paginatedResult.currentPage * ITEMS_PER_PAGE,
                paginatedResult.totalItems
            )} de ${paginatedResult.totalItems} elementos
                </div>
                <div class="pagination-controls">
            `;

            // Botón anterior
            paginationHTML += `
                <button class="pagination-btn ${
                    paginatedResult.currentPage === 1 ? "disabled" : ""
                }" 
                        data-page="${paginatedResult.currentPage - 1}" 
                        data-tab="${tabName}" 
                        data-view="${viewType}"
                        ${paginatedResult.currentPage === 1 ? "disabled" : ""}>
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
            `;

            // Botones de páginas
            for (let i = 1; i <= paginatedResult.totalPages; i++) {
                if (
                    i === 1 ||
                    i === paginatedResult.totalPages ||
                    (i >= paginatedResult.currentPage - 1 &&
                        i <= paginatedResult.currentPage + 1)
                ) {
                    paginationHTML += `
                        <button class="pagination-btn ${
                            i === paginatedResult.currentPage ? "active" : ""
                        }" 
                                data-page="${i}" 
                                data-tab="${tabName}" 
                                data-view="${viewType}">
                            ${i}
                        </button>
                    `;
                } else if (
                    i === paginatedResult.currentPage - 2 ||
                    i === paginatedResult.currentPage + 2
                ) {
                    paginationHTML +=
                        '<span class="pagination-ellipsis">...</span>';
                }
            }

            // Botón siguiente
            paginationHTML += `
                <button class="pagination-btn ${
                    paginatedResult.currentPage === paginatedResult.totalPages
                        ? "disabled"
                        : ""
                }" 
                        data-page="${paginatedResult.currentPage + 1}" 
                        data-tab="${tabName}" 
                        data-view="${viewType}"
                        ${
                            paginatedResult.currentPage ===
                            paginatedResult.totalPages
                                ? "disabled"
                                : ""
                        }>
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            `;

            paginationHTML += "</div>";
            paginationContainer.innerHTML = paginationHTML;
        }

        // Event listener para paginación - ACTUALIZADO
        document.addEventListener("click", function (e) {
            if (
                e.target.closest(".pagination-btn") &&
                !e.target.closest(".pagination-btn").disabled
            ) {
                const btn = e.target.closest(".pagination-btn");
                const page = parseInt(btn.getAttribute("data-page"));
                const tab = btn.getAttribute("data-tab");
                const view = btn.getAttribute("data-view");

                if (!isNaN(page)) {
                    currentPages[tab] = page;

                    if (view === "table") {
                        const tableSelector = `.table-view .tab-content[data-tab="${tab}"] .apiary-table`;
                        const apiarios = getApiariosByTab(tab);
                        renderPaginatedTable(tab, apiarios, tableSelector);
                    } else if (view === "cards") {
                        const cardsSelector = `.cards-view .tab-content[data-tab="${tab}"] .apiary-cards`;
                        const apiarios = getApiariosByTab(tab);
                        renderPaginatedCards(tab, apiarios, cardsSelector);
                    }

                    // Forzar eliminación de scroll después del cambio de página
                    setTimeout(forceRemoveVerticalScroll, 50);

                    // Actualizar datos del clima para los nuevos elementos
                    setTimeout(() => {
                        const apiarios = getApiariosByTab(tab);
                        const paginatedResult = paginateData(
                            apiarios,
                            currentPages[tab]
                        );
                        paginatedResult.data.forEach((apiario) => {
                            if (apiario.latitud && apiario.longitud) {
                                fetchWeatherData(apiario);
                            }
                        });
                    }, 100);
                }
            }

            if (e.target.closest(".locate-btn")) {
                const btn = e.target.closest(".locate-btn");
                const lat = parseFloat(btn.getAttribute("data-lat"));
                const lon = parseFloat(btn.getAttribute("data-lon"));

                if (!isNaN(lat) && !isNaN(lon)) {
                    map.setView([lat, lon], 12);

                    const row = btn.closest("tr");
                    const card = btn.closest(".apiary-card");
                    let apiarioId;

                    if (row) {
                        apiarioId = parseInt(row.getAttribute("data-id"));
                    } else if (card) {
                        apiarioId = parseInt(card.getAttribute("data-id"));
                    }

                    if (apiarioId && markerMap.has(apiarioId)) {
                        const marker = markerMap.get(apiarioId);
                        marker.openPopup();
                    }

                    // --- NUEVO BLOQUEO DE BOTONES ---
                    // Habilita todos los botones primero
                    document
                        .querySelectorAll(".locate-btn")
                        .forEach((b) => (b.disabled = false));
                    // Deshabilita solo el botón del apiario activo
                    btn.disabled = true;
                }
            }
        });

        // Función auxiliar para obtener apiarios por pestaña
        function getApiariosByTab(tab) {
            switch (tab) {
                case "fijos":
                    return apiariosFijos;
                case "base":
                    return apiariosBase;
                case "temporales":
                    return apiariosTemporales;
                case "archivados":
                    return apiariosArchivados;
                default:
                    return [];
            }
        }

        // Función para inicializar paginación
        function initializePagination() {
            // Renderizar tablas paginadas
            renderPaginatedTable(
                "fijos",
                apiariosFijos,
                '.table-view .tab-content[data-tab="fijos"] .apiary-table'
            );
            renderPaginatedTable(
                "base",
                apiariosBase,
                '.table-view .tab-content[data-tab="base"] .apiary-table'
            );
            renderPaginatedTable(
                "temporales",
                apiariosTemporales,
                '.table-view .tab-content[data-tab="temporales"] .apiary-table'
            );
            renderPaginatedTable(
                "archivados",
                apiariosArchivados,
                '.table-view .tab-content[data-tab="archivados"] .apiary-table'
            );

            // Renderizar tarjetas paginadas
            renderPaginatedCards(
                "fijos",
                apiariosFijos,
                '.cards-view .tab-content[data-tab="fijos"] .apiary-cards'
            );
            renderPaginatedCards(
                "base",
                apiariosBase,
                '.cards-view .tab-content[data-tab="base"] .apiary-cards'
            );
            renderPaginatedCards(
                "temporales",
                apiariosTemporales,
                '.cards-view .tab-content[data-tab="temporales"] .apiary-cards'
            );
            renderPaginatedCards(
                "archivados",
                apiariosArchivados,
                '.cards-view .tab-content[data-tab="archivados"] .apiary-cards'
            );
        }

        const map = L.map("map").setView([-33.4489, -70.6693], 6);
        const beeIcon = L.icon({
            iconUrl: "/img/apiario.webp",
            iconSize: [38, 38],
            iconAnchor: [20, 20],
            popupAnchor: [0, -32],
        });

        // Traducción clima
        const weatherEs = {
            Clear: "Despejado",
            Clouds: "Nublado",
            Rain: "Lluvioso",
            Snow: "Nevado",
            Fog: "Neblina",
            Thunderstorm: "Tormenta",
            Drizzle: "Llovizna",
            Mist: "Neblina",
            Haze: "Neblina",
            Smoke: "Humo",
            Dust: "Polvo",
            Sand: "Arena",
            Ash: "Ceniza",
            Squall: "Chubasco",
            Tornado: "Tornado",
        };

        // Diccionario de íconos FontAwesome por código openweather
        const weatherIcons = {
            "01d": '<i class="fa-solid fa-sun"></i>',
            "01n": '<i class="fa-solid fa-moon"></i>',
            "02d": '<i class="fa-solid fa-cloud-sun"></i>',
            "02n": '<i class="fa-solid fa-cloud-moon"></i>',
            "03d": '<i class="fa-solid fa-cloud"></i>',
            "03n": '<i class="fa-solid fa-cloud"></i>',
            "04d": '<i class="fa-solid fa-cloud"></i>',
            "04n": '<i class="fa-solid fa-cloud"></i>',
            "09d": '<i class="fa-solid fa-cloud-rain"></i>',
            "09n": '<i class="fa-solid fa-cloud-rain"></i>',
            "10d": '<i class="fa-solid fa-cloud-sun-rain"></i>',
            "10n": '<i class="fa-solid fa-cloud-moon-rain"></i>',
            "11d": '<i class="fa-solid fa-bolt"></i>',
            "11n": '<i class="fa-solid fa-bolt"></i>',
            "13d": '<i class="fa-solid fa-snowflake"></i>',
            "13n": '<i class="fa-solid fa-snowflake"></i>',
            "50d": '<i class="fa-solid fa-smog"></i>',
            "50n": '<i class="fa-solid fa-smog"></i>',
        };

        // Capas del mapa
        const osm = L.tileLayer(
            "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
            { maxZoom: 40 }
        ).addTo(map);
        const esriSat = L.tileLayer(
            "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}"
        );
        L.control
            .layers(
                { "Mapa Base (OSM)": osm, "Satélite (Esri)": esriSat },
                null,
                { position: "topright" }
            )
            .addTo(map);

        const markers = L.featureGroup().addTo(map);
        const otrosGroup = L.layerGroup();
        const markerMap = new Map();
        const radioGroup = L.layerGroup().addTo(map); // Nuevo grupo para radios
        const radioMap = new Map(); // Para guardar los círculos por apiario.id

        // Variables para estadísticas
        let totalTemp = 0;
        let totalHumidity = 0;
        let countWithData = 0;
        let tempData = [];
        let humidityData = [];

        // Cache de datos climáticos
        const weatherCache = {}; // Guardar datos de clima por apiario.id

        // Función para obtener datos del clima
        function fetchWeatherData(apiario) {
            fetch(
                `https://api.openweathermap.org/data/2.5/weather?lat=${apiario.latitud}&lon=${apiario.longitud}&appid=${apiKey}&units=metric`
            )
                .then((res) => res.json())
                .then((data) => {
                    if (data.main) {
                        // Actualizar tablas - Buscar en todas las filas con el ID del apiario
                        const rows = document.querySelectorAll(
                            `tr[data-id="${apiario.id}"]`
                        );

                        rows.forEach((row) => {
                            const tempCell = row.querySelector(".temp-data");
                            const humidityCell =
                                row.querySelector(".humidity-data");
                            const weatherCell =
                                row.querySelector(".weather-desc");

                            if (tempCell) {
                                tempCell.textContent = `${data.main.temp.toFixed(
                                    1
                                )}°C`;

                                // Aplicar clases según rangos óptimos
                                tempCell.classList.remove(
                                    "temp-low",
                                    "temp-high",
                                    "temp-optimal"
                                );
                                if (data.main.temp < 18) {
                                    tempCell.classList.add("temp-low");
                                } else if (data.main.temp > 32) {
                                    tempCell.classList.add("temp-high");
                                } else {
                                    tempCell.classList.add("temp-optimal");
                                }
                            }

                            if (humidityCell) {
                                humidityCell.textContent = `${data.main.humidity}%`;

                                // Aplicar clases según rangos óptimos
                                humidityCell.classList.remove(
                                    "humidity-low",
                                    "humidity-high",
                                    "humidity-optimal"
                                );
                                if (data.main.humidity < 40) {
                                    humidityCell.classList.add("humidity-low");
                                } else if (data.main.humidity > 60) {
                                    humidityCell.classList.add("humidity-high");
                                } else {
                                    humidityCell.classList.add(
                                        "humidity-optimal"
                                    );
                                }
                            }

                            if (weatherCell) {
                                const icon = data.weather[0].icon;
                                const weatherDesc =
                                    weatherEs[data.weather[0].main] ||
                                    data.weather[0].main;
                                const faIcon = weatherIcons[icon] || "";
                                weatherCell.innerHTML = `${faIcon} ${weatherDesc}`;
                            }
                        });

                        // Actualizar tarjetas - Buscar en todas las tarjetas con el ID del apiario
                        const cards = document.querySelectorAll(
                            `.apiary-card[data-id="${apiario.id}"]`
                        );

                        cards.forEach((card) => {
                            const tempElement =
                                card.querySelector(".temp-data");
                            const humidityElement =
                                card.querySelector(".humidity-data");
                            const weatherElement =
                                card.querySelector(".weather-desc");

                            if (tempElement) {
                                tempElement.textContent = `${data.main.temp.toFixed(
                                    1
                                )}°C`;
                            }
                            if (humidityElement) {
                                humidityElement.textContent = `${data.main.humidity}%`;
                            }
                            if (weatherElement) {
                                const icon = data.weather[0].icon;
                                const weatherDesc =
                                    weatherEs[data.weather[0].main] ||
                                    data.weather[0].main;
                                const faIcon = weatherIcons[icon] || "";
                                weatherElement.innerHTML = `${faIcon} ${weatherDesc}`;
                            }
                        });

                        // Actualizar popup
                        const popupWeather = document.querySelector(
                            '.leaflet-popup .popup-weather[data-id="' +
                                apiario.id +
                                '"]'
                        );
                        if (popupWeather) {
                            popupWeather.innerHTML = `
                                <i class="fa-solid fa-temperature-half"></i> ${data.main.temp.toFixed(
                                    1
                                )}°C, 
                                <i class="fa-solid fa-droplet"></i> ${
                                    data.main.humidity
                                }%, 
                                ${weatherIcons[data.weather[0].icon] || ""} ${
                                weatherEs[data.weather[0].main] ||
                                data.weather[0].main
                            }
                            `;
                        }

                        // Actualizar estadísticas (solo si existen los elementos)
                        totalTemp += data.main.temp;
                        totalHumidity += data.main.humidity;
                        countWithData++;

                        tempData.push(data.main.temp);
                        humidityData.push(data.main.humidity);

                        // Actualizar promedios si existen los elementos
                        const avgTempElement =
                            document.getElementById("avg-temp");
                        const avgHumidityElement =
                            document.getElementById("avg-humidity");

                        if (countWithData > 0) {
                            if (avgTempElement) {
                                avgTempElement.textContent = `${(
                                    totalTemp / countWithData
                                ).toFixed(1)}°C`;
                            }
                            if (avgHumidityElement) {
                                avgHumidityElement.textContent = `${Math.round(
                                    totalHumidity / countWithData
                                )}%`;
                            }
                        }

                        weatherCache[apiario.id] = {
                            temp: data.main.temp,
                            humidity: data.main.humidity,
                            icon: data.weather[0].icon,
                            desc: data.weather[0].main,
                        };
                    }
                })
                .catch((err) => {
                    console.error("Error al obtener datos del clima:", err);
                });
        }

        // Función para obtener datos del clima para todos los apiarios
        function fetchWeatherForAllApiarios() {
            const todosApiarios = [
                ...apiariosFijos,
                ...apiariosBase,
                ...apiariosTemporales,
                ...apiariosArchivados,
            ];

            todosApiarios.forEach((apiario) => {
                if (apiario.latitud && apiario.longitud) {
                    fetchWeatherData(apiario);
                }
            });
        }

        // Agregar marcadores para todos los tipos (excluyendo archivados del mapa)
        [...apiariosFijos, ...apiariosBase].forEach((apiario) => {
            if (apiario.latitud && apiario.longitud) {
                const weatherHtml =
                    weatherCache[apiario.id] &&
                    typeof weatherCache[apiario.id].temp !== "undefined"
                        ? `<i class="fa-solid fa-temperature-half"></i> ${weatherCache[
                              apiario.id
                          ].temp.toFixed(1)}°C, 
       <i class="fa-solid fa-droplet"></i> ${
           weatherCache[apiario.id].humidity
       }%, 
       ${weatherIcons[weatherCache[apiario.id].icon] || ""} ${
                              weatherEs[weatherCache[apiario.id].desc] ||
                              weatherCache[apiario.id].desc
                          }`
                        : `<i class="fa-solid fa-cloud"></i> Cargando datos del clima...`;

                const marker = L.marker([apiario.latitud, apiario.longitud], {
                    icon: beeIcon,
                }).bindPopup(`
    <div class="custom-popup">
        ${
            apiario.foto
                ? `<img src="storage/${apiario.foto}" class="popup-image">`
                : ""
        }
        <h3>${apiario.nombre}</h3>
        <div class="popup-info">
            <p><i class="fa-solid fa-location-dot"></i> ${
                apiario.nombre_comuna || "Sin ubicación"
            }</p>
            <p><i class="fa-solid fa-cubes-stacked"></i> Colmenas: ${
                apiario.num_colmenas
            }</p>
            <p class="popup-weather" data-id="${apiario.id}">
                ${weatherHtml}
            </p>
        </div>
    </div>
`);
                marker.addTo(markers);
                markerMap.set(apiario.id, marker);

                // Determinar color del círculo según tipo
                let circleColor = "#f0941b"; // Default naranja para fijos
                if (apiario.tipo_apiario === "trashumante") {
                    if (apiario.es_temporal) {
                        circleColor = "#27ae60"; // Verde para temporales
                    } else {
                        circleColor = "#3498db"; // Azul para base
                    }
                }

                // Crear círculo pero NO agregarlo al mapa aún
                const circle = L.circle([apiario.latitud, apiario.longitud], {
                    color: circleColor,
                    fillColor: circleColor,
                    fillOpacity: 0.03, // Opacidad baja para el relleno
                    radius: 3000, // 3km
                    interactive: false,
                });
                radioMap.set(apiario.id, circle);
            }
        });

        // Agregar marcadores para apiarios archivados en el grupo de "otros"
        apiariosArchivados.forEach((apiario) => {
            if (apiario.latitud && apiario.longitud) {
                const redCircle = L.circle(
                    [apiario.latitud, apiario.longitud],
                    {
                        color: "#e74c3c",
                        fillColor: "#e74c3c",
                        fillOpacity: 0.2,
                        radius: 3500,
                        interactive: true,
                    }
                ).bindPopup(`
                    <div class="custom-popup other-popup">
                        <h3>Apiario Archivado</h3>
                        <div class="popup-info">
                            <p><i class="fa-solid fa-user"></i> ${
                                apiario.nombre
                            }</p>
                            <p><i class="fa-solid fa-location-dot"></i> ${
                                apiario.nombre_comuna || "Sin ubicación"
                            }</p>
                            <p><i class="fa-solid fa-cubes-stacked"></i> Colmenas: ${
                                apiario.num_colmenas || "N/A"
                            }</p>
                        </div>
                        ${
                            apiario.foto
                                ? `<img src="storage/${apiario.foto}" class="popup-image">`
                                : ""
                        }
                    </div>
                `);

                const redDot = L.circleMarker(
                    [apiario.latitud, apiario.longitud],
                    {
                        color: "#e74c3c",
                        radius: 4,
                        fillOpacity: 1,
                    }
                );

                otrosGroup.addLayer(redCircle);
                otrosGroup.addLayer(redDot);
            }
        });

        // Obtener datos del clima para todos los apiarios
        fetchWeatherForAllApiarios();

        // Toggle de otros apiarios (archivados)
        const toggleOthers = document.getElementById("toggle-others");
        if (toggleOthers) {
            toggleOthers.addEventListener("change", function () {
                if (this.checked) {
                    // Mostrar radios de todos los apiarios activos
                    radioMap.forEach((circle) => radioGroup.addLayer(circle));
                    // Mostrar archivados
                    otrosGroup.addTo(map);
                } else {
                    // Quitar radios
                    radioGroup.clearLayers();
                    // Quitar archivados
                    otrosGroup.removeFrom(map);
                }
            });
        }

        // Ajustar vista del mapa
        if (markers.getLayers().length > 0) {
            map.fitBounds(markers.getBounds(), { padding: [50, 50] });
        }

        // Sistema de pestañas
        const tabBtns = document.querySelectorAll(".tab-btn");
        tabBtns.forEach((btn) => {
            btn.addEventListener("click", function () {
                const tab = this.getAttribute("data-tab");

                if (this.classList.contains("active")) return;

                // Remover clase active de todos los botones y contenidos
                tabBtns.forEach((b) => b.classList.remove("active"));
                document
                    .querySelectorAll(".tab-content")
                    .forEach((t) => t.classList.remove("active"));

                // Activar pestaña seleccionada
                this.classList.add("active");

                // Activar contenido de pestaña en ambas vistas (tabla y tarjetas)
                document
                    .querySelectorAll(`.tab-content[data-tab="${tab}"]`)
                    .forEach((content) => content.classList.add("active"));

                // Resetear página actual cuando cambie de pestaña
                currentPages[tab] = 1;
            });
        });

        // Cambio entre vistas de tabla y tarjetas
        const viewBtns = document.querySelectorAll(".view-btn");
        viewBtns.forEach((btn) => {
            btn.addEventListener("click", function () {
                const view = this.getAttribute("data-view");

                if (this.classList.contains("active")) return;

                viewBtns.forEach((b) => b.classList.remove("active"));
                document
                    .querySelectorAll(".view-container")
                    .forEach((v) => v.classList.remove("active"));

                this.classList.add("active");
                document.querySelector(`.${view}-view`).classList.add("active");

                // Asegurar que la pestaña activa se mantenga en la nueva vista
                const activeTab = document.querySelector(".tab-btn.active");
                if (activeTab) {
                    const tab = activeTab.getAttribute("data-tab");

                    // Remover active de todos los contenidos
                    document
                        .querySelectorAll(".tab-content")
                        .forEach((t) => t.classList.remove("active"));

                    // Activar contenido de la pestaña activa en la nueva vista
                    document
                        .querySelectorAll(
                            `.${view}-view .tab-content[data-tab="${tab}"]`
                        )
                        .forEach((content) => content.classList.add("active"));
                }
            });
        });

        // Función para detectar cambios de tamaño de pantalla y actualizar comportamiento
        function updateTabBehavior() {
            const isMobile = window.innerWidth <= 768;
            const tabBtns = document.querySelectorAll(".tab-btn");

            tabBtns.forEach((btn) => {
                const tabText = btn.querySelector(".tab-text");

                if (isMobile) {
                    // En móvil, asegurar que el tooltip esté disponible
                    if (!btn.hasAttribute("data-tooltip") && tabText) {
                        btn.setAttribute(
                            "data-tooltip",
                            tabText.textContent.trim()
                        );
                    }
                } else {
                    // En desktop, el texto es visible y no necesitamos tooltip
                    if (!btn.hasAttribute("data-tooltip") && tabText) {
                        btn.setAttribute(
                            "data-tooltip",
                            tabText.textContent.trim()
                        );
                    }
                }
            });
        }

        // Llamar al cargar la página
        updateTabBehavior();

        // Llamar cuando cambie el tamaño de ventana
        window.addEventListener("resize", () => {
            updateTabBehavior();
            forceRemoveVerticalScroll(); // Forzar eliminación en resize
        });

        // Botones de localizar en mapa
        document.addEventListener("click", function (e) {
            if (e.target.closest(".locate-btn")) {
                const btn = e.target.closest(".locate-btn");
                const lat = parseFloat(btn.getAttribute("data-lat"));
                const lon = parseFloat(btn.getAttribute("data-lon"));

                if (!isNaN(lat) && !isNaN(lon)) {
                    map.setView([lat, lon], 12);

                    const row = btn.closest("tr");
                    const card = btn.closest(".apiary-card");
                    let apiarioId;

                    if (row) {
                        apiarioId = parseInt(row.getAttribute("data-id"));
                    } else if (card) {
                        apiarioId = parseInt(card.getAttribute("data-id"));
                    }

                    if (apiarioId && markerMap.has(apiarioId)) {
                        const marker = markerMap.get(apiarioId);
                        marker.openPopup();
                    }

                    // --- NUEVO BLOQUEO DE BOTONES ---
                    // Habilita todos los botones primero
                    document
                        .querySelectorAll(".locate-btn")
                        .forEach((b) => (b.disabled = false));
                    // Deshabilita solo el botón del apiario activo
                    btn.disabled = true;
                }
            }
        });

        // Mostrar/ocultar leyenda del mapa
        const legendBtn = document.getElementById("toggle-legend");
        const legend = document.querySelector(".map-legend");
        if (legendBtn && legend) {
            legendBtn.addEventListener("click", function () {
                legend.classList.toggle("visible");
            });
        }

        // Inicializar paginación después de configurar todo
        setTimeout(() => {
            initializePagination();

            // IMPORTANTE: Forzar eliminación de scroll al final
            setTimeout(forceRemoveVerticalScroll, 100);

            // Obtener datos del clima para la primera página de cada pestaña
            const firstPageApiarios = [
                ...paginateData(apiariosFijos, 1).data,
                ...paginateData(apiariosBase, 1).data,
                ...paginateData(apiariosTemporales, 1).data,
                ...paginateData(apiariosArchivados, 1).data,
            ];

            firstPageApiarios.forEach((apiario) => {
                if (apiario.latitud && apiario.longitud) {
                    fetchWeatherData(apiario);
                }
            });
        }, 100);

        if (toggleOthers) {
            toggleOthers.checked = false;
            radioGroup.clearLayers();
            otrosGroup.removeFrom(map);
        }

        markers.eachLayer(function (marker) {
            marker.on("popupopen", function (e) {
                const popupWeather = e.popup
                    .getElement()
                    .querySelector(".popup-weather");
                if (popupWeather) {
                    const apiarioId = parseInt(
                        popupWeather.getAttribute("data-id")
                    );
                    if (!isNaN(apiarioId)) {
                        // Mostrar SIEMPRE los datos en cache si existen
                        if (weatherCache[apiarioId]) {
                            const w = weatherCache[apiarioId];
                            const faIcon = weatherIcons[w.icon] || "";
                            const weatherDesc = weatherEs[w.desc] || w.desc;
                            popupWeather.innerHTML = `
                        <i class="fa-solid fa-temperature-half"></i> ${w.temp.toFixed(
                            1
                        )}°C, 
                        <i class="fa-solid fa-droplet"></i> ${w.humidity}%, 
                        ${faIcon} ${weatherDesc}
                    `;
                        }
                        // Si no hay datos, mostrar "Cargando..."
                        else {
                            popupWeather.innerHTML = `<i class="fa-solid fa-cloud"></i> Cargando datos del clima...`;
                        }
                        // Siempre vuelve a pedir el clima para actualizarlo
                        const apiario = [
                            ...apiariosFijos,
                            ...apiariosBase,
                        ].find((a) => a.id === apiarioId);
                        if (apiario && apiario.latitud && apiario.longitud) {
                            fetchWeatherData(apiario);
                        }
                    }
                }
            });
        });

        // Añadir esto antes de crear los círculos (dentro del foreach de apiariosFijos y apiariosBase)
        function getRadioConfig(apiario) {
            // Radio en metros y color según tipo
            if (apiario.tipo_apiario === "trashumante") {
                if (apiario.es_temporal) {
                    return { radius: 2000, color: "#27ae60" }; // Temporales: 2km, verde
                } else {
                    return { radius: 4000, color: "#3498db" }; // Base: 4km, azul
                }
            }
            return { radius: 3000, color: "#f0941b" }; // Fijos: 3km, naranja
        }

        // Crear círculos para radios de apiarios fijos y base
        [...apiariosFijos, ...apiariosBase].forEach((apiario) => {
            if (apiario.latitud && apiario.longitud) {
                const { radius, color } = getRadioConfig(apiario);

                // Crear círculo mejorado
                const circle = L.circle([apiario.latitud, apiario.longitud], {
                    color: color,
                    weight: 2.5,
                    fillColor: color,
                    fillOpacity: 0.07, // Más visible pero suave
                    radius: radius,
                    interactive: true, // Para tooltip
                    className: "apiary-radius-circle",
                }).bindTooltip(
                    `<b>${apiario.nombre}</b><br>Radio: ${(
                        radius / 1000
                    ).toFixed(1)} km`,
                    { direction: "top", sticky: true }
                );
                radioMap.set(apiario.id, circle);
            }
        });

        // Añade animación de pulso por CSS (agrega esto al final del archivo JS para inyectar el estilo)
        const style = document.createElement("style");
        style.innerHTML = `
            .leaflet-interactive.apiary-radius-circle {
                animation: apiaryPulse 2.5s infinite;
            }
            @keyframes apiaryPulse {
                0% { filter: drop-shadow(0 0 0px #fff); }
                50% { filter: drop-shadow(0 0 12px #fff8); }
                100% { filter: drop-shadow(0 0 0px #fff); }
            }
            `;
        document.head.appendChild(style);
    };
});
