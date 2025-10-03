<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClimaController extends Controller
{
    public function actual(Request $r)
    {
        return response()->json(['temp'=>22.5,'humedad'=>55,'lluvia_mm'=>0]);
    }
    public function pronostico(Request $r)
    {
        $dias = (int)($r->query('dias',3));
        return response()->json(['dias'=>[],'n'=>$dias]);
    }
}
