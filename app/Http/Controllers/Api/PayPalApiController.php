<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PayPalApiController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'amount'  => 'required|numeric|min:0',
        ]);

        $plan = Plan::find($request->plan_id);

        if (!$plan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Plan no encontrado'
            ], 404);
        }

        try {
            DB::transaction(function () use ($user, $plan, $request) {

                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'id_plan'  => $plan->id,
                        'end_date' => Carbon::now()->addDays($plan->duration_days),
                        'updated_at' => now(),
                    ]);

                $admin = DB::table('users')->where('phone_number', '7777777777')->first();

                if ($admin) {
                    $saldoActual = (float) ($admin->amount ?? 0);
                    $nuevoSaldo = $saldoActual + (float) $request->amount;

                    DB::table('users')
                        ->where('id', $admin->id)
                        ->update([
                            'amount' => $nuevoSaldo,
                            'updated_at' => now(),
                        ]);

                    Log::info('💰 Saldo del admin actualizado', [
                        'admin_id' => $admin->id,
                        'saldo_anterior' => $saldoActual,
                        'monto_agregado' => $request->amount,
                        'nuevo_saldo' => $nuevoSaldo,
                    ]);
                } else {
                    Log::warning('⚠️ No se encontró el administrador con phone_number=7777777777');
                }
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Pago PayPal registrado correctamente.',
                'data'    => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                    'amount_added' => $request->amount,
                    'end_date' => Carbon::now()->addDays($plan->duration_days)->toDateString(),
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error en PayPalApiController', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al registrar el pago.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
