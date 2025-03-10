<?php

namespace App\Domain\Ticker\Infrastructure;

class PriceDifferenceDto
{
    public function __construct(
        public readonly int $timeframe,
        public readonly float $latestPrice,
        public readonly float $initialPrice,
    ) {}

    public function getPriceDifference()
    {
        return $this->latestPrice - $this->initialPrice;
    }

    public function getPriceDifferenceAbs()
    {
        return abs($this->latestPrice - $this->initialPrice);
    }

    public function isNegative()
    {
        return $this->latestPrice < $this->initialPrice;
    }

    public function getPercentageDifference()
    {
        return ($this->getPriceDifferenceAbs() / $this->initialPrice) * 100;
    }
}
