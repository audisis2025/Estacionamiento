<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;

class PlanApiController extends Controller
{
    public function index()
    {
        $plans = Plan::where('type', 'user')
            ->orderBy('price')
            ->orderBy('duration_days')
            ->get();

        return response()->json([
            'status' => 'success',
            'count'  => $plans->count(),
            'data'   => $plans,
        ]);
    }

    public function show(Plan $plan)
    {
        abort_unless($plan->type === 'user', 404);

        return response()->json([
            'status' => 'success',
            'data'   => $plan,
        ]);
    }
}
