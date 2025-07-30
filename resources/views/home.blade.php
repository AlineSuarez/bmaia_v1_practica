@extends('layouts.app')

@section('title', 'B-MaiA - Panel de Control Apícola')

@section('content')

  <head>
    <link href="{{ asset('./css/components/home-user/dashboard.css') }}" rel="stylesheet">
  </head>
  <!-- Verificar carga del middleware 
    <p>Formato en config: {{ config('app.date_format') }}</p>
    <p>Hoy es: @date(now())</p> -->

  <!-- Loader con animación de panal (fuera del contenedor principal) -->
  <div id="dashboard-loader">
    <div class="loader-container">
    <div class="honeycomb-loader">
      <div class="honeycomb-glow"></div>
      <div class="honeycomb-grid">
      @for ($i = 1; $i <= 13; $i++)
      <div class="honeycomb-cell" style="--i:{{ $i }}"></div>
    @endfor
      </div>
      <!-- Abejas animadas -->
      @for ($i = 1; $i <= 5; $i++)
      <div class="bee">
      <div class="bee-wing left"></div>
      <div class="bee-wing right"></div>
      </div>
    @endfor
    </div>
    <div class="loader-brand">B-MaiA</div>
    <div class="loader-text">Cargando panel de control</div>
    </div>
  </div>

  <!-- Contenedor principal que aísla los estilos del dashboard -->
  <div id="dashboard-container">
    <!-- Contenido del Dashboard -->
    <div class="dashboard-wrapper" id="main-contenload" style="display: none;">
    <!-- Header del Dashboard -->
    <header class="dashboard-header">
      <div class="header-content">
      <h1>Panel de Control</h1>
      </div>
      <div class="quick-stats">
      <div class="quick-stat">
        <div class="stat-icon">
        <i class="fas fa-calendar-day"></i>
        </div>
        <div class="stat-info">
        <span class="stat-label">Hoy</span>
        <span class="stat-value">@date(now())</span>
        </div>
      </div>
      <div class="quick-stat">
        <div class="stat-icon">
        <i class="fas fa-thermometer-half"></i>
        </div>
        <div class="stat-info">
        <span class="stat-label">Temperatura</span>
        <span class="stat-value" id="header-temp">--°C</span>
        </div>
      </div>
      <div class="quick-stat">
        <div class="stat-icon">
        <i class="fas fa-cloud-sun"></i>
        </div>
        <div class="stat-info">
        <span class="stat-label">Clima</span>
        <span class="stat-value" id="header-weather">---</span>
        </div>
      </div>
      </div>
    </header>

    <!-- Grid de Métricas Principales -->
    <div class="metrics-container">
      <div class="metrics-grid">
      <!-- 1. Apiarios Card -->
      <div class="metric-card apiarios">
        <a href="{{ route('apiarios') }}" class="metric-link">
        <div class="metric-header">
          <div class="metric-icon">
          <i class="fas fa-archive"></i>
          </div>
        </div>
        <div class="metric-content">
          <div class="metric-title">Apiarios</div>
          <div class="metric-value">{{ $totalApiarios }}</div>
          <div class="metric-progress">
          <div class="progress-bar" style="width: 75%"></div>
          </div>
          <div class="metric-detail">
          <span class="metric-badge">{{$totalColmenas}}</span> colmenas activas
          </div>
        </div>
        </a>
      </div>

      <!-- 2. Cuaderno de Campo (Inspecciones) Card -->
      <div class="metric-card inspecciones">
        <a href="{{ route('visitas') }}" class="metric-link">
        <div class="metric-header">
          <div class="metric-icon">
          <i class="fas fa-clipboard-check"></i>
          </div>
        </div>
        <div class="metric-content">
          <div class="metric-title">Cuaderno de Campo</div>
          <div class="metric-value">{{$visitas}}</div>
          <div class="metric-progress">
          <div class="progress-bar" style="width: 65%"></div>
          </div>
          <div class="metric-detail">
          <span class="metric-badge">2024/2025</span> temporada actual
          </div>
        </div>
        </a>
      </div>

      <!-- 3. Tareas Card -->
      <div class="metric-card tareas">
        <a href="{{ route('tareas') }}" class="metric-link">
        <div class="metric-header">
          <div class="metric-icon">
          <i class="fas fa-tasks"></i>
          </div>
        </div>
        <div class="metric-content">
          <div class="metric-title">Plan de Trabajo</div>
          <div class="metric-value">{{ $t_urgentes + $t_pendientes + $t_progreso }}</div>
          <div class="metric-progress">
          <div class="progress-bar" style="width: 45%"></div>
          </div>
          <div class="metric-detail">
          <div class="status-indicators">
            <div class="status-item">
            <span class="status-dot urgent"></span>
            <span class="status-count">{{ $t_urgentes }}</span>
            </div>
            <div class="status-item">
            <span class="status-dot pending"></span>
            <span class="status-count">{{ $t_pendientes }}</span>
            </div>
            <div class="status-item">
            <span class="status-dot progress"></span>
            <span class="status-count">{{ $t_progreso }}</span>
            </div>
          </div>
          </div>
        </div>
        </a>
      </div>

      <!-- 4. Zonificación Card -->
      <div class="metric-card zonificacion">
        <a href="{{ route('zonificacion') }}" class="metric-link">
        <div class="metric-header">
          <div class="metric-icon">
          <i class="fas fa-map-marked-alt"></i>
          </div>
        </div>
        <div class="metric-content">
          <div class="metric-title">Zonificación</div>
          <div class="metric-value">Distribución de Colmenas</div>
          <div class="metric-progress">
          <div class="progress-bar" style="width: 85%"></div>
          </div>
          <div class="metric-detail">
          Gestiona áreas geográficas
          </div>
        </div>
        </a>
      </div>

      <!-- 5. Sistema Experto Card -->
      <!-- <div class="metric-card sistema-experto">
    <a href="{{ route('sistemaexperto.index') }}" class="metric-link">
    <div class="metric-header">
    <div class="metric-icon">
    <i class="fas fa-brain"></i>
    </div>
    </div>
    <div class="metric-content">
    <div class="metric-title">Sistema Experto</div>
    <div class="metric-value">IA</div>
    <div class="metric-progress">
    <div class="progress-bar" style="width: 80%"></div>
    </div>
    <div class="metric-detail">
    Asistencia inteligente para decisiones
    </div>
    </div>
    </a>
    </div> -->

      <!-- 6. Dashboard Analytics Card -->
      <div class="metric-card dashboard-analytics">
        <a href="{{ route('dashboard') }}" class="metric-link">
        <div class="metric-header">
          <div class="metric-icon">
          <i class="fas fa-chart-line"></i>
          </div>
        </div>
        <div class="metric-content">
          <div class="metric-title">Análisis</div>
          <div class="metric-value">Indicadores</div>
          <div class="metric-progress">
          <div class="progress-bar" style="width: 70%"></div>
          </div>
          <div class="metric-detail">
          Estadísticas
          </div>
        </div>
        </a>
      </div>
      </div>
    </div>

    <!-- Secciones del Dashboard -->
    <!-- <div class="dashboard-sections">
    <div class="section-row">
    <!-- Actividad Reciente
    <div class="dashboard-section">
    <div class="section-header">
    <h2>Actividad Reciente</h2>
    <div class="section-actions">
    <div class="dashboard-dropdown">
    <button class="btn-filter">
    <i class="fas fa-filter"></i>
    <span>Filtrar</span>
    </button>
    <div class="dashboard-dropdown-menu">
    <a href="#" class="dashboard-dropdown-item active">Todas</a>
    <a href="#" class="dashboard-dropdown-item">Inspecciones</a>
    <a href="#" class="dashboard-dropdown-item">Tareas</a>
    <a href="#" class="dashboard-dropdown-item">Sistema</a>
    </div>
    </div>
    </div>
    </div>
    <div class="section-content">
    <div class="activity-list">
    @if(isset($actividades) && count($actividades) > 0)
    @foreach($actividades as $actividad)
    <div class="activity-item">
    <div class="activity-time-line">
    <div class="activity-time">{{ $actividad->fecha ?? '10:30 AM' }}</div>
    <div class="activity-line"></div>
    </div>
    <div class="activity-content">
    <div class="activity-icon {{ $actividad->tipo ?? 'inspeccion' }}">
    <i
    class="fas fa-{{ $actividad->tipo == 'tarea' ? 'tasks' : ($actividad->tipo == 'sistema' ? 'cog' : 'clipboard-check') }}"></i>
    </div>
    <div class="activity-details">
    <div class="activity-text">{{ $actividad->descripcion ?? 'Inspección completada en Apiario Norte' }}
    </div>
    <div class="activity-meta">
    <span class="activity-type">{{ ucfirst($actividad->tipo ?? 'Inspección') }}</span>
    <span class="activity-user">{{ $actividad->usuario ?? 'Juan Pérez' }}</span>
    </div>
    </div>
    </div>
    </div>
    @endforeach
    @else
    <div class="empty-state">
    <div class="empty-icon">
    <i class="fas fa-clipboard-list"></i>
    </div>
    <p>No hay actividades recientes</p>
    <button class="btn-action">Crear actividad</button>
    </div>
    @endif
    </div>
    </div>
    </div>

    Próximas Tareas
    <div class="dashboard-section">
    <div class="section-header">
    <h2>Próximas Tareas</h2>
    <div class="section-actions">
    <button class="btn-add">
    <i class="fas fa-plus"></i>
    <span>Nueva tarea</span>
    </button>
    <a href="{{ route('tareas') }}" class="btn-view-all">Ver todas</a>
    </div>
    </div>
    <div class="section-content">
    <div class="tasks-list">
    @if(isset($proximasTareas) && count($proximasTareas) > 0)
    @foreach($proximasTareas as $tarea)
    <div class="task-item priority-{{ $tarea->prioridad ?? 'media' }}">
    <div class="task-content">
    <div class="task-header">
    <div class="task-priority">
    <span class="priority-indicator"></span>
    <span class="priority-text">{{ ucfirst($tarea->prioridad ?? 'Media') }}</span>
    </div>
    <div class="task-date">
    <i class="far fa-calendar-alt"></i>
    <span>{{ $tarea->fecha_limite ?? 'Mañana' }}</span>
    </div>
    </div>
    <div class="task-body">
    <h3 class="task-title">{{ $tarea->titulo ?? 'Inspección de colmenas' }}</h3>
    <p class="task-description">
    {{ $tarea->descripcion ?? 'Revisar estado de las colmenas en el apiario principal' }}
    </p>
    </div>
    <div class="task-footer">
    <div class="task-status">
    <span
    class="status-badge {{ $tarea->estado ?? 'pendiente' }}">{{ ucfirst($tarea->estado ?? 'Pendiente') }}</span>
    </div>
    <div class="task-actions">
    <button class="task-action-btn">
    <i class="fas fa-check-circle"></i>
    </button>
    <button class="task-action-btn">
    <i class="fas fa-edit"></i>
    </button>
    </div>
    </div>
    </div>
    </div>
    @endforeach
    @else
    <div class="empty-state">
    <div class="empty-icon">
    <i class="fas fa-check-circle"></i>
    </div>
    <p>No hay tareas pendientes</p>
    <button class="btn-action">Crear tarea</button>
    </div>
    @endif
    </div>
    </div>
    </div>
    </div>
    </div> -->

    <!-- Sección de Clima y Condiciones -->
    <div class="weather-section">
      <div class="section-header">
      <h2 id="weather-title-js"></h2>
      <!--<div class="section-actions" style="display: flex; align-items: center;">
        <input type="date" id="weather-date-picker" style="margin-right:10px;">
      </div> -->
      </div>
      <div id="weather-cards-js" class="weather-cards">
      <div class="weather-card">
        <div class="weather-header">
        <div class="weather-day">Clima</div>
        </div>
        <div class="weather-icon">
        <i class="fas fa-question"></i>
        </div>
        <div class="weather-temp">---</div>
        <div class="weather-details">
        <div class="weather-detail">
          <span id="weather-msg-js">Permita la ubicación para ver el clima real.</span>
        </div>
        </div>
      </div>
      </div>
    </div>
    </div>
  </div>

  <script src="{{ asset('js/components/home-user/panel-de-control.js') }}"></script>
@endsection