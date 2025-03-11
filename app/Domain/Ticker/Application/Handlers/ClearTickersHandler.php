<?php

namespace App\Domain\Ticker\Application\Handlers;

use App\Domain\Shared\Domain\TimeframeHoursEnum;
use App\Domain\Ticker\Domain\Models\HourlyTicker;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use LogicException;

class ClearTickersHandler
{
    public function handle()
    {
        $maxTimeframe = TimeframeHoursEnum::greatest();
        $retentionHours = env('TICKER_RETENTION_HOURS', $maxTimeframe * 2);

        //Disallow the below case as this will corrupt actively used data
        if ($retentionHours < $maxTimeframe) {
            Log::channel('price_checker')
                ->emergency(
                    sprintf(
                        "Service unresponsive: Ticker retention of %s is less than the maximum timeframe of %s.",
                        $retentionHours,
                        $maxTimeframe
                    )
                );
        }

        $currentTickersQuery =  HourlyTicker::select('id')
            ->whereNotNull('time')
            ->orderByDesc('time');

        if ($currentTickersQuery->count() < $retentionHours) {
            return;
        }

        /**
         * @var Collection $currentTickers
         */
        $currentTickers = $currentTickersQuery->get();

        $currentTickers->slice($retentionHours)->each(function (HourlyTicker $ticker) {
            $ticker->delete();
        });
    }
}
