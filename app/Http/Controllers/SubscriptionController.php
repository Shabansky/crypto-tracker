<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\TimeframeHoursEnum;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Client\RequestException as ClientRequestException;
use InvalidArgumentException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class SubscriptionController extends Controller
{
    /**
     * Create a new subscription
     */
    public function post(Request $request)
    {
        $content = $request->getContent();

        try {
            if (!json_validate($content)) {
                throw new InvalidArgumentException("Request body is not a JSON");
            }

            $request->validate([
                'email' => 'required|email',
                'threshold' => 'required|numeric|gte:0|decimal:0,2',
                'timeframe' => 'required|numeric|in:' . implode(',', TimeframeHoursEnum::values()),
            ], [
                'timeframe.in' => 'The selected timeframe is invalid. Please choose one of the following: ' .  implode(', ', TimeframeHoursEnum::values())
            ]);
        } catch (InvalidArgumentException | ValidationException $e) {
            return new Response(sprintf("Invalid request: %s", $e->getMessage()), 400);
        }

        $content = json_decode($content);

        try {
            //TODO: Move to repository
            $duplicateEntry = Subscription
                ::where('email', $content->email)
                ->where('timeframe', $content->timeframe)
                ->count();

            if ($duplicateEntry > 0) {
                throw new InvalidArgumentException('Subscription already exists. Consider updating it instead.');
            }

            Subscription::create([
                'email' => $content->email,
                'threshold' => $content->threshold,
                'timeframe' => $content->timeframe,
            ]);
        } catch (InvalidArgumentException $e) {
            return new Response($e->getMessage(), 400);
        }

        return new Response('Subscription added successfully', 200);
    }

    /**
     * Update current subscription. Overwrite currently existing subscription settings.
     */
    public function patch(Request $request)
    {
        $content = $request->getContent();
        try {
            if (!json_validate($content)) {
                throw new InvalidArgumentException("Request body is not a JSON");
            }

            $request->validate([
                'email' => 'required|email',
                'threshold' => 'numeric|gte:0|decimal:0,2',
                'timeframe' => 'numeric|in:' . implode(',', TimeframeHoursEnum::values()),
            ], [
                'timeframe.in' => 'The selected timeframe is invalid. Please choose one of the following: ' .  implode(', ', TimeframeHoursEnum::values())
            ]);
        } catch (InvalidArgumentException | ValidationException $e) {
            return new Response(sprintf("Invalid request: %s", $e->getMessage()), 400);
        }

        return new Response('Edit Existing Subscription', 200);
    }

    /**
     * Removes specified subscription. Deletes all related subscription settings.
     */
    public function delete(Request $request)
    {
        $content = $request->getContent();
        try {
            if (!json_validate($content)) {
                throw new InvalidArgumentException("Request body is not a JSON");
            }

            $request->validate([
                'email' => 'required|email',
            ]);
        } catch (InvalidArgumentException | ValidationException $e) {
            return new Response(sprintf("Invalid request: %s", $e->getMessage()), 400);
        }

        return new Response('Delete Existing Subscription', 200);
    }
}
