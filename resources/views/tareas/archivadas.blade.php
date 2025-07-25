@extends('layouts.app')

@section('title', 'Tareas Archivadas - B-MaiA')

@section('content')
    <div class="container mt-4">
        <!-- Encabezado -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0"><i class="fa fa-archive me-2"></i>Tareas Archivadas</h2>
                <p class="text-muted">Restaura tus tareas archivadas cuando las necesites nuevamente.</p>
            </div>
            <a href="{{ route('tareas') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left me-1"></i> Volver a mis tareas
            </a>
        </div>

        <!-- Alertas -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        <!-- Lista de tareas archivadas -->
        @if ($tareasArchivadas->isEmpty())
            <div class="alert alert-warning text-center">
                <i class="fa fa-inbox fa-2x text-muted"></i>
                <p class="mt-2 mb-0">No tienes tareas archivadas actualmente.</p>
            </div>
        @else
            <ul class="list-group">
                @foreach ($tareasArchivadas as $tarea)
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <div class="me-auto">
                            <h5 class="mb-1">
                                <i class="fa fa-archive me-2 text-primary"></i>{{ $tarea->nombre }}
                            </h5>
                            <small class="text-muted">
                                <i class="fa fa-calendar-day me-1"></i>{{ \Carbon\Carbon::parse($tarea->fecha_inicio)->format('d/m/Y') }}
                                &nbsp;&rarr;&nbsp;
                                <i class="fa fa-calendar-check me-1"></i>{{ \Carbon\Carbon::parse($tarea->fecha_limite)->format('d/m/Y') }}
                            </small>
                            <div>
                                <span class="badge bg-{{ strtolower($tarea->prioridad ?? 'secondary') }} mt-2">
                                    Prioridad: {{ ucfirst($tarea->prioridad ?? 'Media') }}
                                </span>
                            </div>
                        </div>

                        <form action="{{ route('tareas.restaurar', $tarea->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-success btn-sm" type="submit">
                                <i class="fa fa-rotate-left me-1"></i> Restaurar
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
