<?php
namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class PlanService
{
    public static function priceFor(string $plan): int
    {
        $isAugust = now()->month === 8;
        return match ($plan) {
            'afc' => $isAugust ? (int) round(69900 * 0.7) : 69900,
            'me'  => $isAugust ? (int) round(87900 * 0.7) : 87900,
            'ge'  => $isAugust ? (int) round(150900 * 0.7) : 150900,
            default => 0,
        };
    }

    public static function activate(User $user, string $plan): void
    {
        $user->plan              = $plan;
        $user->fecha_vencimiento = Carbon::now()->addYear();
        $user->webpay_status     = 'paid';
        $user->save();
    }
}
