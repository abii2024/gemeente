<?php

namespace App\Listeners;

use App\Services\PrivacyLogger;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        PrivacyLogger::logSuccessfulLogin();
    }
}
