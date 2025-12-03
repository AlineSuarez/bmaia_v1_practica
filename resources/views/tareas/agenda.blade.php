{{-- Meta tags requeridos --}}

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('css/components/home-user/tasks/agenda.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>


<div class="container-agenda">
    {{-- Header de Agenda --}}
    <div class="task-list-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="header-title">
                    <i class="fa-solid fa-calendar-days"></i>
                    Agenda de Tareas
                </h1>
                <p class="header-subtitle">Visualiza y actualiza el estado de tus tareas mediante el calendario de tareas mensual</p>
            </div>
        </div>
    </div>
    <div class="agenda-content">
        <!-- Sección del Calendario -->
        <div class="calendar-section">
            <div class="calendar-header">
                <h2 id="monthYear">Noviembre 2025</h2>
                <div class="navegador-contenedor">
                    <i class="fa fa-calendar-alt" id="navegador-meses"></i>

                    <div id="opciones-meses" class="meses-container hidden">
                        @php
                            $meses = [
                                'Enero', 'Febrero', 'Marzo', 'Abril', 
                                'Mayo', 'Junio', 'Julio', 'Agosto', 
                                'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                            ];
                        @endphp

                        @foreach ($meses as $mes)
                            <span class="mes">{{ $mes }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Días de la Semana -->
            <div class="weekdays">
                @php
                    $dias = ['Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa', 'Do'];
                @endphp

                @foreach ($dias as $dia)
                    <span class="weekday">{{ $dia }}</span>
                @endforeach
            </div>

            <div class="calendar-grid" id="calendarGrid">
                <!-- Los días se generarán dinámicamente con JavaScript -->
            </div>

            <div class="calendar-navigation">
                <button class="nav-button" id="prevMonth">◄ Anterior</button>
                <button class="nav-button" id="nextMonth">Siguiente ►</button>
            </div>
        </div>

        <!-- Sección de Tareas -->
        <div class="tasks-section">
            <div class="tasks-header">
                <div>
                    <h2>Tareas del día</h2>
                    <div class="date-subtitle" id="dateSubtitle">Selecciona un día</div>
                </div>
                
                <div class="google-calendar">
                    <button class="google-calendar-btn" id="GoogleCalendarConnect">
                        <i class="fa-brands fa-google" aria-hidden="true"></i>
                        Conectar con Google Calendar</button> 
                </div>
            </div>

            <div class="tasks-list" id="tasksList">
                {{-- Las tareas se cargarán dinámicamente con JavaScript --}}
                <div class="empty-tasks">
                    <i class="fa fa-calendar-check" style="font-size: 3rem; color: #ccc;"></i>
                    <p>Selecciona un día para ver las tareas</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de Progreso de Sincronización --}}
<div id="syncProgressModal" class="sync-modal" style="display: none;">
    <div class="sync-modal-content">
        <div class="sync-header">
            <i class="fa-brands fa-google" style="color: #4285f4; font-size: 2rem;"></i>
            <h3>Sincronizando con Google Calendar</h3>
        </div>
        <div class="sync-body">
            <p id="syncMessage">Preparando sincronización...</p>
            <div class="progress-container">
                <div class="loader" style="display: none;"></div>
                <div class="progress-bar">
                    <div id="syncProgressBar" class="progress-fill"></div>
                </div>
                <span id="syncPercentage">0%</span>
            </div>
            <p id="syncDetails" class="sync-details"></p>
        </div>
    </div>
</div>

{{-- Pasar las tareas al JavaScript --}}
<script>
    window.tareasData = @json($subtareas);
    window.googleCalendarJustConnected = @json(session('google_calendar_connected', false));
</script>

<script src="{{ asset('js/components/home-user/tasks/agenda.js') }}?v={{ time() }}"></script>