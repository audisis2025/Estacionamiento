<?php
/*
* Nombre de la clase         : BillingController.php
* Descripción de la clase    : Controlador que maneja la vista de facturación para el usuario autenticado.
* Fecha de creación          : 06/11/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 06/11/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.0 
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        return view( 'user.billing.index');
    }
}
