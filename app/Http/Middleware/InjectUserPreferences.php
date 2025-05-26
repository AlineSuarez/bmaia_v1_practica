<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class InjectUserPreferences
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $prefs = Auth::user()->preference;

            if ($prefs) {
                // Inyectar en config global
                config([
                    'preferences.language'         => $prefs->language ?? 'es_CL',
                    'preferences.date_format'      => strtoupper($prefs->date_format ?? 'DD/MM/YYYY'),
                    'preferences.theme'            => $prefs->theme ?? 'light',
                    'preferences.voice_preference' => $prefs->voice_preference ?? 'female_1',
                    'preferences.default_view'     => $prefs->default_view ?? 'dashboard',
                    'preferences.voice_match'      => (bool) $prefs->voice_match,
                    'preferences.calendar_email'   => (bool) $prefs->calendar_email,
                    'preferences.calendar_push'    => (bool) $prefs->calendar_push,
                    'preferences.reminder_time'    => $prefs->reminder_time ?? 15,
                ]);
            }
        }

        return $next($request);
    }
}
