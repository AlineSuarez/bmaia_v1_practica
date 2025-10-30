
@extends('layouts.app')

@section('title', 'Tareas Descartadas - B-MaiA')

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
                            <span>Tareas Descartadas</span>
                        </h1>
                        <p class="header-subtitle">Restaura tus tareas descartadas cuando las necesites nuevamente</p>
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
                        <div class="tareas-lista">
                            @foreach ($tareasArchivadas as $tarea)
                                <div class="tarea-item" data-tarea-id="{{ $tarea->id }}">
                                    <!-- Icono -->
                                    <div class="item-icon">
                                        <i class="fa fa-archive"></i>
                                    </div>

                                    <!-- Información de la tarea -->
                                    <div class="item-info">
                                        <h4 class="item-title">{{ $tarea->nombre }}</h4>
                                        
                                        <div class="item-details">
                                            <span class="detail-item">
                                                <i class="fa fa-calendar-day"></i>
                                                Inicio: {{ \Carbon\Carbon::parse($tarea->fecha_inicio)->format('d/m/Y') }}
                                            </span>
                                            <span class="detail-separator">•</span>
                                            <span class="detail-item">
                                                <i class="fa fa-calendar-check"></i>
                                                Límite: {{ \Carbon\Carbon::parse($tarea->fecha_limite)->format('d/m/Y') }}
                                            </span>
                                            <span class="detail-separator">•</span>
                                            <div class="priority-badge priority-{{ strtolower($tarea->prioridad ?? 'media') }}">
                                                <i class="fa fa-circle"></i>
                                                <span>{{ ucfirst($tarea->prioridad ?? 'Media') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Acciones -->
                                    <div class="item-actions">
                                        <form action="{{ route('tareas.restaurar', $tarea->id) }}" method="POST" class="restore-form">
                                            @csrf
                                            <button class="btn-restaurar" type="submit" title="Restaurar tarea">
                                                <i class="fa fa-rotate-left"></i>
                                                <span>Restaurar</span>
                                            </button>
                                        </form>

                                        <form action="{{ route('tareas.destroy', $tarea->id) }}" method="POST" class="delete-form" onsubmit="return confirm('¿Estás seguro de eliminar esta tarea permanentemente?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-eliminar" type="submit" title="Eliminar tarea">
                                                <i class="fa fa-trash"></i>
                                                <span>Eliminar</span>
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