<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Models\Preference;

class InjectUserDateFormat
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $pref = Auth::user()->preference;
            if ($pref && $pref->date_format) {
                Config::set('app.date_format', strtoupper($pref->date_format));
            }
        }

        return $next($request);
    }
}