<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\FreeTrialExpiredMail;
use Carbon\Carbon;

class CheckFreeTrialExpirations extends Command
{
    protected $signature = 'trial:check-expirations';
    protected $description = 'Verifica los usuarios con prueba gratuita vencida y envía correo';

    public function handle()
    {
        $today = Carbon::now()->startOfDay();

        $users = User::whereDate('fecha_vencimiento', $today)
                     ->where('plan', 'drone')
                     ->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new FreeTrialExpiredMail($user));
            $this->info("Correo enviado a {$user->email}");
        }
    }
}
