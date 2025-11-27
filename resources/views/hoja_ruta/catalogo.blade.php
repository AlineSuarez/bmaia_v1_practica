{{-- resources/views/hoja_ruta/catalogo.blade.php --}}
@extends('layouts.app')

@section('title','B-MaiA - Catálogo de Flora')

@section('content')
    @include('hoja_ruta.partials.subnav')



    @php
        // Si el controlador ya envió $speciesList con resultados (por ejemplo con filtros),
        // lo usamos tal cual. Si NO existe o viene vacío, cargamos todo el catálogo
        // directamente desde la base de datos.
        if (!isset($speciesList) || ($speciesList instanceof \Illuminate\Support\Collection && $speciesList->isEmpty())) {
            $speciesList = \App\Models\FloraSpecies::orderBy('common_name')->get();
        }
    @endphp
         {{-- Estilos compartidos (banner honeycomb, etc.) --}}
    <link rel="stylesheet"
          href="{{ asset('css/components/home-user/zonificacion.css') }}">

    {{-- ✅ Estilos específicos del Catálogo de Flora --}}
    <link rel="stylesheet"
          href="{{ asset('css/components/home-user/hoja-ruta-catalogo.css') }}">
    

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
