@extends('layouts.app')

@section('title', 'B-MaiA - Panel de Control Apícola')

@section('content')

  <head>
    <link href="{{ asset('./css/components/home-user/dashboard.css') }}" rel="stylesheet">
  </head>

  <!-- Loader con animación de panal -->
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
        <span class="stat-value">{{ date('d M, Y') }}</span>
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
          <div class="metric-value">Localización de apiarios</div>
          <div class="metric-progress">
          <div class="progress-bar" style="width: 85%"></div>
          </div>
          <div class="metric-detail">
          Gestiona áreas geográficas
          </div>
        </div>
        </a>
      </div>

      <!-- 5. Dashboard Analytics Card -->
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

    <!-- Sección de Clima y Condiciones -->
    <div class="weather-section">
      <div class="section-header">
      <h2 id="weather-title-js"></h2>
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