<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user    = auth()->user();
        $range   = $request->input('range', 'day'); // day|week|month
        $now     = Carbon::now();

        // Ventana fija (no “corre” la UI sola; solo cambia si el user cambia range)
        if ($range === 'week') {
            $from  = (clone $now)->startOfWeek();
            $to    = (clone $now)->endOfWeek();
            $group = "YEARWEEK(entry_date, 1)";
            $label = "CONCAT(YEAR(entry_date), '-W', LPAD(WEEK(entry_date,1),2,'0'))";
        } elseif ($range === 'month') {
            $from  = (clone $now)->startOfMonth();
            $to    = (clone $now)->endOfMonth();
            $group = "DATE_FORMAT(entry_date, '%Y-%m')";
            $label = $group;
        } else {
            $from  = (clone $now)->startOfDay();
            $to    = (clone $now)->endOfDay();
            $group = "DATE(entry_date)";
            $label = $group;
        }

        // --------- NUEVO: tolerar usuario sin estacionamiento/lectores ----------
        $parking   = $user->parking;           // puede ser null
        $readerIds = collect();                // por defecto vacío
        $hasParking = (bool) $parking;

        if ($hasParking) {
            $readerIds = $parking->qrReaders()->pluck('id'); // puede estar vacío
        }

        // Si NO hay parking o NO hay lectores, no ejecutes consultas con whereIn vacío.
        $noReaders = !$hasParking || $readerIds->isEmpty();

        if ($noReaders) {
            // Datos “vacíos”, la vista sigue renderizando sin romper
            $revenue      = collect([]);
            $usersNormal  = collect([]);
            $usersDynamic = collect([]);
            $kpis = [
                'revenue'        => 0,
                'users_normal'   => 0,
                'users_dynamic'  => 0,
            ];

            return view('dashboard', [
                'range'         => $range,
                'revenue'       => $revenue,
                'usersNormal'   => $usersNormal,
                'usersDynamic'  => $usersDynamic,
                'kpis'          => $kpis,
                'hasParking'    => $hasParking,
                'readersCount'  => $hasParking ? $parking->qrReaders()->count() : 0,
            ]);
        }

        // --------- Hay lectores: ejecuta consultas normalmente ----------
        $revenue = DB::table('transactions')
            ->selectRaw("$label AS label, SUM(amount) AS total")
            ->whereIn('id_qr_reader', $readerIds)
            ->whereBetween('entry_date', [$from, $to])
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $usersNormal = DB::table('transactions as t')
            ->join('users as u', 'u.id', '=', 't.id_user')
            ->selectRaw("$label AS label, COUNT(DISTINCT t.id_user) AS total")
            ->whereIn('t.id_qr_reader', $readerIds)
            ->whereBetween('t.entry_date', [$from, $to])
            ->where('u.id_role', 3)
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $usersDynamic = DB::table('transactions as t')
            ->join('users as u', 'u.id', '=', 't.id_user')
            ->join('user_client_types as uct', 'uct.id_user', '=', 'u.id')
            ->selectRaw("$label AS label, COUNT(DISTINCT t.id_user) AS total")
            ->whereIn('t.id_qr_reader', $readerIds)
            ->whereBetween('t.entry_date', [$from, $to])
            ->whereNull('u.id_role') // tu definición de “dinámico” (ajústala si hace falta)
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $kpis = [
            'revenue' => (int) DB::table('transactions')
                ->whereIn('id_qr_reader', $readerIds)
                ->whereBetween('entry_date', [$from, $to])
                ->sum('amount'),

            'users_normal' => (int) DB::table('transactions as t')
                ->join('users as u', 'u.id', '=', 't.id_user')
                ->whereIn('t.id_qr_reader', $readerIds)
                ->whereBetween('t.entry_date', [$from, $to])
                ->where('u.id_role', 3)
                ->distinct('t.id_user')->count('t.id_user'),

            'users_dynamic' => (int) DB::table('transactions as t')
                ->join('users as u', 'u.id', '=', 't.id_user')
                ->join('user_client_types as uct', 'uct.id_user', '=', 'u.id')
                ->whereIn('t.id_qr_reader', $readerIds)
                ->whereBetween('t.entry_date', [$from, $to])
                ->whereNull('u.id_role')
                ->distinct('t.id_user')->count('t.id_user'),
        ];

        return view('dashboard', [
            'range'         => $range,
            'revenue'       => $revenue,
            'usersNormal'   => $usersNormal,
            'usersDynamic'  => $usersDynamic,
            'kpis'          => $kpis,
            'hasParking'    => true,
            'readersCount'  => $parking->qrReaders()->count(),
        ]);
    }
}
