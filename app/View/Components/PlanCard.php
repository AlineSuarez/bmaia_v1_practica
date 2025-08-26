<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PlanCard extends Component
{
    public $plan;   // afc, me, ge
    public $type;   // content o settings

    public function __construct($plan, $type = 'content')
    {
        $this->plan = $plan;
        $this->type = $type;
    }

    public function render(): View|Closure|string
    {
        $iva = 0.19;
        $isAugust = now()->month == 8;

        $base = [
            'afc' => 69900,
            'me'  => 87900,
            'ge'  => 150900,
        ];

        $limit = [
            'afc' => 299,
            'me'  => 799,
            'ge'  => null,
        ];

        $price = $isAugust ? intval(round($base[$this->plan] * 0.7)) : $base[$this->plan];
        $withIva = intval(round($price * (1 + $iva)));
        $monthlyWithIva = intval(round($withIva / 12));
        $perColmena = $limit[$this->plan] ? intval(round($price / $limit[$this->plan])) : null;

        return view('components.plan-card', [
            'isAugust' => $isAugust,
            'basePrice' => $base[$this->plan],
            'price' => $price,
            'withIva' => $withIva,
            'monthlyWithIva' => $monthlyWithIva,
            'perColmena' => $perColmena,
        ]);
    }
}
