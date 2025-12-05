@extends('layouts.app')

@section('title', 'B-MaiA - Cotizador de Productos')

@section('content')
<head>
    <link rel="stylesheet" href="{{ asset('css/components/home-user/inventario/inventario.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/home-user/inventario/cotizador.css') }}">
    
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    </script>
</head>

<div class="cotizador-header">
    <button class="btn-inventory">
        <a href='{{ route("inventario") }}' style="text-decoration: none; color: white;">
            Volver al Inventario
        </a>
    </button>

    <div class="cotizador-buttons-right">
        <button 
            class="btn"
            id="btn-pedidos"
            onclick="mostrarSeccion('pedidos')"
        >
            <i class="fas fa-shopping-cart"></i>
            Mis Pedidos
        </button>
        <button
            class="btn"
            id="btn-zona"
            onclick="mostrarSeccion('mapa')"
        >
            Buscar Proveedores por zona
        </button>
        <button
            class="btn"
            id="btn-productos"
            onclick="mostrarSeccion('busqueda')"
            disabled
        >
            Buscar Productos de Proveedores
        </button>
    </div>
</div>

<!-- SECCIÓN: MIS PEDIDOS -->
<div id="inventario-pedidos" style="display:none;">
    @include('Inventario.pedidos')
</div>

<!--SECCIÓN: MAPA -->
<div id="inventario-map" style="display:none;">
    @include('Inventario.map')
</div>

<!-- SECCIÓN: BÚSQUEDA DE PRODUCTOS -->
<div id="inventario-search" style="display:block;">
    @include('Inventario.search')
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    function mostrarSeccion(seccion) {
        // Ocultar todas las secciones
        document.getElementById('inventario-pedidos').style.display = 'none';
        document.getElementById('inventario-map').style.display = 'none';
        document.getElementById('inventario-search').style.display = 'none';

        // Habilitar todos los botones
        document.getElementById('btn-pedidos').disabled = false;
        document.getElementById('btn-zona').disabled = false;
        document.getElementById('btn-productos').disabled = false;

        // Mostrar la sección seleccionada y deshabilitar su botón
        if (seccion === 'pedidos') {
            document.getElementById('inventario-pedidos').style.display = 'block';
            document.getElementById('btn-pedidos').disabled = true;
        } else if (seccion === 'mapa') {
            document.getElementById('inventario-map').style.display = 'block';
            document.getElementById('btn-zona').disabled = true;
        } else if (seccion === 'busqueda') {
            document.getElementById('inventario-search').style.display = 'block';
            document.getElementById('btn-productos').disabled = true;
        }
    }
</script>

<script src="{{ asset('js/components/home-user/pedidos.js') }}"></script>

@endsection
