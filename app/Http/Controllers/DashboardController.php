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

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
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
            ->whereDate(
                'end_date',
                '>=',
                $today
            )
            ->whereHas('plan', fn ($query) => $query->where('type', 'user'))
            ->count();

        $admin = User::where('phone_number','7777777777')->first();

        $totalRevenue = (float) optional($admin)->amount ?? 0.0;

        $monthRevenue = 0.00;

        return view('admin.admin-dashboard', compact(
                                                        'activeParking',
                                                        'activeUser',
                                                        'totalRevenue',
                                                        'monthRevenue'
                                                    )
        );
    }
}
