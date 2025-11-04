<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $today = Carbon::today();

        // Planes activos (Parking)
        $activeParking = User::query()
            ->whereNotNull('id_plan')
            ->whereDate('end_date', '>=', $today)
            ->whereHas('plan', fn($q) => $q->where('type', 'parking'))
            ->count();

        // Planes activos (Usuario)
        $activeUser = User::query()
            ->whereNotNull('id_plan')
            ->whereDate('end_date', '>=', $today)
            ->whereHas('plan', fn($q) => $q->where('type', 'user'))
            ->count();

        // Ingresos acumulados (desde el admin con teléfono 777...)
        $admin = User::where('phone_number', '7777777777')->first();
        $totalRevenue = (float) optional($admin)->amount ?? 0.0;

        // Ingreso del mes actual (si tienes tabla de pagos, reemplaza este bloque)
        // Si aún no la tienes, mostramos 0.00 y luego te digo cómo agregarla.
        $monthRevenue = 0.00;

        return view('admin-dashboard', compact(
            'activeParking',
            'activeUser',
            'totalRevenue',
            'monthRevenue'
        ));
    }
}
