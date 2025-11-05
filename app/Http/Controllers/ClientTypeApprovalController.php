<?php

namespace App\Http\Controllers;

use App\Models\UserClientType;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;

class ClientTypeApprovalController extends Controller
{
    public function index()
    {
        $parking = Auth::user()->parking;

        $pending = $parking->userClientTypes()
            ->with(['user:id,name,email,phone_number', 'clientType:id,typename,discount_type,amount,id_parking'])
            ->where('approval', 0)
            ->orderByDesc('id')
            ->get();

        $approved = $parking->userClientTypes()
            ->with(['user:id,name,email,phone_number', 'clientType:id,typename,discount_type,amount,id_parking'])
            ->where('approval', 1)
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        return view('user.client_approvals.index', compact('pending', 'approved'));
    }

    public function approve(HttpRequest $request, UserClientType $userClientType)
    {
        $this->ensureOwnership($userClientType);

        $data = $request->validate([
            'expiration_date' => ['required', 'date', 'after:today'],
        ]);

        $userClientType->update([
            'approval'        => 1,
            'expiration_date' => $data['expiration_date'],
        ]);

        return back()->with('swal', [
            'icon'  => 'success',
            'title' => 'Solicitud aprobada',
            'text'  => 'El usuario queda habilitado hasta ' . $data['expiration_date'] . '.',
        ]);
    }

    public function reject(UserClientType $userClientType)
    {
        $this->ensureOwnership($userClientType);

        $userClientType->delete();

        return back()->with('swal', [
            'icon'  => 'success',
            'title' => 'Solicitud rechazada',
            'text'  => 'Se eliminó la solicitud.',
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
