<?php

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

        foreach ($days as $day) {
            DB::table('days')->updateOrInsert(
                ['name' => $day],
                ['name' => $day]
            );
        }
    }
}
