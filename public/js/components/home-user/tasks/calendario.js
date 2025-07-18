document.addEventListener("DOMContentLoaded", () => {
    const calendarEl = document.getElementById("calendar");
    if (!calendarEl) return;

    const calendarConfig = {
        initialView: "dayGridMonth",
        events: window.calendarEvents || [],
        locale: "es",
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,timeGridDay",
        },
        eventClick: (info) => {
            // Tu lógica de modal o alerta aquí
        },
        eventTimeFormat: {
            hour: "2-digit",
            minute: "2-digit",
            meridiem: false,
            hour12: false,
        },
    };

    new FullCalendar.Calendar(calendarEl, calendarConfig).render();
});
