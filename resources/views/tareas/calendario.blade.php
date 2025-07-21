<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('./css/components/home-user/tasks/calendario.css') }}">
</head>

<body>
    <div class="calendar-panel">
        <h3 class="calendar-title">Calendario de Tareas</h3>

        <!-- Leyenda de colores -->
        <div id="leyenda-calendario">
            <span class="leyenda-color pendiente"></span> Pendiente
            <span class="leyenda-color progreso"></span> En Progreso
            <span class="leyenda-color completada"></span> Completada
            <span class="leyenda-color vencida"></span> Vencida
        </div>

        <!-- Filtros para tareas -->
        <div id="filtros-calendario">
            <select id="filtro-estado">
                <option value="">Todos los estados</option>
                <option value="Pendiente">Pendiente</option>
                <option value="En progreso">En Progreso</option>
                <option value="Completada">Completada</option>
                <option value="Vencida">Vencida</option>
            </select>
            <select id="filtro-prioridad">
                <option value="">Todas las prioridades</option>
                <option value="baja">Baja</option>
                <option value="media">Media</option>
                <option value="alta">Alta</option>
                <option value="urgente">Urgente</option>
            </select>
        </div>

        <!-- Contenedor para alertas personalizadas -->
        <div id="alertas-container"></div>

        <!-- Calendario -->
        <div id="calendar"></div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>
    <script src="{{ asset('js/fullcalendar-init.js') }}"></script>
</body>

</html>