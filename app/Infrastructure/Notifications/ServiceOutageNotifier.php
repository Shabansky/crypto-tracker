<?php

namespace App\Infrastructure\Notifications;

use App\Domain\Subscription\Domain\Subscription;
use App\Jobs\ProcessOutageEmails;
use Illuminate\Support\Facades\Log;

class ServiceOutageNotifier
{
    public static function run(string $logChannel, $logMessage)
    {
        Log::channel($logChannel)->emergency($logMessage);
        $emailsToNotify =
            Subscription::select('email')
            ->distinct()
            ->pluck('email');

        foreach ($emailsToNotify as $email) {
            ProcessOutageEmails::dispatch($email);
        }
    }

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
}
