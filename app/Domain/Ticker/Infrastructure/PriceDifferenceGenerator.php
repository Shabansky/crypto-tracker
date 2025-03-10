<?php

namespace App\Domain\Ticker\Infrastructure;

use App\Domain\Shared\Domain\TimeframeHoursEnum;
use Illuminate\Database\Eloquent\Collection;

class PriceDifferenceGenerator
{
    /**
     * Attempts to generate PriceDifferenceDto objects for each
     * timeframe as defined in TimeframeHoursEnum. Will ignore
     * cases where the number of collection tickers is lower than
     * the timeframe itself.
     * 
     * @param Collection<HourlyTicker> $collectionTickers
     */
    public static function generate(Collection $collectionTickers)
    {
        foreach (TimeframeHoursEnum::values() as $timeframe) {
            //Something to worry about only in the first day or so.
            if ($timeframe > $collectionTickers->count()) {
                continue;
            }

            $timeFramedTickers = $collectionTickers->slice(0, $timeframe + 1);

            $latestPrice = $timeFramedTickers->first()->price;
            $initialPrice = $timeFramedTickers->last()->price;

            yield new PriceDifferenceDto(
                $timeframe,
                $latestPrice,
                $initialPrice
            );
        }
    }
}
