<?php

namespace App\Services\TickerManager;

use App\TimeframeHoursEnum;
use Illuminate\Database\Eloquent\Collection;

class PriceDifferenceGenerator
{
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
