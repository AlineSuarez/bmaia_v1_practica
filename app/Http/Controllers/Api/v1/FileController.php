<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index(Request $r)   { return response()->json(['data'=>[]]); }
    public function store(Request $r)
    {
        $r->validate(['file'=>'required|file|max:20480']);
        $path = $r->file('file')->store('uploads');
        return response()->json(['id'=>basename($path),'name'=>$r->file('file')->getClientOriginalName(),'status'=>'uploaded'],201);
    }
    public function destroy(Request $r, string $id)
    {
        // TODO: delete físico + BD
        return response()->json(['status'=>'deleted']);
    }
}
