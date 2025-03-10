<?php

namespace App\Domain\Subscription\Application\Handlers;

use App\Domain\Shared\Domain\TimeframeHoursEnum;
use App\Domain\Subscription\Domain\Subscription;
use Illuminate\Http\Response;
use InvalidArgumentException;

class addSubscriptionHandler extends Handler
{
    protected function validate()
    {
        $this->request->validate([
            'email' => 'required|email',
            'threshold' => 'required|numeric|gte:0|decimal:0,2',
            'timeframe' => 'required|numeric|in:' . implode(',', TimeframeHoursEnum::values()),
        ], [
            'timeframe.in' => 'The selected timeframe is invalid. Please choose one of the following: ' .  implode(', ', TimeframeHoursEnum::values())
        ]);

        //TODO: Move to repository
        $existingEntry = Subscription
            ::where('email', $this->request->email)
            ->where('timeframe', $this->request->timeframe)
            ->count();

        if ($existingEntry) {
            throw new InvalidArgumentException('Subscription already exists. Consider updating it instead.');
        }
    }

    protected function process(): Response
    {
        Subscription::create([
            'email' => $this->requestContent->email,
            'threshold' => $this->requestContent->threshold,
            'timeframe' => $this->requestContent->timeframe,
        ]);

        return new Response('Subscription added successfully', 200);
    }
}
