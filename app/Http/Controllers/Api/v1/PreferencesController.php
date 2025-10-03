<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PreferencesController extends Controller
{
    public function show(Request $request)
    {
        return response()->json($request->user()->preferences ?? []);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'locale' => 'nullable|string|max:10',
            'units'  => 'nullable|string|in:metric,imperial',
        ]);
        $u = $request->user();
        $u->preferences = array_merge((array)($u->preferences ?? []), $data);
        $u->save();
        return response()->json($u->preferences);
    }
}
