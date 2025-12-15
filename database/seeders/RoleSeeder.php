<?php
/*
* Nombre de la clase         : RoleSeeder.php
* Descripción de la clase    : Seeder para crear datos iniciales en la tabla de roles.
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

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->upsert([
                ['id' => 1, 'name' => 'admin'],
                ['id' => 2, 'name' => 'adminEstacionamiento'],
                ['id' => 3, 'name' => 'usuario'],
            ], 
            ['id'],
            ['name']
        );
    }
}
