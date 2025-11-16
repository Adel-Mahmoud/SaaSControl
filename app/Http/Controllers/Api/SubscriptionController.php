<?php

namespace App\Http\Controllers\Api;

use App\Models\Subscription;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionResource;

class SubscriptionController extends Controller
{
    function check($domain)
    {
        $subscription = Subscription::where('domain', $domain)->first();

        if (!$subscription) {
            return response()->json([
                'status' => false,
                'message' => 'Subscription not found',
            ], 404);
        }

        return new SubscriptionResource($subscription);
    }
}
