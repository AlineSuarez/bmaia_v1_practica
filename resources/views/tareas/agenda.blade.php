{{-- filepath: c:\Users\JKL528\Documents\proyecto\bmaia_v1_practica\resources\views\tareas\agenda.blade.php --}}
{{-- Meta tags requeridos --}} 
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('css/components/home-user/tasks/agenda.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container-agenda">
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
                <h2>Tareas del día</h2>
                <div class="date-subtitle" id="dateSubtitle">Selecciona un día</div>
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

    {{-- Pasar las tareas al JavaScript --}}
    <script>
        window.tareasData = @json($subtareas);
    </script>

    <script src="{{ asset('js/components/home-user/tasks/agenda.js') }}"></script>
</body>