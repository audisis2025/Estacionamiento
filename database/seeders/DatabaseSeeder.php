<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        
        $this->call(DaySeeder::class);

        User::factory()->create([
            'name' => 'Elian Perez',
            'email' => 'admgenineral@gmail.com',
            'phone_number' => '7777777777',
            'password' => bcrypt('12345678'),
            'id_role' => 1,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'remember_token' => null,
        ]);

        $this->call(PlanSeeder::class);

        $this->call(TestParkingSeeder::class);
    }
}
