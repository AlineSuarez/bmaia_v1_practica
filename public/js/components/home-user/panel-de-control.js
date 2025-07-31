document.addEventListener("DOMContentLoaded", function () {
    // Animación de carga
    setTimeout(function () {
        document.getElementById("dashboard-loader").classList.add("fade-out");
        setTimeout(function () {
            document.getElementById("dashboard-loader").style.display = "none";
            document.getElementById("main-contenload").style.display = "block";
            document.getElementById("main-contenload").classList.add("fade-in");
        }, 800);
    }, 1200);

    // Inicializar dropdowns
    const dropdownButtons = document.querySelectorAll(
        "#dashboard-container .btn-filter"
    );
    dropdownButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const dropdownMenu = this.nextElementSibling;
            dropdownMenu.classList.toggle("show");
        });
    });

    // Cerrar dropdowns al hacer clic fuera
    document.addEventListener("click", function (event) {
        if (!event.target.matches("#dashboard-container .btn-filter")) {
            const dropdowns = document.querySelectorAll(
                "#dashboard-container .dashboard-dropdown-menu"
            );
            dropdowns.forEach((dropdown) => {
                if (dropdown.classList.contains("show")) {
                    dropdown.classList.remove("show");
                }
            });
        }
    });

    // Inicializar las barras de progreso con animación
    const progressBars = document.querySelectorAll(
        "#dashboard-container .progress-bar"
    );
    progressBars.forEach((bar) => {
        const width = bar.style.width;
        bar.style.width = "0";
        setTimeout(() => {
            bar.style.width = width;
        }, 300);
    });
});

