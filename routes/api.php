<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RouteTimeController;  //  sin Api\

Route::get('route-time', [RouteTimeController::class, 'calc']);
