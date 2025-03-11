<?php

namespace App\Jobs;

use App\Mail\ServiceOutageMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessOutageEmails implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $email
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $email = new ServiceOutageMail();
        Log::notice("HERE");
        Mail::to($this->email)->send($email);
    }
}
