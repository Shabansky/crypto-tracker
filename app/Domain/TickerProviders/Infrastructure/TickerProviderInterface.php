<?php

namespace App\Domain\TickerProviders\Infrastructure;

interface TickerProviderInterface
{
    public const IDENTIFIER = 'default';

    /**
     * Attempt to retrieve a TickerResponseDto object from
     * the 3rd party service. If anything breaks during retrieval
     * and formatting, throw TickerProviderApiException with
     * data on exact cause.
     * 
     * @return TickerResponseDto
     * @throws TickerProviderApiException
     */
    public function get(): ?TickerResponseDto;
}
