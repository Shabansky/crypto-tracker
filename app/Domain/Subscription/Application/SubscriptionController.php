<?php

namespace App\Domain\Subscription\Application;

use App\Infrastructure\Http\Controller;
use App\Domain\Subscription\Domain\Subscription;
use App\Domain\Shared\Domain\TimeframeHoursEnum;
use App\Domain\Subscription\Application\Handlers\addSubscriptionHandler;
use App\Domain\Subscription\Application\Handlers\deleteSubscriptionHandler;
use App\Domain\Subscription\Application\Handlers\editSubscriptionHandler;
use App\Domain\Subscription\Application\Handlers\listSubscriptionHandler;
use InvalidArgumentException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class SubscriptionController extends Controller
{

    /**
     * List all subscription settings for a given email
     */
    public function list(Request $request)
    {
        return new listSubscriptionHandler()->run($request);
    }

    /**
     * Create a new subscription
     */
    public function post(Request $request): Response
    {
        return new addSubscriptionHandler()->run($request);
    }

    /**
     * Update current subscription. Overwrite currently existing subscription settings.
     */
    public function patch(Request $request): Response
    {
        return new editSubscriptionHandler()->run($request);
    }

    /**
     * Remove specified subscription. Delete all related subscription settings.
     */
    public function delete(Request $request)
    {
        return new deleteSubscriptionHandler()->run($request);
    }
}