document.addEventListener("DOMContentLoaded", function () {
    // Animación de carga
    setTimeout(function () {
        document.getElementById("dashboard-loader").classList.add("fade-out");
        setTimeout(function () {
            document.getElementById("dashboard-loader").style.display = "none";
            document.getElementById("main-contenload").style.display = "block";
            document.getElementById("main-contenload").classList.add("fade-in");
        }, 800);
    }, 1200);

    // Inicializar dropdowns
    const dropdownButtons = document.querySelectorAll(
        "#dashboard-container .btn-filter"
    );
    dropdownButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const dropdownMenu = this.nextElementSibling;
            dropdownMenu.classList.toggle("show");
        });
    });

    // Cerrar dropdowns al hacer clic fuera
    document.addEventListener("click", function (event) {
        if (!event.target.matches("#dashboard-container .btn-filter")) {
            const dropdowns = document.querySelectorAll(
                "#dashboard-container .dashboard-dropdown-menu"
            );
            dropdowns.forEach((dropdown) => {
                if (dropdown.classList.contains("show")) {
                    dropdown.classList.remove("show");
                }
            });
        }
    });

    // Inicializar las barras de progreso con animación
    const progressBars = document.querySelectorAll(
        "#dashboard-container .progress-bar"
    );
    progressBars.forEach((bar) => {
        const width = bar.style.width;
        bar.style.width = "0";
        setTimeout(() => {
            bar.style.width = width;
        }, 300);
    });

    // --- CLIMA ---
    const apiKey = "de61de99d65654be2bf826e37b82888a";
    const weatherMsg = document.getElementById("weather-msg-js");
    const weatherCards = document.getElementById("weather-cards-js");
    const weatherTitle = document.getElementById("weather-title-js");
    const datePicker = document.getElementById("weather-date-picker");
    const headerTemp = document.getElementById("header-temp");
    const headerWeather = document.getElementById("header-weather");
    let volverBtn = null;
    const iconColors = {
        Clear: "#FFD600",
        Clouds: "#90A4AE",
        Rain: "#2196F3",
        Drizzle: "#4FC3F7",
        Thunderstorm: "#FF7043",
        Snow: "#90CAF9",
        Mist: "#B0BEC5",
    };
    const iconos = {
        Clear: "fa-sun",
        Clouds: "fa-cloud",
        Rain: "fa-cloud-showers-heavy",
        Drizzle: "fa-cloud-rain",
        Thunderstorm: "fa-bolt",
        Snow: "fa-snowflake",
        Mist: "fa-smog",
    };
    // Cambiar a 5 días
    const dias = [
        "Hoy",
        "Mañana",
        "Pasado mañana",
        "En 3 días",
        "En 4 días",
        "En 5 días",
    ];

    // Cambia el array de días por los nombres de los días de la semana en español
    const diasSemana = [
        "Domingo",
        "Lunes",
        "Martes",
        "Miércoles",
        "Jueves",
        "Viernes",
        "Sábado",
    ];

    function reverseGeocode(lat, lon, callback) {
        fetch(`/reverse-geocode?lat=${lat}&lon=${lon}`)
            .then((res) => res.json())
            .then((data) => {
                if (data && data.address) {
                    let lugar =
                        data.address.village ||
                        data.address.town ||
                        data.address.city ||
                        data.address.hamlet ||
                        data.address.county ||
                        "";
                    let region = data.address.state || "";
                    let pais = data.address.country_code
                        ? data.address.country_code.toUpperCase()
                        : "";
                    callback(
                        `${lugar}${region ? ", " + region : ""}${
                            pais ? ", " + pais : ""
                        }`
                    );
                } else {
                    callback("");
                }
            })
            .catch(() => callback(""));
    }

    let days = {};
    let dayKeys = [];
    let daysArray = [];

    function formatFecha(fechaISO) {
        const fecha = new Date(fechaISO);
        const dia = String(fecha.getDate()).padStart(2, "0");
        const mes = String(fecha.getMonth() + 1).padStart(2, "0");
        const anio = fecha.getFullYear();
        return `${dia}/${mes}/${anio}`;
    }

    function renderMainCards() {
        let html = "";
        for (let i = 0; i < 6; i++) {
            if (!daysArray[i]) {
                // Si no hay datos para este día, muestra tarjeta vacía
                let fechaFormateada = dayKeys[i] ? formatFecha(dayKeys[i]) : "";
                let nombreDia = dayKeys[i]
                    ? diasSemana[new Date(dayKeys[i]).getDay()]
                    : "";
                html += `<div class="weather-card${i === 0 ? " today" : ""}">
      <div class="weather-header">
      <div class="weather-day">${nombreDia}</div>
      <div class="weather-date">${fechaFormateada}</div>
      </div>
      <div class="weather-icon">
      <i class="fas fa-question" style="color:#ccc;font-size:2.2em"></i>
      </div>
      <div class="weather-temp">---</div>
      <div class="weather-details" style="margin-top:8px">
      <div class="weather-detail">No hay datos para este día.</div>
      </div>
      </div>`;
                continue;
            }
            const day = daysArray[i];
            const main = day.weather[0].main;
            const color = iconColors[main] || "#FFD600";
            let fechaFormateada = formatFecha(dayKeys[i]);
            let nombreDia = diasSemana[new Date(dayKeys[i]).getDay()];
            html += `<div class="weather-card${i === 0 ? " today" : ""}">
      <div class="weather-header">
      <div class="weather-day">${nombreDia}</div>
      <div class="weather-date">${fechaFormateada}</div>
      </div>
      <div class="weather-icon">
      <i class="fas ${
          iconos[main] || "fa-sun"
      }" style="color:${color};font-size:2.2em"></i>
      </div>
      <div class="weather-temp" style="font-weight:bold;font-size:1.4em">${Math.round(
          day.main.temp
      )}°C</div>
      <div class="weather-details" style="margin-top:8px">
      <div class="weather-detail" style="text-transform:capitalize">
      <i class="fas fa-info-circle"></i>
      ${day.weather[0].description}
      </div>
      <div class="weather-detail">
      <i class="fas fa-tint"></i>
      Humedad: ${day.main.humidity}%
      </div>
      </div>
    </div>`;
        }
        weatherCards.innerHTML = html;
        if (volverBtn) volverBtn.style.display = "none";
    }

    function renderSelectedDay(dateStr) {
        const idx = dayKeys.indexOf(dateStr);
        let html = "";
        let fechaFormateada = formatFecha(dateStr);
        let nombreDia = diasSemana[new Date(dateStr).getDay()];
        if (idx === -1) {
            html = `<div class="weather-card">
      <div class="weather-header">
      <div class="weather-day">${nombreDia}</div>
      <div class="weather-date">${fechaFormateada}</div>
      </div>
      <div class="weather-icon">
      <i class="fas fa-question" style="color:#ccc;font-size:2.2em"></i>
      </div>
      <div class="weather-temp">---</div>
      <div class="weather-details" style="margin-top:8px">
      <div class="weather-detail">No hay datos para este día.</div>
      </div>
      </div>`;
        } else {
            const day = daysArray[idx];
            const main = day.weather[0].main;
            const color = iconColors[main] || "#FFD600";
            html = `<div class="weather-card">
      <div class="weather-header">
      <div class="weather-day">${nombreDia}</div>
      <div class="weather-date">${fechaFormateada}</div>
      </div>
      <div class="weather-icon">
      <i class="fas ${
          iconos[main] || "fa-sun"
      }" style="color:${color};font-size:2.2em"></i>
      </div>
      <div class="weather-temp" style="font-weight:bold;font-size:1.4em">${Math.round(
          day.main.temp
      )}°C</div>
      <div class="weather-details" style="margin-top:8px">
      <div class="weather-detail" style="text-transform:capitalize">
      <i class="fas fa-info-circle"></i>
      ${day.weather[0].description}
      </div>
      <div class="weather-detail">
      <i class="fas fa-tint"></i>
      Humedad: ${day.main.humidity}%
      </div>
      </div>
      </div>`;
        }
        weatherCards.innerHTML = html;
        if (volverBtn) volverBtn.style.display = "inline-block";
    }

    function crearBotonVolver() {
        if (!volverBtn) {
            volverBtn = document.createElement("button");
            volverBtn.textContent = "Volver a la actualidad";
            volverBtn.className = "btn btn-warning";
            volverBtn.style.margin = "12px 0 0 0";
            volverBtn.onclick = function () {
                if (datePicker) datePicker.value = "";
                renderMainCards();
            };
            weatherCards.parentNode.insertBefore(
                volverBtn,
                weatherCards.nextSibling
            );
        }
        volverBtn.style.display = "none";
    }

    if (navigator.geolocation) {
        if (weatherMsg) weatherMsg.textContent = "Obteniendo ubicación...";
        navigator.geolocation.getCurrentPosition(
            function (position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                if (weatherMsg) weatherMsg.textContent = "Cargando clima...";
                fetch(
                    `https://api.openweathermap.org/data/2.5/forecast?lat=${lat}&lon=${lon}&appid=${apiKey}&units=metric&lang=es`
                )
                    .then((res) => res.json())
                    .then((data) => {
                        // Actualizar header con el clima de hoy
                        if (data.list && data.list.length > 0) {
                            const hoy = data.list[0];
                            const temp = Math.round(hoy.main.temp) + "°C";
                            const clima =
                                hoy.weather[0].description
                                    .charAt(0)
                                    .toUpperCase() +
                                hoy.weather[0].description.slice(1);
                            if (headerTemp) headerTemp.textContent = temp;
                            if (headerWeather)
                                headerWeather.textContent = clima;
                        }

                        reverseGeocode(lat, lon, function (lugarExacto) {
                            if (lugarExacto) {
                                weatherTitle.textContent = `${lugarExacto}`;
                            } else if (data.city && data.city.name) {
                                weatherTitle.textContent = `${data.city.name}, ${data.city.country}`;
                            } else {
                                weatherTitle.textContent =
                                    "Ubicación desconocida";
                            }
                        });

                        if (!data.list || !Array.isArray(data.list)) {
                            if (weatherMsg)
                                weatherMsg.textContent =
                                    "No se pudo obtener el clima.";
                            return;
                        }
                        // Tomar el primer pronóstico de cada día
                        days = {};
                        dayKeys = [];
                        daysArray = [];
                        data.list.forEach((item) => {
                            const date = new Date(item.dt * 1000)
                                .toISOString()
                                .split("T")[0];
                            if (!days[date]) {
                                days[date] = item;
                                dayKeys.push(date);
                                daysArray.push(item);
                            }
                        });

                        crearBotonVolver();
                        renderMainCards();

                        // Configurar el calendario
                        if (datePicker) {
                            datePicker.min = dayKeys[0];
                            datePicker.max = dayKeys[dayKeys.length - 1];
                            datePicker.value = "";
                            datePicker.onchange = function () {
                                if (datePicker.value) {
                                    renderSelectedDay(datePicker.value);
                                } else {
                                    renderMainCards();
                                }
                            };
                        }
                    })
                    .catch(() => {
                        if (weatherMsg)
                            weatherMsg.textContent =
                                "No se pudo obtener el clima.";
                    });
            },
            function () {
                if (weatherMsg)
                    weatherMsg.textContent = "No se pudo obtener la ubicación.";
            }
        );
    } else {
        if (weatherMsg)
            weatherMsg.textContent =
                "La geolocalización no está soportada por su navegador.";
    }
});
