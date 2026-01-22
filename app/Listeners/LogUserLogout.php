<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Services\LogService;

class LogUserLogout
{
    public function handle(Logout $event): void
    {
        LogService::log(
            'logout',
            'auth',
            'User logout dari sistem',
            $event->user?->id
        );
    }
}
