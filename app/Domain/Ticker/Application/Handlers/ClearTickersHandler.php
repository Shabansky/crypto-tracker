<?php

namespace App\Domain\Ticker\Application\Handlers;

use App\Domain\Ticker\Domain\Models\HourlyTicker;
use App\Infrastructure\Notifications\ServiceOutageNotifier;
use Illuminate\Database\Eloquent\Collection;

class ClearTickersHandler
{
    public function handle(int $retentionHours, int $maxTimeframe)
    {
        //Disallow the below case as this will corrupt actively used data
        if ($retentionHours < $maxTimeframe) {
            ServiceOutageNotifier::run(
                'price_checker',
                sprintf(
                    "Service unresponsive: Ticker retention of %s is less than the maximum timeframe of %s.",
                    $retentionHours,
                    $maxTimeframe
                )
            );
            return;
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

        $currentTickers->slice($retentionHours)->each(function ($ticker) {
            if ($ticker instanceof HourlyTicker) {
                $ticker->delete();
            }
        });
    }
}
