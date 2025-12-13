<?php
/*
* Nombre de la clase         : DashboardController.php
* Descripción de la clase    : Controlador que maneja el dashboard administrativo.
* Fecha de creación          : 05/11/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 05/11/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.0
* Fecha de mantenimiento     : 
* Folio de mantenimiento     : 
* Descripción del mantenimiento : 
* Responsable                : 
* Revisor                    : 
*/

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $today = Carbon::today();

        $activeParking = User::query()
            ->whereNotNull('id_plan')
            ->whereDate(
                'end_date', 
                '>=', 
                $today
            )
            ->whereHas('plan', fn ($query) => $query->where('type', 'parking'))
            ->count();

        $activeUser = User::query()
            ->whereNotNull('id_plan')
            ->whereHas('plan', fn ($query) => $query->where('type', 'user'))
            ->where(function ($q) use ($today) 
            {
                $q->whereDate(
                    'end_date', 
                    '>=',
                    $today
                ) ->orWhere(function ($q2) 
                  {
                      $q2->where('id_plan', 4)
                         ->whereNull('end_date');
                  });
            })
            ->count();

        $admin = User::where('phone_number', '7777777777')->first();
        $totalRevenue = (float) optional($admin)->amount ?? 0.0;
        $monthRevenue = 0.00;

        $roleFilter = $request->input('role', '');
        $planFilter = $request->input('plan', '');
        $search     = trim($request->input('q', ''));

        if ($roleFilter === 'dynamic') 
        {
            $planFilter = '';
        }

        if ($roleFilter !== 'dynamic' && $planFilter !== '') 
        {
            $selectedPlan = Plan::find((int) $planFilter);

            if (! $selectedPlan) 
            {
                $planFilter = '';
            } elseif ($roleFilter === '2' && $selectedPlan->type !== 'parking') 
            {
                $planFilter = '';
            } elseif ($roleFilter === '3' && $selectedPlan->type !== 'user') 
            {
                $planFilter = '';
            }
        }

        $usersQuery = User::query()
            ->with(['role', 'plan']);

        if ($roleFilter === '2') 
        {
            $usersQuery->where('id_role', 2);
        } elseif ($roleFilter === '3') 
        {
            $usersQuery->where('id_role', 3);
        } elseif ($roleFilter === 'dynamic') 
        {
            $usersQuery->whereNull('id_role');
        }

        if ($planFilter !== '' && $roleFilter !== 'dynamic') 
        {
            $usersQuery
                ->whereIn('id_role', [2, 3])
                ->where('id_plan', (int) $planFilter);
        }

        if ($search !== '') 
        {
            $usersQuery->where(function ($q) use ($search) 
            {
                $q->where(
                    'name', 
                    'like', 
                    "%{$search}%"
                )->orWhere(
                    'email', 
                    'like', 
                    "%{$search}%"
                )->orWhere(
                    'phone_number', 
                    'like', 
                    "%{$search}%"
                );
            });
        }

        $users = $usersQuery
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $plansQuery = Plan::query()
            ->whereIn('type', ['parking', 'user']);

        if ($roleFilter === '2') 
        {
            $plansQuery->where('type', 'parking');
        } elseif ($roleFilter === '3') 
        {
            $plansQuery->where('type', 'user');
        }

        $plans = $plansQuery
            ->orderBy('type')
            ->orderBy('price')
            ->get();

        return view('admin.admin-dashboard', [
            'active_parking' => $activeParking,
            'active_user'    => $activeUser,
            'total_revenue'  => $totalRevenue,
            'month_revenue'  => $monthRevenue,
            'users'          => $users,
            'plans'          => $plans,
            'role_filter'    => $roleFilter,
            'plan_filter'    => $planFilter,
            'search'         => $search
        ]);
    }
}