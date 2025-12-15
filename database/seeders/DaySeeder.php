<?php
/*
* Nombre de la clase         : DaySeeder.php
* Descripción de la clase    : Seeder para crear datos iniciales en la tabla de días.
* Fecha de creación          : 03/11/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 03/11/2025
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

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DaySeeder extends Seeder
{
    public function run(): void
    {
        $days = [
            'Lunes',
            'Martes',
            'Miércoles',
            'Jueves',
            'Viernes',
            'Sábado',
            'Domingo'
        ];

        foreach ($days as $day) 
        {
            DB::table('days')->updateOrInsert(['name' => $day], ['name' => $day]);
        }
    }
}
