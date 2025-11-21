<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestParkingSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = DB::table('users')->insertGetId([
            'name'          => 'Admin Estacionamiento',
            'email'         => 'admin@parking.test',
            'phone_number'  => '5551112222',
            'password'      => Hash::make('12345678'),
            'id_role'       => 2,
            'amount'        => 0,
        ]);

        $parkingId = DB::table('parkings')->insertGetId([
            'id_user'              => $adminId,
            'name'                 => 'Estacionamiento Central',
            'latitude_coordinate'  => 19.2923,
            'longitude_coordinate' => -99.6555,
            'type'                 => 1,
            'price'                => 20,
        ]);

        $days = DB::table('days')->pluck('id')->all();
        foreach ($days as $dayId) {
            DB::table('schedules')->insert([
                'opening_time' => '08:00',
                'closing_time' => '22:00',
                'id_day'       => $dayId,
                'id_parking'   => $parkingId,
            ]);
        }

        $taxistaId = DB::table('client_types')->insertGetId([
            'typename'      => 'Taxista',
            'discount_type' => 0,
            'amount'        => 15,
            'id_parking'    => $parkingId,
        ]);

        $proveedorId = DB::table('client_types')->insertGetId([
            'typename'      => 'Proveedor',
            'discount_type' => 1,
            'amount'        => 10,
            'id_parking'    => $parkingId,
        ]);

        $user1 = DB::table('users')->insertGetId([
            'name'         => 'Juan Taxista',
            'email'        => 'juan@correo.com',
            'phone_number' => '5550001111',
            'password'     => Hash::make('12345678'),
            'amount'       => 50,
        ]);

        $user2 = DB::table('users')->insertGetId([
            'name'         => 'Pedro Proveedor',
            'email'        => 'pedro@correo.com',
            'phone_number' => '5550002222',
            'password'     => Hash::make('12345678'),
            'amount'       => 0,
        ]);

        DB::table('user_client_types')->insert([
            [
                'approval'        => 0,
                'expiration_date' => null,
                'id_user'         => $user2,
                'id_client_type'  => $proveedorId,
            ],
            [
                'approval'        => 1,
                'expiration_date' => Carbon::now()->addDays(30),
                'id_user'         => $user1,
                'id_client_type'  => $taxistaId,
            ],
        ]);
    }
}
