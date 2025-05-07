document.addEventListener("DOMContentLoaded", function () {
    const calendarEl = document.getElementById("calendar");
    if (!calendarEl) return console.warn("No existe #calendar en la página.");
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        locale: "es",
        headerToolbar: {
            left:   "prev,next today",
            center: "title",
            right:  "dayGridMonth,timeGridWeek,timeGridDay"
        },
        // indica la URL de tu JSON
        events: "/tareas/json",
        // cuando pinchen en un evento, sacamos sus extendedProps
        eventClick(info) {
            const props = info.event.extendedProps;
            alert(
            `Tarea: ${info.event.title}\n` +
            `Estado: ${props.estado}\n` +
            `Prioridad: ${props.prioridad}\n` +
            `Descripción: ${props.description || '—'}`
            );
        },
        eventDidMount(info) {
            const colores = {
            urgente:        "#dc3545",
            alta:           "#ffc107",
            media:          "#0dcaf0",
            baja:           "#198754",
            "no-prioritaria":"#6c757d"
            };
            const pri = info.event.extendedProps.prioridad;
            if (colores[pri]) {
            info.el.style.backgroundColor = colores[pri];
            }
        },
        navLinks:    true,
        dayMaxEvents:true
        });
    
        calendar.render();
    
        // DEBUG
        console.log("Eventos cargados:", calendar.getEvents().map(e => e.toPlainObject()));
    });  