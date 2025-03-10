<?php

namespace App\Domain\Subscription\Application;

use App\Infrastructure\Http\Controller;
use App\Domain\Subscription\Domain\Subscription;
use App\Domain\Shared\Domain\TimeframeHoursEnum;
use App\Domain\Subscription\Application\Handlers\addSubscriptionHandler;
use App\Domain\Subscription\Application\Handlers\deleteSubscriptionHandler;
use App\Domain\Subscription\Application\Handlers\editSubscriptionHandler;
use InvalidArgumentException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class SubscriptionController extends Controller
{

    /**
     * List all subscription settings for a given email
     */
    public function get(Request $request)
    {
        $content = $request->getContent();

        try {
            if (!json_validate($content)) {
                throw new InvalidArgumentException("Request body is not a JSON");
            }

            $request->validate([
                'email' => 'required|email',
            ]);

            $content = json_decode($content);

            $existingEntries = Subscription
                ::select('email', 'timeframe', 'threshold')
                ->where('email', $content->email)
                ->get();

            return new Response(json_encode($existingEntries->toArray()), 200);
        } catch (InvalidArgumentException | ValidationException $e) {
            return new Response(sprintf("Invalid request: %s", $e->getMessage()), 400);
        }
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
