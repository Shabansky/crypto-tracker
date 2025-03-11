<?php

namespace Tests\Feature;

use App\Domain\Shared\Domain\TimeframeHoursEnum;
use App\Domain\Ticker\Application\Handlers\ClearTickersHandler;
use App\Domain\Ticker\Domain\Models\HourlyTicker;
use Database\Factories\HourlyTickerFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ClearTickersHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_hourly_tickers_count_is_affected()
    {
        $tickerRetention = 48;

        //Explicit declaration as automatic model getter not working with new DDD structure.
        $factory = new HourlyTickerFactory();

        //Tickers double the number of retention hours
        $factory->count((int) $tickerRetention * 2)->create();

        //Apply a clear. Tickers number goes back to its original value (equals retention hours)
        $handler = new ClearTickersHandler();
        $handler->handle($tickerRetention, (int) $tickerRetention / 2);

        $this->assertEquals((int) $tickerRetention, HourlyTicker::count());
    }

    public function test_retention_less_than_hourly_tickers_does_not_delete()
    {
        //Explicit declaration as automatic model getter not working with new DDD structure.
        $factory = new HourlyTickerFactory();

        //Set ticker retention to be less than max timeframe. This will put the system in an emergency.
        $maxTimeframe = TimeframeHoursEnum::greatest();
        $tickerRetention = $maxTimeframe - 1;

        //Create an arbitrary number of tickers to test against.
        $factory->count(10)->create();
        $initialCount = HourlyTicker::count();

        //Apply a clear. System should go in an emergency. This can be observed by the unchanging number of tickers.
        $handler = new ClearTickersHandler();
        $handler->handle($tickerRetention, $maxTimeframe);

        $finalCount = HourlyTicker::count();

        $this->assertEquals($initialCount, $finalCount);
    }
}
