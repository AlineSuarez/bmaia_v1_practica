
@extends('layouts.app')

@section('title', 'Tareas Archivadas - B-MaiA')

@section('content')
    <head>
        <link rel="stylesheet" href="{{ asset('./css/components/home-user/tasks/archives.css') }}">
    </head>

    <div class="archivadas-container">
        <div class="archivadas-wrapper">
            <!-- Encabezado principal -->
            <header class="archivadas-header">
                <div class="header-content">
                    <div class="header-left">
                        <h1 class="header-title">
                            <i class="fa fa-archive"></i>
                            <span>Tareas Archivadas</span>
                        </h1>
                        <p class="header-subtitle">Restaura tus tareas archivadas cuando las necesites nuevamente</p>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('tareas') }}" class="btn-volver" title="Volver a mis tareas">
                            <i class="fa fa-arrow-left"></i>
                            <span>Volver a mis tareas</span>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Alertas -->
            @if(session('success'))
                <div class="alert-success">
                    <div class="alert-content">
                        <i class="fa fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                        <button type="button" class="alert-close" onclick="this.parentElement.parentElement.style.display='none'">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Contenido principal -->
            <main class="archivadas-content">
                @if ($tareasArchivadas->isEmpty())
                    <!-- Estado vacío -->
                    <div class="empty-state">
                        <div class="empty-state-content">
                            <div class="empty-icon">
                                <i class="fa fa-inbox"></i>
                            </div>
                            <h3 class="empty-title">No hay tareas archivadas</h3>
                            <p class="empty-message">Las tareas archivadas aparecerán aquí cuando las archives desde tu lista principal</p>
                        </div>
                    </div>
                @else
                    <!-- Lista de tareas archivadas -->
                    <div class="tareas-archivadas-container">
                        <div class="tareas-grid">
                            @foreach ($tareasArchivadas as $tarea)
                                <div class="tarea-card" data-tarea-id="{{ $tarea->id }}">
                                    <div class="card-header">
                                        <div class="card-icon">
                                            <i class="fa fa-archive"></i>
                                        </div>
                                        <h4 class="card-title">{{ $tarea->nombre }}</h4>
                                    </div>

                                    <div class="card-body">
                                        <div class="card-dates">
                                            <div class="date-item">
                                                <i class="fa fa-calendar-day"></i>
                                                <span class="date-label">Inicio:</span>
                                                <span class="date-value">{{ \Carbon\Carbon::parse($tarea->fecha_inicio)->format('d/m/Y') }}</span>
                                            </div>
                                            <div class="date-item">
                                                <i class="fa fa-calendar-check"></i>
                                                <span class="date-label">Límite:</span>
                                                <span class="date-value">{{ \Carbon\Carbon::parse($tarea->fecha_limite)->format('d/m/Y') }}</span>
                                            </div>
                                        </div>

                                        <div class="card-meta">
                                            <div class="priority-badge priority-{{ strtolower($tarea->prioridad ?? 'media') }}">
                                                <i class="fa fa-flag"></i>
                                                <span>{{ ucfirst($tarea->prioridad ?? 'Media') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-actions">
                                        <form action="{{ route('tareas.restaurar', $tarea->id) }}" method="POST" class="restore-form">
                                            @csrf
                                            <button class="btn-restaurar" type="submit" title="Restaurar tarea">
                                                <i class="fa fa-rotate-left"></i>
                                                <span>Restaurar</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </main>
        </div>
    </div>
@endsection