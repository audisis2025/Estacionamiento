<?php
/*
* Nombre de la clase         : ClientTypeApprovalController.php
* Descripción de la clase    : Controlador que maneja la aprobación de tipos de cliente para el usuario autenticado.
* Fecha de creación          : 05/11/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 06/11/2025
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

use App\Models\UserClientType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClientTypeApprovalController extends Controller
{
	public function index(): View
	{
		$parking = Auth::user()->parking;

		$pending = $parking->userClientTypes()
			->with(['user:id,name,email,phone_number','clientType:id,type_name,discount_type,amount,id_parking'])
			->where('approval', 0)
			->orderByDesc('id')
			->get();

		$approved = $parking->userClientTypes()
			->with(['user:id,name,email,phone_number','clientType:id,type_name,discount_type,amount,id_parking'])
			->where('approval', 1)
			->orderByDesc('id')
			->limit(30)
			->get();

		return view('user.client_approvals.index', compact('pending', 'approved'));
	}

	public function approve(Request $request, UserClientType $userClientType): RedirectResponse
	{
		$this->ensureOwnership($userClientType);

		$data = $request->validate([
			'expiration_date' => [
				'required',
				'date',
				'after:today'
			]
		]);

		$userClientType->update(['approval' => 1, 'expiration_date' => $data['expiration_date']]);

		return back()->with('swal', [
			'icon'  => 'success',
			'title' => 'Solicitud aprobada',
			'text'  => 'El usuario queda habilitado hasta ' . $data['expiration_date'] . '.'
		]);
	}

	public function reject(UserClientType $userClientType): RedirectResponse
	{
		$this->ensureOwnership($userClientType);

		$userClientType->delete();

		return back()->with('swal', [
			'icon'  => 'success',
			'title' => 'Solicitud rechazada',
			'text'  => 'Se eliminó la solicitud.'
		]);
	}

	private function ensureOwnership(UserClientType $userClientType): void
	{
		$parking = Auth::user()->parking;

		abort_unless(
			$parking && $userClientType->clientType && $userClientType->clientType->id_parking === $parking->id,
			403,
			'No autorizado.'
		);
	}
}
