<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Cancel an active subscription.
     * The subscription remains active until the end of the current billing period.
     */
    public function cancel(Request $request, Subscription $subscription)
    {
        // Ensure user owns the subscription
        if ($subscription->client_id !== Auth::id()) {
            abort(403);
        }

        if ($subscription->status !== 'active') {
            return back()->with('error', 'Only active subscriptions can be cancelled.');
        }

        $subscription->update([
            'canceled_at' => now(),
            // We keep status as 'active' but set canceled_at
            // The scheduler or a separate job will set status to 'expired' at ends_at
        ]);

        return back()->with('success', 'Your subscription has been cancelled and will expire at the end of the current billing period.');
    }
}
