<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function store(Request $request)
    {
        $subscriptionPlan = SubscriptionPlan::create([
            'name' => $request->name,
            'price' => $request->price,
            'duration' => $request->duration,
            'features' => json_encode($request->features),
        ]);
    }
}
