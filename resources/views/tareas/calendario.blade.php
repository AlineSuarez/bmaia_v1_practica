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

<style>
    .calendar-panel {
        max-width: 900px;
        margin: auto;
    }

    .calendar-title {
        text-align: center;
        margin-bottom: 1rem;
    }

    #leyenda-calendario {
        margin-bottom: 1rem;
        display: flex;
        gap: 1.5rem;
        justify-content: center;
    }

    .leyenda-color {
        width: 18px;
        height: 18px;
        border-radius: 4px;
        display: inline-block;
        margin-right: 6px;
        vertical-align: middle;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: background 0.3s ease;
        cursor: default;
    }

    .leyenda-color.pendiente {
        background: #ffe082;
    }

    .leyenda-color.progreso {
        background: #81d4fa;
    }

    .leyenda-color.completada {
        background: #a5d6a7;
    }

    .leyenda-color.vencida {
        background: #ef9a9a;
    }

    #filtros-calendario {
        margin-bottom: 1rem;
        display: flex;
        gap: 1rem;
        justify-content: center;
    }

    #filtros-calendario select {
        padding: 0.5rem;
        border-radius: 6px;
        border: 1px solid #ddd;
        font-size: 1rem;
    }

    #calendar {
        min-height: 500px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
        padding: 1rem;
    }

    .alerta-miel {
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
        background: #e3f2fd;
        color: #1976d2;
        font-size: 1.1rem;
    }

    .cerrar-alerta {
        background: none;
        border: none;
        font-size: 1.3rem;
        cursor: pointer;
        color: inherit;
    }
</style>