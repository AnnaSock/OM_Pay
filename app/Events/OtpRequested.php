<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OtpRequested
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $otp;
    public $numeroUser;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, string $otp, string $numeroUser)
    {
        $this->user = $user;
        $this->otp = $otp;
        $this->numeroUser = $numeroUser;
    }
}