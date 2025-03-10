<?php

namespace App\Domain\Subscription\Application;

use App\Infrastructure\Http\Controller;
use App\Domain\Subscription\Domain\Subscription;
use App\Domain\Shared\Domain\TimeframeHoursEnum;
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

            $content = json_decode($content);

            //TODO: Move to repository
            $existingEntry = Subscription
                ::where('email', $content->email)
                ->where('timeframe', $content->timeframe)
                ->count();

            if ($existingEntry) {
                throw new InvalidArgumentException('Subscription already exists. Consider updating it instead.');
            }
        } catch (InvalidArgumentException | ValidationException $e) {
            return new Response(sprintf("Invalid request: %s", $e->getMessage()), 400);
        }

        try {
            Subscription::create([
                'email' => $content->email,
                'threshold' => $content->threshold,
                'timeframe' => $content->timeframe,
            ]);
        } catch (\Exception $e) {
            //TODO: Log as well
            return new Response('Error creating subscription', 500);
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

            $content = json_decode($content);

            //TODO: Move to repository
            $existingEntry = Subscription
                ::where('email', $content->email)
                ->where('timeframe', $content->timeframe);
            if (!$existingEntry->count()) {
                throw new InvalidArgumentException('Subscription does not exist. Consider adding it instead.');
            }
        } catch (InvalidArgumentException | ValidationException $e) {
            return new Response(sprintf("Invalid request: %s", $e->getMessage()), 400);
        }

        $existingEntry = $existingEntry->first();
        $existingEntry->threshold = $content->threshold;

        try {
            $existingEntry->save();
        } catch (\Exception $e) {
            //TODO: Log as well
            return new Response('Error updating subscription', 500);
        }

        return new Response('Edit Existing Subscription', 200);
    }

    /**
     * Remove specified subscription. Delete all related subscription settings.
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
                'timeframe' => 'numeric|in:' . implode(',', TimeframeHoursEnum::values()),
            ]);

            $content = json_decode($content);
            isset($content->timeframe) ?
                $this->deleteSubscriptionSetting($content->email, $content->timeframe) : $this->deleteSubscription($content->email);
        } catch (InvalidArgumentException | ValidationException $e) {
            return new Response(sprintf("Invalid request: %s", $e->getMessage()), 400);
        }

        return new Response('Delete Existing Subscription', 200);
    }

    protected function deleteSubscriptionSetting($email, $timeframe)
    {
        $existingEntry = Subscription
            ::where('email', $email)
            ->where('timeframe', $timeframe);

        if (!$existingEntry->count()) {
            throw new InvalidArgumentException('Subscription setting does not exist. Cannot delete.');
        }

        try {
            $existingEntry->get()->first()->delete();
        } catch (\Exception $e) {
            //TODO: Log as well
            return new Response('Error deleting subscription setting', 500);
        }
    }

    protected function deleteSubscription($email)
    {
        $existingEntries = Subscription
            ::where('email', $email);

        if (!$existingEntries->count()) {
            throw new InvalidArgumentException('Subscription does not exist. Cannot delete.');
        }

        try {
            $existingEntries->get()->each(function (Subscription $subscription) {
                $subscription->delete();
            });
        } catch (\Throwable $e) {
            //TODO: Log as well
            var_dump($e->getMessage());
            return new Response('Error deleting subscription setting', 500);
        }
    }
}
