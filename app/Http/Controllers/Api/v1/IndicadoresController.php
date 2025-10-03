<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndicadoresController extends Controller
{
    public function produccion(Request $r)
    {
        return response()->json(['kg_totales'=>0,'por_apiario'=>[],'tendencia_6m'=>[]]);
    }
    public function comparativo(Request $r)
    {
        return response()->json(['series'=>[]]);
    }
}
