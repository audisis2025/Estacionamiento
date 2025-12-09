<?php
/*
* Nombre de la clase         : ClientTypeApprovalController.php
* Descripción de la clase    : Controlador que maneja la aprobación de tipos de cliente para el usuario autenticado.
* Fecha de creación          : 05/11/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 06/11/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.1
* Fecha de mantenimiento     : 09/12/2025
* Folio de mantenimiento     : 
* Tipo de mantenimiento      : Correctivo
* Descripción del mantenimiento : Corrección de race condition en eliminación
* Responsable                : Elian Pérez
* Revisor                    : Angel Davila
*/

namespace App\Http\Controllers;

use App\Models\UserClientType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClientTypeApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $parking = Auth::user()->parking;

        $phone = trim($request->input('phone', ''));

        $relations = ['user:id,name,email,phone_number', 'clientType:id,type_name,discount_type,amount,id_parking'];

        $pendingQuery = $parking->userClientTypes()
            ->with($relations)
            ->where('approval', 0);

        $approvedQuery = $parking->userClientTypes()
            ->with($relations)
            ->where('approval', 1);

        if ($phone !== '') {
            $filterUser = function ($q) use ($phone) {
                $q->where('phone_number', 'like', "%{$phone}%")
                  ->orWhere('name', 'like', "%{$phone}%")
                  ->orWhere('email', 'like', "%{$phone}%");
            };

            $pendingQuery->whereHas('user', $filterUser);
            $approvedQuery->whereHas('user', $filterUser);
        }

        $pending = $pendingQuery
            ->orderByDesc('id')
            ->get();

        $approved = $approvedQuery
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        return view('user.client_approvals.index', [
            'pending'  => $pending,
            'approved' => $approved,
            'phone'    => $phone
        ]);
    }

    public function approve(Request $request, $id): RedirectResponse
    {
        $parking = Auth::user()->parking;
        
        // Buscar el registro - evita 404 automático
        $userClientType = UserClientType::with('clientType')->find($id);
        
        if (!$userClientType) {
            return back()->with('swal', [
                'icon'  => 'warning',
                'title' => 'No encontrado',
                'text'  => 'La solicitud ya no existe o fue eliminada.',
                'confirmButtonColor' => '#494949'
            ]);
        }
        
        // Verificar propiedad
        if (!$parking || !$userClientType->clientType || $userClientType->clientType->id_parking !== $parking->id) {
            return back()->with('swal', [
                'icon'  => 'error',
                'title' => 'No autorizado',
                'text'  => 'No tienes permiso para aprobar esta solicitud.',
                'confirmButtonColor' => '#494949'
            ]);
        }

        $userClientType->update(['approval' => 1]);

        return back()->with('swal', [
            'icon'  => 'success',
            'title' => 'Solicitud aprobada',
            'text'  => 'El usuario ahora tiene el descuento activo hasta que lo canceles.',
            'confirmButtonColor' => '#494949'
        ]);
    }

    public function reject($id): RedirectResponse
    {
        $parking = Auth::user()->parking;
        
        // Buscar el registro - devuelve null si no existe (evita el 404 automático)
        $userClientType = UserClientType::with('clientType')->find($id);
        
        // Verificar si existe
        if (!$userClientType) {
            return back()->with('swal', [
                'icon'  => 'warning',
                'title' => 'Ya eliminado',
                'text'  => 'La solicitud ya fue eliminada previamente.',
                'confirmButtonColor' => '#494949'
            ]);
        }
        
        // Verificar propiedad
        if (!$parking || !$userClientType->clientType || $userClientType->clientType->id_parking !== $parking->id) {
            return back()->with('swal', [
                'icon'  => 'error',
                'title' => 'No autorizado',
                'text'  => 'No tienes permiso para eliminar esta solicitud.',
                'confirmButtonColor' => '#494949'
            ]);
        }
        
        // Eliminar
        $userClientType->delete();
        
        return back()->with('swal', [
            'icon'  => 'success',
            'title' => 'Solicitud eliminada',
            'text'  => 'Se eliminó el beneficio para este usuario.',
            'confirmButtonColor' => '#494949'
        ]);
    }
}