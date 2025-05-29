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
            'default_view' => 'required|in:dashboard,apiaries,calendar,reports,home,cuaderno,tareas,zonificacion,sistemaexperto',
            'voice_match'     => 'nullable|boolean',
            'calendar_email'  => 'nullable|boolean',
            'calendar_push'   => 'nullable|boolean',
            'reminder_time'   => 'required|in:15,30,60,120,1440',
        ]);

        try {
            $data['date_format'] = strtoupper($data['date_format']);
            $data['user_id'] = Auth::id();
            $prefs = Preference::updateOrCreate(
                ['user_id' => Auth::id()],
                $data
            );
            return response()->json([
                'message'     => 'Preferencias guardadas correctamente',
                'preferences' => $prefs
            ], 200);
        } catch (Exception $e) {
            throw $e;
        }
    }

    // GET /user/settings/preferences/date-format-demo
    public function dateFormatDemo()
    {
        $user    = Auth::user();
        // Si no hay prefs, tomamos el config actual
        $prefs   = $user->preference
                 ?? new Preference(['date_format' => config('app.date_format')]);
        $formats = ['DD/MM/YYYY', 'MM/DD/YYYY', 'YYYY-MM-DD', 'DD-MM-YYYY'];

        return view('user.partials.date_format_demo', [
            'currentFormat' => strtoupper($prefs->date_format),
            'formats'       => $formats,
        ]);
    }

    public function updateDateFormat(Request $request)
    {
        $data = $request->validate([
            'date_format' => 'required|string|in:DD/MM/YYYY,MM/DD/YYYY,YYYY-MM-DD,DD-MM-YYYY',
        ]);

        $prefs = Preference::firstOrCreate(
            ['user_id' => Auth::id()],
            ['date_format' => strtoupper($data['date_format'])]
        );

        // Si ya existía, actualizamos su valor
        if (! $prefs->wasRecentlyCreated) {
            $prefs->date_format = strtoupper($data['date_format']);
            $prefs->save();
        }

        return redirect()
               ->route('preferences.demo')
               ->with('success', 'Formato de fecha actualizado a ' . strtoupper($data['date_format']));
    }

    // POST /user/settings/preferences/reset
    public function reset()
    {
        try {
            Preference::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'language'         => 'es_CL',
                    'date_format'      => 'DD/MM/YYYY',
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