<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //User::factory(1)->create();
        $this->call(RoleSeeder::class);
        
        $this->call(DaySeeder::class);

        User::factory()->create([
            'name' => 'Elian Perez',
            'email' => 'elianperezrom@gmail.com',
            'phone_number' => '7777777777',
            'password' => bcrypt('12345678'),
            'id_role' => 1,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'remember_token' => null,
        ]);

        $this->call(PlanSeeder::class);
    }
}
