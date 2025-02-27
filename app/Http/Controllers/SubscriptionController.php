<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    /**
     * Subscribe a user
     * @param Request $request
     * @return Response
     */
    public function subscribe(Request $request)
    {
        // get user information
        $user = Auth::user();

        $subscription = Subscription::updateOrCreate([
            'user_id' => $user->id,
            'subscription_plan_id' => $request->plan_id ?? 1,
            'status' => 'pending',
        ],
            [
            'user_id' => $user->id,
            'subscription_plan_id' => $request->plan_id ?? 1,
            'status' => 'pending',
            'start_date' => now()->timestamp
        ]);

        return response()->json([
            'subscription' => $subscription
        ]);
    }

    /**
     * Save subscription
     * @param Request $request
     * @param Subscription $subscription
     * @return RedirectResponse
     */
    public function save(Request $request, Subscription $subscription): RedirectResponse
    {
        // Process payments

        // update changes
        $subscription->start_date = $request->input('start_date');
        $subscription->end_date = $request->input('end_date');
        $subscription->status = 'active';
        $subscription->save();

        return back()->with('success', 'Subscription saved successfully');
    }
}
