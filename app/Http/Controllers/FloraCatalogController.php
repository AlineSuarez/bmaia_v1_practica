<?php

namespace App\Http\Controllers;

use App\Models\FloraSpecies;
use Illuminate\Http\Request;

class FloraCatalogController extends Controller
{
    /**
     * Listado tipo tabla con filtros (mockup verde).
     */
    public function index(Request $request)
    {
        // Detectamos si vienen parámetros en la query (?q=...&nectar=...)
        $hasQueryParams = $request->hasAny(['q', 'nectar', 'forma', 'nivel', 'floracion']);

        // Leemos filtros (string vacío por defecto)
        $search    = $request->input('q', '');
        $nectar    = $request->input('nectar', '');
        $forma     = $request->input('forma', '');
        $nivel     = $request->input('nivel', '');
        $floracion = $request->input('floracion', '');

        $query = FloraSpecies::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('common_name', 'like', "%{$search}%")
                  ->orWhere('scientific_name', 'like', "%{$search}%");
            });
        }

        if ($nectar !== '') {
            $query->where('nectar_type', $nectar);
        }

        if ($forma !== '') {
            $query->where('growth_form', $forma);
        }

        if ($nivel !== '') {
            $query->where('attraction_level', $nivel);
        }

        if ($floracion !== '') {
            $query->where('flowering_season', $floracion);
        }

        if (!$hasQueryParams) {
            // Primera carga: mostramos todo el catálogo
            $speciesList = FloraSpecies::orderBy('common_name')->get();
        } else {
            // Carga con filtros / búsqueda
            $speciesList = $query->orderBy('common_name')->get();
        }

        return view('hoja_ruta.catalogo', [
            'speciesList' => $speciesList,
            'search'      => $search,
            'nectar'      => $nectar,
            'forma'       => $forma,
            'nivel'       => $nivel,
            'floracion'   => $floracion,
        ]);
    }

    /**
     * Perfil de una flor (vista tipo "Tevo (Retanilla trinervia)").
     */
    public function show(FloraSpecies $species)
    {
        return view('hoja_ruta.catalogo_detalle', [
            'species' => $species,
        ]);
    }
}
