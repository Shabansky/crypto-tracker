<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubscriptionController extends Controller
{
    /**
     * Create a new subscription
     */
    public function post(Request $request)
    {
        return new Response('New Subscription', 200);
    }

    /**
     * Update current subscription. Overwrite currently existing subscription settings.
     */
    public function patch(Request $request)
    {
        return new Response('Edit Existing Subscription', 200);
    }

    /**
     * Removes specified subscription. Deletes all related subscription settings.
     */
    public function delete(Request $request)
    {
        return new Response('Delete Existing Subscription', 200);
    }
}
