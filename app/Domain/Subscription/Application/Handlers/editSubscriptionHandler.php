<?php

namespace App\Domain\Subscription\Application\Handlers;

use App\Domain\Shared\Domain\TimeframeHoursEnum;
use App\Domain\Subscription\Domain\Subscription;
use Illuminate\Http\Response;
use InvalidArgumentException;

class editSubscriptionHandler extends Handler
{
    protected $existingEntry;

    protected function validate()
    {
        $this->request->validate([
            'email' => 'required|email',
            'threshold' => 'required|numeric|gte:0|decimal:0,2',
            'timeframe' => 'required|numeric|in:' . implode(',', TimeframeHoursEnum::values()),
        ], [
            'timeframe.in' => 'The selected timeframe is invalid. Please choose one of the following: ' .  implode(', ', TimeframeHoursEnum::values())
        ]);
    }

    protected function process(): Response
    {
        $existingEntry = Subscription
            ::where('email', $this->requestContent->email)
            ->where('timeframe', $this->requestContent->timeframe)
            ->first();

        if ($existingEntry === null) {
            throw new InvalidArgumentException('Subscription does not exist. Consider adding it instead.');
        }

        $existingEntry->threshold = $this->requestContent->threshold;
        $existingEntry->save();

        return new Response('Subscription updated successfully', 200);
    }
}
