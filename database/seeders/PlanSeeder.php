<?php
/*
* Nombre de la clase         : PlanSeeder.php
* Descripción de la clase    : Seeder para crear datos iniciales en la tabla de planes.
* Fecha de creación          : 
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 
* Autorizó                   : Angel Davila
* Versión                    : 1.0 
* Fecha de mantenimiento     : 
* Folio de mantenimiento     : 
* Tipo de mantenimiento      : 
* Descripción del mantenimiento : 
* Responsable                : 
* Revisor                    : 
*/
namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::updateOrCreate(['type' => 'parking', 'name' => 'Plan Básico'], [
            'price' => 90.00, 
            'duration_days' => 30, 
            'description' => 'Ideal para estacionamientos pequeños.'
        ]);

        Plan::updateOrCreate(['type' => 'parking', 'name' => 'Plan Profesional'], [
            'price' => 249.00,
            'duration_days' => 30,
            'description' => 'Para medianas empresas con estadísticas y administración avanzada.'
        ]);

        Plan::updateOrCreate(['type' => 'parking', 'name' => 'Plan Empresarial'], [
            'price' => 499.00,
            'duration_days' => 30,
            'description' => 'Soporte prioritario e integraciones avanzadas.'
        ]);

        Plan::updateOrCreate(['type' => 'user', 'name' => 'Plan Básico'], [
            'price' => 0,
            'duration_days' => 30,
            'description' => 'Acciones limitadas para usuarios gratuitos.'
        ]);
        Plan::updateOrCreate(['type' => 'user', 'name' => 'Plan Profesional'], [
            'price' => 49.00,
            'duration_days' => 30,
            'description' => 'Acceso completo por un mes.'
        ]);
        Plan::updateOrCreate(['type' => 'user', 'name' => 'Plan Empresarial'], [
            'price' => 129.00,
            'duration_days' => 30,
            'description' => 'Ahorra con acceso completo por tres meses.'
        ]);
    }
}
