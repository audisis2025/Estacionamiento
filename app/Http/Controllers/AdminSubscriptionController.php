<?php
/*
* Nombre de la clase         : AdminSubscriptionController.php
* Descripción de la clase    : Controlador para administrar las suscripciones (planes)
*                              de usuarios de estacionamiento y usuarios de la app.
* Fecha de creación          : 08/12/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 08/12/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.0
* Fecha de mantenimiento     : 
* Folio de mantenimiento     : 
* Tipo de mantenimiento      : 
* Descripción del mantenimiento :
* Responsable                : 
* Revisor                    : 
*/

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class AdminSubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $roleFilter   = $request->input('role', '');
        $statusFilter = $request->input('status', '');
        $search       = trim($request->input('q', ''));

        $today = Carbon::today();

        $query = User::query()
            ->with('plan')
            ->whereIn('id_role', [2, 3]);

        if ($roleFilter === '2') 
        {
            $query->where('id_role', 2);
        } elseif ($roleFilter === '3')
        {
            $query->where('id_role', 3);
        }

        if ($statusFilter === 'active') 
        {
            $query->whereNotNull('id_plan')
                ->where(function ($q) use ($today) 
                {
                    $q->whereDate(
                        'end_date', 
                        '>=', 
                        $today
                    )
                    ->orWhere(function ($q2) 
                    {
                        $q2->where('id_plan', 4)
                            ->whereNull('end_date');
                    });
                });
        } elseif ($statusFilter === 'expired') 
        {
            $query->where(function ($q) use ($today) 
            {
                $q->whereNull('id_plan')
                ->orWhere(function ($q2) use ($today) 
                {
                    $q2->whereNotNull('id_plan')
                        ->where(function ($q3) use ($today) 
                        {
                            $q3->whereNull('end_date')->where(
                                'id_plan', 
                                '!=', 
                                4
                            )->orWhereDate(
                                'end_date', 
                                '<', 
                                $today
                            );
                        });
                });
            });
        }

        if ($search !== '') 
        {
            $query->where(function ($q) use ($search) 
            {
                $q->where(
                    'name', 
                    'like', 
                    "%{$search}%"
                )
                ->orWhere(
                    'email', 
                    'like', 
                    "%{$search}%"
                )
                ->orWhere(
                    'phone_number', 
                    'like', 
                    "%{$search}%"
                );
            });
        }

        $users = $query
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.subscriptions.index', [
            'users' => $users,
            'role_filter' => $roleFilter,
            'status_filter' => $statusFilter,
            'search' => $search
        ]);
    }


    public function cancel(User $user): RedirectResponse
    {
        if (! in_array((int) $user->id_role, [2, 3], true)) 
        {
            return back()->with('swal', [
                'icon' => 'error',
                'title' => 'Operación no permitida',
                'text' => 'Solo puedes cancelar planes de estacionamientos y usuarios app.'
            ]);
        }

        if (is_null($user->id_plan)) 
        {
            return back()->with('swal', [
                'icon' => 'info',
                'title' => 'Sin plan',
                'text' => 'Este usuario no tiene un plan asignado actualmente.'
            ]);
        }

        if($user->id_role === 3)
        {
           $user->update(['id_plan' => 4, 'end_date' => null]); 
        } else
        {
            $user->update(['id_plan' => null, 'end_date' => null]);
        }

        return back()->with('swal', [
            'icon' => 'success',
            'title' => 'Suscripción cancelada',
            'text' => 'El plan del usuario fue cancelado correctamente.'
        ]);
    }

    public function renew(User $user): RedirectResponse
    {
        if (! in_array((int) $user->id_role, [2, 3], true)) 
        {
            return back()->with('swal', [
                'icon' => 'error',
                'title' => 'Operación no permitida',
                'text' => 'Solo puedes renovar planes de estacionamientos y usuarios app.'
            ]);
        }

        $plan = $user->plan;

        if (! $plan) {
            return back()->with('swal', [
                'icon' => 'error',
                'title' => 'Sin plan',
                'text' => 'El usuario no tiene un plan asignado.'
            ]);
        }

        $days = (int) ($plan->duration_days ?? 30);

        $newEndDate = Carbon::today()->addDays($days);

        $user->update(['end_date' => $newEndDate]);

        return back()->with('swal', [
            'icon' => 'success',
            'title' => 'Plan renovado',
            'text' => "Se renovó el plan por {$days} días a partir de hoy."
        ]);
    }
}
