<?php
/*
* Nombre de la clase         : UserDashboardController.php
* Descripción de la clase    : Controlador que muestra las métricas y estadísticas del dashboard para el usuario autenticado.
* Fecha de creación          : 05/11/2026
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 05/11/2026
* Autorizó                   : Angel Davila
* Versión                    : 2.0
* Fecha de mantenimiento     : 07/01/2026
* Folio de mantenimiento     : L0021
* Tipo de mantenimiento      : Correctivo
* Descripción del mantenimiento : Se modifico las consultas para que los ingresos fueran en base a la fecha almacenada en departure_date
* Responsable                : Elian Pérez
* Revisor                    : Angel Davila
*/

namespace App\Http\Controllers;

use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function __construct(private UserService $service)
    {

    }

    public function index(Request $request): View
    {
        $user = auth()->user();
        $range = $request->input('range', 'day');
        $now = Carbon::now();

        if ($range === 'week')
        {
            $from = (clone $now)->startOfWeek();
            $to = (clone $now)->endOfWeek();
            $group = "DATE(departure_date)";
            $label = "DATE_FORMAT(departure_date, '%Y-%m-%d')";
        } elseif ($range === 'month')
        {
            $from = (clone $now)->startOfMonth();
            $to = (clone $now)->endOfMonth();
            $group = "DATE(departure_date)";
            $label = "DATE_FORMAT(departure_date, '%Y-%m-%d')";
        } else
        {
            $from = (clone $now)->startOfDay();
            $to = (clone $now)->endOfDay();
            $group = "HOUR(departure_date)";
            $label = "HOUR(departure_date)";
        }

        $parking = $user->parking;
        $readerIds = collect();
        $hasParking = (bool) $parking;

        if ($hasParking)
        {
            $readerIds = $parking->qrReaders()->pluck('id');
        }

        $noReaders = !$hasParking || $readerIds->isEmpty();

        if ($noReaders)
        {
            $revenue = collect([]);
            $usersNormal = collect([]);
            $usersDynamic = collect([]);

            $kpis = [
                'revenue'       => 0,
                'users_normal'  => 0,
                'users_dynamic' => 0
            ];

            return view('user.dashboard', [
                'range' => $range,
                'revenue' => $revenue,
                'users_normal' => $usersNormal,
                'users_dynamic' => $usersDynamic,
                'kpis' => $kpis,
                'has_parking' => $hasParking,
                'readers_count' => $hasParking ? $parking->qrReaders()->count() : 0
            ]);
        }

        $revenue = DB::table('transactions')
            ->selectRaw("$label AS label, SUM(amount) AS total")
            ->whereIn('id_qr_reader', $readerIds)
            ->whereNotNull('departure_date')
            ->whereBetween('departure_date', [$from, $to])
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $usersNormal = DB::table('transactions as t')
            ->join(
                'users as u', 
                'u.id', 
                '=', 
                't.id_user'
            )
            ->selectRaw("$label AS label, COUNT(DISTINCT t.id_user) AS total")
            ->whereIn('t.id_qr_reader', $readerIds)
            ->whereBetween('t.departure_date', [$from, $to])
            ->where('u.id_role', 3)
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $usersDynamic = DB::table('transactions as t')
            ->join(
                'users as u', 
                'u.id', 
                '=', 
                't.id_user'
            )
            ->leftJoin(
                'user_client_types as uct', 
                'uct.id_user', 
                '=', 
                'u.id'
            )
            ->selectRaw("$label AS label, COUNT(DISTINCT t.id_user) AS total")
            ->whereIn('t.id_qr_reader', $readerIds)
            ->whereNotNull('t.departure_date')
            ->whereBetween('t.departure_date', [$from, $to])
            ->whereNull('u.id_role')
            ->where(function ($q) 
            {
                $q->whereNull('u.id_plan')
                ->orWhereNotNull('uct.id'); 
            })
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $kpis['revenue'] = (float) DB::table('transactions')
            ->whereIn('id_qr_reader', $readerIds)
            ->whereNotNull('departure_date')
            ->whereBetween('departure_date', [$from, $to])
            ->sum('amount');

        $kpis['users_normal'] = (int) DB::table('transactions as t')
            ->join(
                'users as u', 
                'u.id', 
                '=', 
                't.id_user'
            )
            ->whereIn('t.id_qr_reader', $readerIds)
            ->whereNotNull('t.departure_date')
            ->whereBetween('t.departure_date', [$from, $to])
            ->where('u.id_role', 3)
            ->distinct('t.id_user')
            ->count('t.id_user');

        $kpis['users_dynamic'] = (int) DB::table('transactions as t')
            ->join(
                'users as u', 
                'u.id', 
                '=', 
                't.id_user'
            )
            ->leftJoin(
                'user_client_types as uct', 
                'uct.id_user', 
                '=', 
                'u.id'
            )
            ->whereIn('t.id_qr_reader', $readerIds)
            ->whereNotNull('t.departure_date')
            ->whereBetween('t.departure_date', [$from, $to])
            ->whereNull('u.id_role')
            ->where(function ($q) 
            {
                $q->whereNull('u.id_plan')
                ->orWhereNotNull('uct.id');
            })
            ->distinct('t.id_user')
            ->count('t.id_user');


        return view('user.dashboard', [
            'range' => $range,
            'revenue' => $revenue,
            'users_normal' => $usersNormal,
            'users_dynamic' => $usersDynamic,
            'kpis' => $kpis,
            'has_parking' => true,
            'readers_count' => $parking->qrReaders()->count()
        ]);
    }
}