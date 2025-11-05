<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;

class PlanApiController extends Controller
{
    /**
     * 🔹 Obtener solo los planes de tipo "user"
     * Endpoint: GET /api/plans
     */
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

    /**
     * 🔹 Obtener un plan específico de tipo "user"
     * Endpoint: GET /api/plans/{plan}
     */
    public function show(Plan $plan)
    {
        // Evita mostrar planes de otros tipos
        abort_unless($plan->type === 'user', 404);

        return response()->json([
            'status' => 'success',
            'data'   => $plan,
        ]);
    }
}
