<?php
/*
Nombre de la clase         : ClientTypeApprovalController.php
Descripción de la clase    : Controlador que maneja el estado activo o inactivo de los usuarios desde el 
                             panel de administración.
Fecha de creación          : 08/12/2025
Elaboró                    : Elian Pérez
Fecha de liberación        : 08/12/2025
Autorizó                   : Angel Davila
Versión                    : 1.0
Fecha de mantenimiento     :
Folio de mantenimiento     :
Tipo de mantenimiento      :
Descripción del mantenimiento :
Responsable                :
Revisor                    :
*/
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Laravel\Sanctum\PersonalAccessToken;

class AdminUserStatusController extends Controller
{
    public function __invoke(User $user): RedirectResponse
    {
        if ((int) $user->id_role === 1) 
        {
            return back()->with('swal', [
                'icon'  => 'error',
                'title' => 'Acción no permitida',
                'text'  => 'No puedes bloquear la cuenta de administrador.',
                'confirmButtonColor' => '#494949'
            ]);
        }

        $user->update([ 'is_active' => ! (bool) $user->is_active]);

        if ($user->id_role === 3 || $user->id_role === null)
        {
            if (! $user->is_active)
            {
                PersonalAccessToken::where('tokenable_type', User::class)
                ->where('tokenable_id', $user->id)
                ->delete();
            }
        }
        
        return back()->with('swal', [
            'icon'  => 'success',
            'title' => $user->is_active ? 'Usuario activado' : 'Usuario bloqueado',
            'text'  => $user->is_active ? 'La cuenta se ha activado correctamente.' : 'La cuenta se ha bloqueado correctamente.',
            'confirmButtonColor' => '#494949'
        ]);
    }
}