<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Preference;
use Exception;

class PreferencesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // GET /user/settings/preferences
    public function index()
    {
        try {
            $prefs = Preference::firstOrCreate(
                ['user_id' => Auth::id()],
                [] // usa defaults de migración
            );
            return response()->json($prefs);
        } catch (Exception $e) {
            return response()->json(
                ['error' => 'Index Exception: '.$e->getMessage()],
                500
            );
        }
    }

    // POST /user/settings/preferences
    public function update(Request $request)
    {
        $data = $request->validate([
            'language'        => 'required|string',
            'date_format'     => 'required|string',
            'theme'           => 'required|in:light,dark,auto',
            'voice_preference'=> 'required|in:female_1,female_2,male_1,male_2',
            'default_view'    => 'required|in:dashboard,apiaries,calendar,reports',
            'voice_match'     => 'boolean',
            'calendar_email'  => 'boolean',
            'calendar_push'   => 'boolean',
            'reminder_time'   => 'required|integer',
        ]);

        try {
            $prefs = Preference::updateOrCreate(
                ['user_id' => Auth::id()],
                $data
            );
            return response()->json([
                'message'     => 'Preferencias guardadas correctamente',
                'preferences' => $prefs
            ], 200);
        } catch (Exception $e) {
            return response()->json(
                ['error' => 'Update Exception: '.$e->getMessage()],
                500
            );
        }
    }

    // POST /user/settings/preferences/reset
    public function reset()
    {
        try {
            $prefs = Preference::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'language'         => 'es_CL',
                    'date_format'      => 'dd/mm/yyyy',
                    'theme'            => 'light',
                    'voice_preference' => 'female_1',
                    'default_view'     => 'dashboard',
                    'voice_match'      => false,
                    'calendar_email'   => false,
                    'calendar_push'    => false,
                    'reminder_time'    => 15,
                ]
            );
            return response()->json([
                'message'     => 'Preferencias restablecidas a valores predeterminados',
                'preferences' => $prefs
            ], 200);
        } catch (Exception $e) {
            return response()->json(
                ['error' => 'Reset Exception: '.$e->getMessage()],
                500
            );
        }
    }
}