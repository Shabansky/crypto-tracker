<?php

namespace App\Domain\Subscription\Application\Handlers;

use App\Domain\Shared\Domain\TimeframeHoursEnum;
use App\Domain\Subscription\Domain\Subscription;
use Illuminate\Http\Response;
use InvalidArgumentException;

class deleteSubscriptionHandler extends Handler
{
    protected function validate()
    {
        $rulesToValidate = [
            'email' => 'required|email'
        ];

        if ($this->request->query('timeframe') !== null) {
            $rulesToValidate['timeframe'] = 'numeric|in:' . implode(',', TimeframeHoursEnum::values());
        }

        $this->request->validate($rulesToValidate);
    }

    protected function process(): Response
    {
        if ($this->request->query('timeframe') !== null) {
            return $this->deleteSubscriptionSetting();
        }

        return $this->deleteSubscription();
    }

    protected function deleteSubscriptionSetting(): Response
    {
        $existingEntry = Subscription
            ::where('email', $this->request->query('email'))
            ->where('timeframe', $this->request->query('timeframe'));

        if (!$existingEntry->count()) {
            throw new InvalidArgumentException('Subscription setting does not exist. Cannot delete.');
        }

        $existingEntry->first()->delete();

        return new Response('Subscription setting deleted successfully', 200);
    }

    protected function deleteSubscription(): Response
    {
        $existingEntries = Subscription
            ::where('email', $this->request->query('email'));

        if (!$existingEntries->count()) {
            throw new InvalidArgumentException('Subscription does not exist. Cannot delete.');
        }

        $existingEntries->get()->each(function (Subscription $subscription) {
            $subscription->delete();
        });

        return new Response('Subscription deleted successfully', 200);
    }
}
