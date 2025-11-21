<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->upsert(
            [
                ['id' => 1, 'name' => 'admin'],
                ['id' => 2, 'name' => 'adminEstacionamiento'],
                ['id' => 3, 'name' => 'usuario'],
            ], 
            ['id'],
            ['name']
        );
    }
}
