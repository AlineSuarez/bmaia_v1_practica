<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function apiariosCount(Request $r)
    {
        $n = DB::table('apiarios')->where('user_id', $r->user()->id)->count();
        return response()->json(['count' => $n]);
    }
    public function colmenasCount(Request $r)
    {
        $q = DB::table('colmenas')->where('user_id', $r->user()->id);
        if ($e = $r->query('estado')) $q->where('estado', $e);
        return response()->json(['count' => $q->count()]);
    }
    public function colmenasHealth(Request $r)
    {
        $u = $r->user()->id;
        return response()->json([
            'sanas'     => DB::table('colmenas')->where('user_id',$u)->where('estado','sana')->count(),
            'enfermas'  => DB::table('colmenas')->where('user_id',$u)->where('estado','enferma')->count(),
            'inactivas' => DB::table('colmenas')->where('user_id',$u)->where('estado','inactiva')->count(),
        ]);
    }
    public function visitasSemana(Request $r)
    {
        $u = $r->user()->id;
        $desde = now()->startOfWeek()->toDateString();
        $hasta = now()->endOfWeek()->toDateString();
        $n = DB::table('visitas')->where('user_id',$u)->whereBetween('fecha',[$desde,$hasta])->count();
        return response()->json(['total'=>$n,'desde'=>$desde,'hasta'=>$hasta]);
    }
}
