<?php
namespace App\Mail\Plans;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlanActivatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $plan) {}

    public function build()
    {
        return $this->subject('🎉 ¡Tu plan ha sido activado! – B-MaiA')
            ->markdown('emails.plans.activated', [
                'user' => $this->user,
                'plan' => $this->plan,
            ]);
    }
}