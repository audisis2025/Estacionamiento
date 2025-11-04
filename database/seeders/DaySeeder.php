<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days = [
            'Lunes',
            'Martes',
            'Miércoles', // Nota: Corregí la tilde
            'Jueves',
            'Viernes',
            'Sábado',    // Nota: Corregí la tilde
            'Domingo'
        ];

        foreach ($days as $day) {
            DB::table('days')->updateOrInsert(
                ['name' => $day],
                ['name' => $day]
            );
        }
    }
}
