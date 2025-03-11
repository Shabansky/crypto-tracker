<?php

namespace App\Domain\Ticker\Application\Handlers;

use App\Domain\Subscription\Domain\Subscription;
use App\Domain\Ticker\Domain\Models\HourlyTicker;
use App\Domain\Ticker\Domain\Repositories\HourlyTickerRepository;
use App\Domain\Ticker\Infrastructure\PriceDifferenceGenerator;
use App\Domain\Ticker\Infrastructure\PriceDifferenceDto;
use App\Domain\TickerProviders\Infrastructure\TickerProviderInterface;
use App\Domain\TickerProviders\Infrastructure\TickerProviderApiException;
use App\Jobs\ProcessPriceEmails;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;

class SubscribersNotificationHandler
{
    public function handle(TickerProviderInterface $provider)
    {
        $this->addTickerFromProvider($provider);

        //Get current Tickers (up to 25 - Current one plus the last 24 hours.
        //Reason for this is we need the intervals between these hours)
        $currentTickers = HourlyTickerRepository::getLatestTickersPrices(25);

        foreach (PriceDifferenceGenerator::generate($currentTickers) as $priceDifferenceDto) {
            $emailsToNotify = $this->getEmailsToNotifyByTimeframe($priceDifferenceDto);

            self::logPriceDifference($priceDifferenceDto);

            if (empty($emailsToNotify)) {
                continue;
            }

            self::logMailQueueInfo($priceDifferenceDto->timeframe, count($emailsToNotify));
            foreach ($emailsToNotify as $email) {
                ProcessPriceEmails::dispatch($email, $priceDifferenceDto);
            }
        }
    }

    protected static function logPriceDifference(PriceDifferenceDto $priceDifferenceDto)
    {
        $logMessage = sprintf(
            "Timeframe (hours): %s, Latest Price : %s, Initial Price : %s, Price Difference : %s, Percentage : %s",
            $priceDifferenceDto->timeframe,
            $priceDifferenceDto->latestPrice,
            $priceDifferenceDto->initialPrice,
            $priceDifferenceDto->getPriceDifferenceAbs(),
            $priceDifferenceDto->getPercentageDifference()
        );
        Log::channel('price_checker')->notice($logMessage);
    }

    protected static function logMailQueueInfo(int $timeframe, int $emailsCount)
    {
        $logMessage = sprintf(
            "Timeframe (hours): %s, Mails queued to send: %s",
            $timeframe,
            $emailsCount
        );
        Log::channel('emails')->notice($logMessage);
    }

    protected function getEmailsToNotifyByTimeframe(
        PriceDifferenceDto $priceDifferenceDto
    ): array {
        return Subscription::select('email')
            ->where('timeframe', $priceDifferenceDto->timeframe)
            ->where('threshold', '<=', $priceDifferenceDto->getPercentageDifference())
            ->pluck('email')
            ->toArray();
    }

    protected function addTickerFromProvider(TickerProviderInterface $provider)
    {
        //Try to get data from provider
        try {
            $response = $provider->get();
        } catch (ConnectionException | TickerProviderApiException $e) {
            Log::channel('price_checker')->emergency(sprintf("Service unresponsive: %s", $e->getMessage()));
            return;
        }

        //Save to DB
        HourlyTicker::create([
            'price' => $response->price,
            'time' => $response->time
        ]);
    }
}
