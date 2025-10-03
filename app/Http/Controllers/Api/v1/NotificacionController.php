<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function email(Request $r)
    {
        $r->validate(['to'=>'required|email','subject'=>'required','body'=>'required']);
        return response()->json(['status'=>'queued']);
    }

    public function whatsapp(Request $r)
    {
        $r->validate(['to'=>'required','message'=>'required']);
        return response()->json(['status'=>'queued']);
    }

    public function push(Request $r)
    {
        $r->validate(['device_id'=>'required','title'=>'required','body'=>'required']);
        return response()->json(['status'=>'queued']);
    }
}
