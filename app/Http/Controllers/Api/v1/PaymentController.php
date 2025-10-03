<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function paymentResponse(Request $request)
    {
        // según lógica...
        if ($request->query('status') === 'ok') {
            return redirect()->route('payment.success'); // RedirectResponse
        }
        // o
        return response()->json(['status' => 'failed'], 400); // JsonResponse
    }
}
