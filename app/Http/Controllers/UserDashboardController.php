<?php
/*
* Nombre de la clase         : UserDashboardController.php
* Descripción de la clase    : Controlador que muestra las métricas y estadísticas del dashboard para el usuario autenticado.
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
            $group = "DATE(entry_date)";
            $label = "DATE_FORMAT(entry_date, '%Y-%m-%d')";
        } elseif ($range === 'month')
        {
            $from = (clone $now)->startOfMonth();
            $to = (clone $now)->endOfMonth();
            $group = "DATE(entry_date)";
            $label = "DATE_FORMAT(entry_date, '%Y-%m-%d')";
        } else
        {
            $from = (clone $now)->startOfDay();
            $to = (clone $now)->endOfDay();
            $group = 'HOUR(entry_date)';
            $label = 'HOUR(entry_date)';
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
            ->whereBetween('entry_date', [$from, $to])
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
            ->whereBetween('t.entry_date', [$from, $to])
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
            ->join(
                'user_client_types as uct', 
                'uct.id_user',
                '=', 
                'u.id'
            )
            ->selectRaw("$label AS label, COUNT(DISTINCT t.id_user) AS total")
            ->whereIn('t.id_qr_reader', $readerIds)
            ->whereBetween('t.entry_date', [$from, $to])
            ->whereNull('u.id_role')
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $kpis = [
            'revenue' => DB::table('transactions')
                ->whereIn('id_qr_reader', $readerIds)
                ->whereBetween('entry_date', [$from, $to])
                ->sum('amount'),

            'users_normal' => (int) DB::table('transactions as t')
                ->join(
                    'users as u', 
                    'u.id', 
                    '=', 
                    't.id_user'
                )
                ->whereIn('t.id_qr_reader', $readerIds)
                ->whereBetween('t.entry_date', [$from, $to])
                ->where('u.id_role', 3)
                ->distinct('t.id_user')
                ->count('t.id_user'),

            'users_dynamic' => (int) DB::table('transactions as t')
                ->join(
                    'users as u', 
                    'u.id', 
                    '=', 
                    't.id_user'
                )
                ->join(
                    'user_client_types as uct', 
                    'uct.id_user', 
                    '=', 
                    'u.id'
                )
                ->whereIn('t.id_qr_reader', $readerIds)
                ->whereBetween('t.entry_date', [$from, $to])
                ->whereNull('u.id_role')
                ->distinct('t.id_user')
                ->count('t.id_user')
        ];

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