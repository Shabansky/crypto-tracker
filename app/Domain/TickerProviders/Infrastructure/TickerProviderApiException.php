<?php

namespace App\Domain\TickerProviders\Infrastructure;

use Exception;

class TickerProviderApiException extends Exception
{
    /**
     * Create a new class instance.
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
