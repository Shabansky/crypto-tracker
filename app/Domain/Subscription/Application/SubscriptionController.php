<?php

namespace App\Domain\Subscription\Application;

use App\Domain\Shared\Domain\TimeframeHoursEnum;
use App\Infrastructure\Http\Controller;
use App\Domain\Subscription\Application\Handlers\addSubscriptionHandler;
use App\Domain\Subscription\Application\Handlers\deleteSubscriptionHandler;
use App\Domain\Subscription\Application\Handlers\editSubscriptionHandler;
use App\Domain\Subscription\Application\Handlers\listSubscriptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;


#[OA\Info(
    version: '1.0.0',
    title: 'Subscription API',
    description: 'Allows for listing and control of different email subscriptions'
)]
class SubscriptionController extends Controller
{

    /**
     * List all subscription settings for a given email
     */
    #[OA\Get(path: '/subscription/{email}', description: 'List all subscription settings for a given email')]
    #[OA\Parameter(
        name: 'email',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string'),
        description: 'The email to search subscriptions by'
    )]

    #[OA\Response(response: Response::HTTP_OK, description: 'OK')]
    #[OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Validation errors')]
    public function list(string $email)
    {
        $request = new Request(['email' => $email]);
        return new listSubscriptionHandler()->run($request);
    }

    /**
     * Create a new subscription
     */
    #[OA\Post(path: '/subscription', description: 'Add a new subscription')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email', 'timeframe', 'threshold'],
            properties: [
                new OA\Property(
                    property: 'email',
                    type: 'string',
                    description: 'The email to search subscriptions by'
                ),
                new OA\Property(
                    property: 'timeframe',
                    type: 'integer',
                    enum: TimeframeHoursEnum::VALUES,
                    description: 'The timeframe in hours at which to check for price deviations'
                ),
                new OA\Property(
                    property: 'threshold',
                    type: 'float',
                    minimum: 0,
                    description: 'A percentage of the price difference to look for within the given time frame'
                )
            ]
        )
    )]
    #[OA\Response(response: Response::HTTP_OK, description: 'OK')]
    #[OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Validation errors')]
    #[OA\Response(response: Response::HTTP_CONFLICT, description: 'Subscriber to be created exists')]
    #[OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Service unable to process request')]
    public function post(Request $request): Response
    {
        return new addSubscriptionHandler()->run($request);
    }

    /**
     * Update current subscription. Overwrite currently existing subscription settings.
     */
    #[OA\Patch(path: '/subscription', description: 'Update a subscriber setting')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email', 'timeframe', 'threshold'],
            properties: [
                new OA\Property(
                    property: 'email',
                    type: 'string',
                    description: 'The email to search subscriptions by'
                ),
                new OA\Property(
                    property: 'timeframe',
                    type: 'integer',
                    enum: TimeframeHoursEnum::VALUES,
                    description: 'The timeframe in hours at which to check for price deviations'
                ),
                new OA\Property(
                    property: 'threshold',
                    type: 'float',
                    minimum: 0,
                    description: 'A percentage of the price difference to look for within the given time frame'
                )
            ]
        )
    )]
    #[OA\Response(response: Response::HTTP_OK, description: 'OK')]
    #[OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Validation errors')]
    #[OA\Response(response: Response::HTTP_CONFLICT, description: 'Subscriber to be updated does not exist')]
    #[OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Service unable to process request')]
    public function patch(Request $request): Response
    {
        return new editSubscriptionHandler()->run($request);
    }

    /**
     * Remove specified subscription. Delete all related subscription settings.
     */
    #[OA\Delete(path: '/subscription', description: 'Deletes either a subscription or a subscription setting if a timeframe is defined')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email',],
            properties: [
                new OA\Property(
                    property: 'email',
                    type: 'string',
                    description: 'The email to search subscriptions by'
                ),
                new OA\Property(
                    property: 'timeframe',
                    type: 'integer',
                    enum: TimeframeHoursEnum::VALUES,
                    description: 'The timeframe in hours at which to check for price deviations. If defined, delete a subscription setting.'
                )
            ]
        )
    )]
    #[OA\Response(response: Response::HTTP_OK, description: 'OK')]
    #[OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Validation errors')]
    #[OA\Response(response: Response::HTTP_CONFLICT, description: 'Subscriber to be deleted does not exist')]
    #[OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Service unable to process request')]
    public function delete(Request $request)
    {
        return new deleteSubscriptionHandler()->run($request);
    }
}
