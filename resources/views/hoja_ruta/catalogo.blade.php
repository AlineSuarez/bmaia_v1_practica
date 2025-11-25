{{-- resources/views/hoja_ruta/catalogo.blade.php --}}
@extends('layouts.app')

@section('title','B-MaiA - Catálogo de Flora')

@section('content')
    @include('hoja_ruta.partials.subnav')

    {{-- Banner honeycomb / estilos compartidos (EL MISMO QUE EN CÁLCULO DE RUTA) --}}
    <link rel="stylesheet" href="{{ asset('css/components/home-user/zonificacion.css') }}">

    @php
        // Si el controlador ya envió $speciesList con resultados (por ejemplo con filtros),
        // lo usamos tal cual. Si NO existe o viene vacío, cargamos todo el catálogo
        // directamente desde la base de datos.
        if (!isset($speciesList) || ($speciesList instanceof \Illuminate\Support\Collection && $speciesList->isEmpty())) {
            $speciesList = \App\Models\FloraSpecies::orderBy('common_name')->get();
        }
    @endphp

    <style>
        .zonificacion-container {
            padding: 20px 24px 32px;
        }

        /* ==== Tarjeta de filtros y tabla (no tocamos el banner, viene desde zonificacion.css) ==== */

        .catalogo-filtros-card {
            margin-top: 22px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(15,23,42,0.08);
            padding: 14px 16px 18px;
            border: 1px solid #e5e7eb;
        }

        .catalogo-filtros-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .catalogo-search-wrapper {
            flex: 1 1 260px;
        }

        .catalogo-search-input {
            width: 100%;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
            font-size: 13px;
        }

        .catalogo-select {
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            padding: 7px 10px;
            font-size: 13px;
            min-width: 150px;
            background-color: #ffffff;
        }

        .btn-filtros {
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
            padding: 7px 12px;
            font-size: 13px;
            cursor: pointer;
            white-space: nowrap;
            text-decoration: none;
            color: #111827;
        }

        .btn-filtros:hover {
            background: #f3f4f6;
        }

        .btn-ver-listado {
            margin-left: auto;
            border-radius: 8px;
            border: none;
            background: #7ac943;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-ver-listado:hover {
            background: #5aa227;
        }

        .flora-table-wrapper {
            margin-top: 18px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(15,23,42,0.08);
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        table.flora-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .flora-table thead {
            background: #f3f4f6;
        }

        .flora-table th,
        .flora-table td {
            padding: 6px 10px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .flora-table th {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #6b7280;
        }

        .flora-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .flora-table tbody tr:hover {
            background: #ecfdf5;
        }

        .flora-table a {
            color: inherit;
            text-decoration: none;
        }

        .flora-table a:hover {
            text-decoration: underline;
        }

        @media (max-width: 900px) {
            .catalogo-filtros-row {
                align-items: stretch;
            }

            .btn-ver-listado {
                width: 100%;
                margin-left: 0;
                margin-top: 8px;
                text-align: center;
            }
        }
    </style>

    <div class="zonificacion-container">

        {{-- BANNER HONEYCOMB EXACTAMENTE IGUAL AL DE CÁLCULO DE RUTA --}}
        <div class="honeycomb-header">
            <div class="honeycomb-overlay"></div>
            <div class="header-content">
                <h1 class="zonificacion-title">Catálogo de Flora</h1>
                <p class="zonificacion-subtitle">
                    Explora las especies del territorio, revisa su importancia apícola
                    y conéctalas con tus apiarios y la planificación de la Hoja de Ruta.
                </p>
            </div>
        </div>

        {{-- TARJETA DE FILTROS --}}
        <form class="catalogo-filtros-card" method="GET" action="{{ route('flora.catalogo.index') }}">
            <div class="catalogo-filtros-row">

                {{-- Búsqueda por nombre --}}
                <div class="catalogo-search-wrapper">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        class="catalogo-search-input"
                        placeholder="Buscar por Quillay, Eucalipto, etc..."
                    >
                </div>

                {{-- Filtro: tipo de recurso floral --}}
                <select name="nectar" class="catalogo-select">
                    <option value="">Néctar / Polen...</option>
                    <option value="Néctar"  {{ request('nectar') === 'Néctar' ? 'selected' : '' }}>Néctar</option>
                    <option value="Polen"   {{ request('nectar') === 'Polen' ? 'selected' : '' }}>Polen</option>
                    <option value="Ambos"   {{ request('nectar') === 'Ambos' ? 'selected' : '' }}>Ambos</option>
                </select>

                {{-- Filtro: forma --}}
                <select name="forma" class="catalogo-select">
                    <option value="">Forma (Árbol/Arbusto)</option>
                    <option value="Árbol"      {{ request('forma') === 'Árbol' ? 'selected' : '' }}>Árbol</option>
                    <option value="Arbusto"    {{ request('forma') === 'Arbusto' ? 'selected' : '' }}>Arbusto</option>
                    <option value="Herbácea"   {{ request('forma') === 'Herbácea' ? 'selected' : '' }}>Herbácea</option>
                    <option value="Trepadora"  {{ request('forma') === 'Trepadora' ? 'selected' : '' }}>Trepadora</option>
                </select>

                {{-- Filtro: nivel de atracción --}}
                <select name="nivel" class="catalogo-select">
                    <option value="">Nivel de atracción</option>
                    <option value="Alto"  {{ request('nivel') === 'Alto' ? 'selected' : '' }}>Alto</option>
                    <option value="Medio" {{ request('nivel') === 'Medio' ? 'selected' : '' }}>Medio</option>
                    <option value="Bajo"  {{ request('nivel') === 'Bajo' ? 'selected' : '' }}>Bajo</option>
                </select>

                {{-- Filtro: época de floración --}}
                <select name="floracion" class="catalogo-select">
                    <option value="">Época de floración</option>
                    <option value="Primavera"            {{ request('floracion') === 'Primavera' ? 'selected' : '' }}>Primavera</option>
                    <option value="Verano"               {{ request('floracion') === 'Verano' ? 'selected' : '' }}>Verano</option>
                    <option value="Otoño"                {{ request('floracion') === 'Otoño' ? 'selected' : '' }}>Otoño</option>
                    <option value="Invierno"             {{ request('floracion') === 'Invierno' ? 'selected' : '' }}>Invierno</option>
                    <option value="Primavera-Verano"     {{ request('floracion') === 'Primavera-Verano' ? 'selected' : '' }}>Primavera-Verano</option>
                </select>

                {{-- Botón limpiar filtros --}}
                <a href="{{ route('flora.catalogo.index') }}" class="btn-filtros">
                    Limpiar filtros
                </a>

                {{-- Botón "Ver listado completo" --}}
                <button type="submit" class="btn-ver-listado">
                    Ver listado completo
                </button>
            </div>
        </form>

        {{-- TABLA DE ESPECIES (SIN IMAGEN) --}}
        <div class="flora-table-wrapper">
            <table class="flora-table">
                <thead>
                <tr>
                    <th>Nº</th>
                    <th>Nombre Común</th>
                    <th>Nombre Científico</th>
                    <th>Familia</th>
                    <th>Origen</th>
                    <th>Hábito de Crecimiento</th>
                </tr>
                </thead>
                <tbody>
                @forelse($speciesList as $index => $flora)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <a href="{{ route('flora.catalogo.show', $flora->id) }}"
                               style="color:#111827; text-decoration:none;">
                                {{ $flora->common_name }}
                            </a>
                        </td>
                        <td><em>{{ $flora->scientific_name }}</em></td>
                        <td>{{ $flora->family }}</td>
                        <td>{{ $flora->origin }}</td>
                        <td>{{ $flora->growth_habit }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding:14px 0;">
                            No hay especies registradas todavía en el catálogo.
                            Agrega algunas filas en la tabla
                            <span style="font-family:monospace; color:#ef4444;">flora_species</span>.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection
