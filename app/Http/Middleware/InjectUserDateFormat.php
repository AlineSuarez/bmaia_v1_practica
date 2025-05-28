<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class InjectUserDateFormat
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            config(['app.date_format' => Auth::user()->preference->date_format ?? 'DD/MM/YYYY']);
        }

        return $next($request);
    }
}