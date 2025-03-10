<?php

namespace App\Domain\Subscription\Application\Handlers;

use App\Domain\Subscription\Domain\Subscription;
use Illuminate\Http\Response;

class listSubscriptionHandler extends Handler
{
    protected function validate()
    {
        $this->request->validate([
            'email' => 'required|email',
        ]);
    }

    protected function process(): Response
    {
        $existingEntries = Subscription
            ::select('email', 'timeframe', 'threshold')
            ->where('email', $this->requestContent->email)
            ->get();

        return new Response(json_encode($existingEntries->toArray()), 200);
    }
}
