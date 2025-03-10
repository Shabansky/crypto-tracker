<?php

namespace App\Mail;

use App\Domain\Ticker\Infrastructure\PriceDifferenceDto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Mockery\ReceivedMethodCalls;

class PriceChangeNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected PriceDifferenceDto $priceDifferenceDto;

    /**
     * Create a new message instance.
     */
    public function __construct(PriceDifferenceDto $priceDifferenceDto)
    {
        $this->priceDifferenceDto = $priceDifferenceDto;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Price Change Notification',
            from: 'test@mytracker.com',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.price_change_notification',
            with: [
                'timeframe' => $this->priceDifferenceDto->timeframe,
                'latestPrice' => $this->priceDifferenceDto->latestPrice,
                'initialPrice' => $this->priceDifferenceDto->initialPrice,
                'priceDifference' => $this->priceDifferenceDto->getPriceDifference(),
                'percentageDifference' => $this->priceDifferenceDto->getPercentageDifference(),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
