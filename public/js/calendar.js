import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";

document.addEventListener("DOMContentLoaded", function () {
    var calendarEl = document.getElementById("calendar");
    var calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin],
        initialView: "dayGridMonth",
        events: tasks.map((task) => ({
            title: task.title,
            start: task.fecha_inicio,
            end: task.fecha_fin,
            url: task.url,
        })),
    });
    calendar.render();
});
