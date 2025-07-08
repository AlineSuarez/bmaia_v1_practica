@extends('layouts.app')

@section('title', 'Colmenas Históricas de ' . $apiario->nombre)

@section('content')
    <head>
        <link href="{{ asset('css/components/home-user/colmenas.css') }}" rel="stylesheet">
    </head>

    <div class="main-layout">
      <div class="container">
        <div class="page-header">
          <div class="header-row">
            <h1 class="page-title">Colmenas del Apiario</h1>
            <div class="back-button-container">
              <a href="{{ route('apiarios') }}" class="back-button">
                <i class="fas fa-arrow-left"></i>
                <span>Volver a Apiarios</span>
              </a>
            </div>
          </div>
          <div class="apiario-info">{{ $apiario->nombre }} (Archivado)</div>
          <div class="apiario-stats">
            <div class="stat-item">
              <div class="stat-icon"><i class="fas fa-cube"></i></div>
              <span>Total: {{ $colmenasPorOrigen->flatten()->count() }} colmenas</span>
            </div>
          </div>
        </div>

        {{-- Recorro cada grupo de origen --}}
        @foreach($colmenasPorOrigen as $origen => $colmenas)
          <div class="section-group">
            <h2 class="section-title">
              <i class="fas fa-home mr-2"></i>{{ $origen }}
            </h2>

            <div class="colmenas-container">
              <div class="colmenas-info">
                <div class="colmenas-count">
                  <strong>{{ $colmenas->count() }}</strong> colmenas en este grupo
                </div>
              </div>

              <div class="colmenas-grid">
                @forelse($colmenas as $colmena)
                  <div class="colmena-card archived"
                       style="border-color: {{ $colmena->color_etiqueta }};">
                    <div class="colmena-icon"><i class="fas fa-cube"></i></div>
                    <div class="colmena-number">#{{ $colmena->numero }}</div>
                  </div>
                @empty
                  <div class="empty-state">
                    <div class="empty-state-icon">
                      <i class="fas fa-cube"></i>
                    </div>
                    <div>No hay colmenas en este grupo</div>
                  </div>
                @endforelse
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <style>
      /* Las colmenas históricas no son clicables y aparecen atenuadas */
      .colmena-card.archived {
        cursor: default;
        opacity: 0.6;
      }
    </style>
@endsection
