<?php

namespace App\Jobs;

use App\Domain\Ticker\Infrastructure\PriceDifferenceDto;
use App\Mail\PriceChangeNotificationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class ProcessEmails implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $email,
        protected PriceDifferenceDto $priceDifferenceDto
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $email = new PriceChangeNotificationMail($this->priceDifferenceDto);
        Mail::to($this->email)->send($email);
    }
}
