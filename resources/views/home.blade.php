@extends('layouts.app')

@section('title', 'MaiA - Panel de Control Apícola')

<head>
  <link href="{{ asset('./css/components/home-user/dashboard.css') }}" rel="stylesheet">
</head>

@section('content')
  <div id="loader">
    <div class="loader-container">
    <div class="honeycomb-loader">
      <div class="honeycomb-glow"></div>
      <div class="honeycomb-grid">
      <div class="honeycomb-cell" style="--i:1"></div>
      <div class="honeycomb-cell" style="--i:2"></div>
      <div class="honeycomb-cell" style="--i:3"></div>
      <div class="honeycomb-cell" style="--i:4"></div>
      <div class="honeycomb-cell" style="--i:5"></div>
      <div class="honeycomb-cell" style="--i:6"></div>
      <div class="honeycomb-cell" style="--i:7"></div>
      <div class="honeycomb-cell" style="--i:8"></div>
      <div class="honeycomb-cell" style="--i:12"></div>
      <div class="honeycomb-cell" style="--i:13"></div>
      <div class="honeycomb-cell" style="--i:14"></div>
      <div class="honeycomb-cell" style="--i:15"></div>
      <div class="honeycomb-cell" style="--i:16"></div>
      </div>
    </div>
    <div class="loader-brand">MaiA</div>
    <div class="loader-text">Cargando panel de control</div>
    </div>
  </div>

  <!-- Dashboard Content -->
  <div class="dashboard-wrapper" style="display: none;" id="main-contenload">
    <!-- Dashboard Header con Estadísticas Rápidas -->
    <div class="dashboard-header">
    <div class="header-content">
      <h1>Panel de Control</h1>
      <p class="dashboard-subtitle">Gestión integral de apicultura</p>
    </div>
    <div class="quick-stats">
      <div class="quick-stat">
      <div class="stat-icon">
        <i class="fas fa-calendar-day"></i>
      </div>
      <div class="stat-info">
        <span class="stat-label">Hoy</span>
        <span class="stat-value">{{ date('d M, Y') }}</span>
      </div>
      </div>
      <div class="quick-stat">
      <div class="stat-icon">
        <i class="fas fa-thermometer-half"></i>
      </div>
      <div class="stat-info">
        <span class="stat-label">Temperatura</span>
        <span class="stat-value">24°C</span>
      </div>
      </div>
      <div class="quick-stat">
      <div class="stat-icon">
        <i class="fas fa-cloud-sun"></i>
      </div>
      <div class="stat-info">
        <span class="stat-label">Clima</span>
        <span class="stat-value">Soleado</span>
      </div>
      </div>
    </div>
    </div>

    <!-- Métricas Principales -->
    <div class="metrics-grid">
    <!-- Apiarios Card -->
    <div class="metric-card apiarios">
      <a href="{{ route('apiarios') }}" class="metric-link">
      <div class="metric-header">
        <div class="metric-icon">
        <i class="fas fa-archive"></i>
        </div>
        <div class="metric-trend up">
        <i class="fas fa-arrow-up"></i>
        <span>12%</span>
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

    <!-- Inspecciones Card -->
    <div class="metric-card inspecciones">
      <a href="{{ route('visitas') }}" class="metric-link">
      <div class="metric-header">
        <div class="metric-icon">
        <i class="fas fa-clipboard-check"></i>
        </div>
        <div class="metric-trend up">
        <i class="fas fa-arrow-up"></i>
        <span>8%</span>
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

    <!-- Tareas Card -->
    <div class="metric-card tareas">
      <a href="{{ route('tareas') }}" class="metric-link">
      <div class="metric-header">
        <div class="metric-icon">
        <i class="fas fa-tasks"></i>
        </div>
        <div class="metric-trend down">
        <i class="fas fa-arrow-down"></i>
        <span>5%</span>
        </div>
      </div>
      <div class="metric-content">
        <div class="metric-title">Tareas</div>
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

    <!-- Zonificación Card -->
    <div class="metric-card zonificacion">
      <a href="{{ route('zonificacion') }}" class="metric-link">
      <div class="metric-header">
        <div class="metric-icon">
        <i class="fas fa-map-marked-alt"></i>
        </div>
        <div class="metric-trend up">
        <i class="fas fa-arrow-up"></i>
        <span>15%</span>
        </div>
      </div>
      <div class="metric-content">
        <div class="metric-title">Zonificación</div>
        <div class="metric-value">Áreas</div>
        <div class="metric-progress">
        <div class="progress-bar" style="width: 85%"></div>
        </div>
        <div class="metric-detail">
        Gestiona áreas geográficas
        </div>
      </div>
      </a>
    </div>
    </div>

    <!-- Dashboard Sections -->
    <div class="dashboard-sections">
    <div class="section-row">
      <!-- Actividad Reciente -->
      <div class="dashboard-section">
      <div class="section-header">
        <h2>Actividad Reciente</h2>
        <div class="section-actions">
        <div class="dropdown">
          <button class="btn-filter">
          <i class="fas fa-filter"></i>
          <span>Filtrar</span>
          </button>
          <div class="dropdown-menu">
          <a href="#" class="dropdown-item active">Todas</a>
          <a href="#" class="dropdown-item">Inspecciones</a>
          <a href="#" class="dropdown-item">Tareas</a>
          <a href="#" class="dropdown-item">Sistema</a>
          </div>
        </div>
        <button class="btn-refresh">
          <i class="fas fa-sync-alt"></i>
        </button>
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

      <!-- Próximas Tareas -->
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
    </div>

    <!-- Sección de Clima y Condiciones -->
    <div class="weather-section">
    <div class="section-header">
      <h2>Condiciones Climáticas</h2>
      <div class="section-actions">
      <button class="btn-refresh">
        <i class="fas fa-sync-alt"></i>
      </button>
      </div>
    </div>
    <div class="weather-cards">
      <div class="weather-card today">
      <div class="weather-header">
        <div class="weather-day">Hoy</div>
        <div class="weather-date">{{ date('d M') }}</div>
      </div>
      <div class="weather-icon">
        <i class="fas fa-sun"></i>
      </div>
      <div class="weather-temp">24°C</div>
      <div class="weather-details">
        <div class="weather-detail">
        <i class="fas fa-wind"></i>
        <span>12 km/h</span>
        </div>
        <div class="weather-detail">
        <i class="fas fa-tint"></i>
        <span>20%</span>
        </div>
      </div>
      </div>
      <div class="weather-card">
      <div class="weather-header">
        <div class="weather-day">Mañana</div>
        <div class="weather-date">{{ date('d M', strtotime('+1 day')) }}</div>
      </div>
      <div class="weather-icon">
        <i class="fas fa-cloud-sun"></i>
      </div>
      <div class="weather-temp">22°C</div>
      </div>
      <div class="weather-card">
      <div class="weather-header">
        <div class="weather-day">{{ date('D', strtotime('+2 day')) }}</div>
        <div class="weather-date">{{ date('d M', strtotime('+2 day')) }}</div>
      </div>
      <div class="weather-icon">
        <i class="fas fa-cloud"></i>
      </div>
      <div class="weather-temp">20°C</div>
      </div>
      <div class="weather-card">
      <div class="weather-header">
        <div class="weather-day">{{ date('D', strtotime('+3 day')) }}</div>
        <div class="weather-date">{{ date('d M', strtotime('+3 day')) }}</div>
      </div>
      <div class="weather-icon">
        <i class="fas fa-cloud-sun-rain"></i>
      </div>
      <div class="weather-temp">19°C</div>
      </div>
      <div class="weather-card">
      <div class="weather-header">
        <div class="weather-day">{{ date('D', strtotime('+4 day')) }}</div>
        <div class="weather-date">{{ date('d M', strtotime('+4 day')) }}</div>
      </div>
      <div class="weather-icon">
        <i class="fas fa-sun"></i>
      </div>
      <div class="weather-temp">23°C</div>
      </div>
    </div>
    </div>
  </div>
@endsection

@section('optional-scripts')
  <script src="{{ asset('vendor/chatbot/js/chatbot.js') }}"></script>
  <script src="{{ asset('./js/components/home-user/home-resumen.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
      document.getElementById('loader').classList.add('fade-out');
      setTimeout(function () {
      document.getElementById('loader').style.display = 'none';
      document.getElementById('main-contenload').style.display = 'block';
      document.getElementById('main-contenload').classList.add('fade-in');
      }, 800);
    }, 1200);
    });
  </script>
@endsection