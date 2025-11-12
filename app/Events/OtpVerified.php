<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OtpVerified
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $login;
    public $password;
    public $numeroUser;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, string $login, string $password, string $numeroUser)
    {
        $this->user = $user;
        $this->login = $login;
        $this->password = $password;
        $this->numeroUser = $numeroUser;
    }
}