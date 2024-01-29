<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create('es_MX');

        foreach (range(1, 50) as $index) {
            DB::table('clients')->insert([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'address_street' => $faker->streetName,
                'address_ext' => $faker->buildingNumber,
                'address_int' => $faker->secondaryAddress,
                'address_col' => $faker->citySuffix,
                'address_town' => $faker->city,
                'address_state' => 'México', // Puedes establecer el estado como 'México' si deseas direcciones específicas de México.
                'address_zip' => $faker->postcode,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
