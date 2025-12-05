@extends('layouts.app')

@section('title', 'B-MaiA - Plan de Trabajo Anual')

@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/components/home-user/inventario/inventario.css') }}">
    </head>

    <!-- Navegador de Inventario -->
    <!-- contenedor lado izquierdo -->
    <div
        class="inventory-container" 
    >
        <div class="inventory-controls">
        <div class="control-group">
            <button id="btn-openModalCreateProduct" class="btn-inventory">
                <i class="fas fa-plus"></i>
                Agregar Producto
            </button>
        </div>
        
        <div class="control-group">
            <button class="btn-inventory">
                <a href="{{ url('/cotizador') }}" class="text-white">
                    Cotizador
                </a>    
            </button>
            <button id="modalFiltrar" class="btn-inventory">
                <i class="fas fa-filter"></i>
                Buscar / Filtrar
            </button>
        </div>
    </div>
    <!-- contenedor de inventario -->
    <div style=" margin-top: 20px;">
        <!-- Listado de Inventario -->
        <div 
            id="inventario-listado" 
        >
            @include('Inventario.list')
        </div>
        
        <!-- Filtrar -->
        <div
            id="inventario-filtros"
            style="
                display:none; 
                border: teal; 
                border-style: solid; 
                padding: 10px; 
                margin-top: 20px;
                position: fixed;
                top: 25%;
                left: 37%;
                background-color: cyan;
            "
        >
            @include('Inventario.filter')
        </div>
    </div>
    <!-- modal para crear un nuevo item -->
    <div id="modalCreateProduct" 
        style="
            display:none; 
            border: teal; 
            border-style: solid; 
            padding: 10px; 
            margin-top: 20px;
            position: fixed;
            top: 25%;
            left: 37%;
            background-color: cyan;
            "
    >
        @include('Inventario.create')
    </div>
    <script src="{{ asset('js/components/home-user/inventario.js') }}"></script>
@endsection