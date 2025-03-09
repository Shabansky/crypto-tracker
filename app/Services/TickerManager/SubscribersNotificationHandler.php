<?php

namespace App\Services\TickerManager;

use App\Models\HourlyTicker;
use App\Models\HourlyTickerRepository;
use App\Services\TickerManager\PriceDifferenceGenerator;
use App\Services\TickerProviders\BitfinexTickerProvider;
use App\Services\TickerProviders\TickerProviderApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;

class SubscribersNotificationHandler
{
    public function handle()
    {
        //Try to get data from provider
        try {
            $provider = new BitfinexTickerProvider();
            $response = $provider->get();
        } catch (ConnectionException | TickerProviderApiException $e) {
            Log::emergency(sprintf("Service unresponsive: %s", $e->getMessage()));
        }

        //Save to DB
        HourlyTicker::create([
            'price' => $response->price,
            'time' => $response->time
        ]);

        //Get current Tickers (up to 25 - Current one plus the last 24 hours.
        //Reason for this is we need the intervals between these hours)
        $currentTickers = HourlyTickerRepository::getLatestTickersPrices(25);

        foreach (PriceDifferenceGenerator::generate($currentTickers) as $priceDifferenceDto) {
            self::logPriceDifference($priceDifferenceDto);
        }
    }

    private static function logPriceDifference(PriceDifferenceDto $priceDifferenceDto)
    {
        $logMessage = sprintf(
            "Timeframe (hours): %s, Latest Price : %s, Initial Price : %s, Price Difference : %s, Percentage : %s",
            $priceDifferenceDto->timeframe,
            $priceDifferenceDto->latestPrice,
            $priceDifferenceDto->initialPrice,
            $priceDifferenceDto->getPriceDifferenceAbs(),
            $priceDifferenceDto->getPercentageDifference()
        );
        Log::notice($logMessage);
    }
}
