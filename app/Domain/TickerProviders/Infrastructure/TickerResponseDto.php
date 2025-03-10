<?php

namespace App\Domain\TickerProviders\Infrastructure;

class TickerResponseDto
{
    public function __construct(
        public readonly float $price,
        public readonly \DateTime $time,
    ) {}
}
