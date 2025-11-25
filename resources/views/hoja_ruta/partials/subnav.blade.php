{{-- resources/views/hoja_ruta/partials/subnav.blade.php --}}
@php
  // Definición centralizada de items (ruta, ícono e título)
  $hrItems = [
    ['route' => 'hoja.explorador',     'icon' => 'fa-compass',     'label' => 'Explorador de Zonas'],
    ['route' => 'hoja.calculo',        'icon' => 'fa-route',       'label' => 'Cálculo de Ruta'],
    // Usa el catálogo con datos enriquecidos (controlador) si existe, si no cae al catálogo simple:
    ['route' => Route::has('hojaruta.catalogo') ? 'hojaruta.catalogo' : 'hoja.catalogo',
                                     'icon' => 'fa-seedling',     'label' => 'Catálogo de Flora'],
    ['route' => 'hoja.monitoreo',      'icon' => 'fa-chart-area',  'label' => 'Monitoreo Histórico'],
    ['route' => 'hoja.capacidad',      'icon' => 'fa-box',         'label' => 'Capacidad de Carga'],
  ];
@endphp

@once
  <style>
    /* Barra contenedora */
    .hr-subnav-wrap{
      margin: 18px 0 22px 0;
      padding: 0 6px;
    }

    /* Carril horizontal con scroll suave en pantallas chicas */
    .hr-subnav{
      display:flex;
      gap:12px;
      overflow:auto;
      scrollbar-width: thin;
      align-items:center;
      padding: 8px 4px;
    }
    .hr-subnav::-webkit-scrollbar{height:8px}
    .hr-subnav::-webkit-scrollbar-thumb{background:#e5e7eb;border-radius:8px}

    /* Píldoras */
    .hr-pill{
      --hr-b: #e5e7eb;
      --hr-bg:#ffffff;
      --hr-t:#1f2937;
      display:flex; align-items:center; gap:10px;
      background: var(--hr-bg);
      color: var(--hr-t);
      border:1px solid var(--hr-b);
      padding:12px 16px;
      border-radius: 12px;
      text-decoration:none;
      white-space:nowrap;
      box-shadow: 0 1px 0 rgba(0,0,0,.02);
      transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
    }
    .hr-pill:hover{
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(0,0,0,.07);
      border-color:#d1d5db;
    }

    /* Estado activo (similar al acento ámbar que usas) */
    .hr-pill.is-active{
      border-color:#f59e0b;
      box-shadow: 0 6px 18px rgba(245,158,11,.18);
      background:#fffaf0;
    }
    .hr-pill.is-active .hr-badge{
      background:#ffedd5; color:#9a3412;
    }

    /* Icono y título */
    .hr-icon{font-size:18px; line-height: 1}
    .hr-title{font-weight:600; font-size:14px}

    /* “Planifica tu Hoja de Ruta” como chip */
    .hr-chip{
      background:#ff8f00; color:#fff; font-weight:700;
      border-radius:8px; padding:10px 14px; margin-right:8px;
      white-space:nowrap;
    }

    /* Badge opcional a la derecha (por si en el futuro usas contadores) */
    .hr-badge{
      margin-left:6px; font-size:12px; font-weight:700;
      background:#f3f4f6; color:#374151; padding:2px 7px; border-radius:999px;
    }

    /* Compacto en móviles */
    @media (max-width: 768px){
      .hr-title{font-size:13px}
      .hr-pill{padding:10px 12px}
    }
  </style>
@endonce

<div class="hr-subnav-wrap">
  <div class="hr-subnav">
    <div class="hr-chip">Planifica tu Hoja de Ruta</div>

    @foreach ($hrItems as $item)
      @php
        $isActive = request()->routeIs($item['route']);
        $href = route($item['route']);
      @endphp

      <a href="{{ $href }}"
         class="hr-pill {{ $isActive ? 'is-active' : '' }}">
        <i class="fa-solid {{ $item['icon'] }} hr-icon"></i>
        <span class="hr-title">{{ $item['label'] }}</span>
        {{-- <span class="hr-badge">12</span>  <!-- si alguna vez quieres contar algo --> --}}
      </a>
    @endforeach
  </div>
</div>
