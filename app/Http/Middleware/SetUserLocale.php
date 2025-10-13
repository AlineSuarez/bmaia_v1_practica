<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class SetUserLocale
{
    public function handle($request, Closure $next)
    {
        $lang = Auth::user()?->preference?->language ?: 'es'; // default
        App::setLocale($lang);
        // Opcional: reflejarlo en config para librerías que lean config()
        config()->set('app.locale', $lang);

        return $next($request);
    }
}