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

use App\Http\Requests\UpdateNotificationTokenRequest;
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
            $group = "YEARWEEK(entry_date, 1)";
            $label = "CONCAT(YEAR(entry_date), '-W', LPAD(WEEK(entry_date,1),2,'0'))";
        } elseif ($range === 'month')
        {
            $from = (clone $now)->startOfMonth();
            $to = (clone $now)->endOfMonth();
            $group = "DATE_FORMAT(entry_date, '%Y-%m')";
            $label = $group;
        } else
        {
            $from = (clone $now)->startOfDay();
            $to = (clone $now)->endOfDay();
            $group = 'DATE(entry_date)';
            $label = $group;
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
                'users_dynamic' => 0,
            ];

            return view('user.dashboard', [
                'range'        => $range,
                'revenue'      => $revenue,
                'usersNormal'  => $usersNormal,
                'usersDynamic' => $usersDynamic,
                'kpis'         => $kpis,
                'hasParking'   => $hasParking,
                'readersCount' => $hasParking ? $parking->qrReaders()->count() : 0,
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
                ->join('users as u', 'u.id', '=', 't.id_user')
                ->whereIn('t.id_qr_reader', $readerIds)
                ->whereBetween('t.entry_date', [$from, $to])
                ->where('u.id_role', 3)
                ->distinct('t.id_user')
                ->count('t.id_user'),

            'users_dynamic' => (int) DB::table('transactions as t')
                ->join('users as u', 'u.id', '=', 't.id_user')
                ->join('user_client_types as uct', 'uct.id_user', '=', 'u.id')
                ->whereIn('t.id_qr_reader', $readerIds)
                ->whereBetween('t.entry_date', [$from, $to])
                ->whereNull('u.id_role')
                ->distinct('t.id_user')
                ->count('t.id_user'),
        ];

        return view('user.dashboard', [
            'range'        => $range,
            'revenue'      => $revenue,
            'usersNormal'  => $usersNormal,
            'usersDynamic' => $usersDynamic,
            'kpis'         => $kpis,
            'hasParking'   => true,
            'readersCount' => $parking->qrReaders()->count(),
        ]);
    }

    //Se agrego esto 
    // public function updateNotificationToken(int $id, UpdateNotificationTokenRequest $request)
    // {
    //     $response = $this->service->updateNotificationToken($id, $request->validated());
    // }

    public function updateNotificationToken(Request $request)
    {
        $request->validate(['notification_token' => ['required', 'string'],]);

        $user = $request->user();

        $token = $request->input('notification_token');

        \App\Models\User::where('notification_token', $token)
            ->where('id', '!=', $user->id)
            ->update(['notification_token' => null]);

        $user->update([
            'notification_token' => $token,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Notification token actualizado.',
        ]);
    }
}
