<?php

namespace App\Services\TickerProviders;

class TickerResponseDto
{
    public function __construct(
        public readonly float $price,
        public readonly \DateTime $time,
    ) {}
}
