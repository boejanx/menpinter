<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\LogService;

class LogUserLogin
{
    public function handle(Login $event): void
    {
        LogService::log(
            'login',
            'auth',
            'User login ke sistem',
            $event->user->id
        );
    }
}
