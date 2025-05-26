<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class SetUserLocale
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->preference && Auth::user()->preference->language) {
            App::setLocale(Auth::user()->preference->language);
        }

        return $next($request);
    }
}