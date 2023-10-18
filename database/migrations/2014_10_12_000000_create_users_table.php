<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->string('signature')->nullable();
            $table->unsignedBigInteger('user_type');
            $table->rememberToken();
            $table->timestamps();

            // $table->foreign('user_type')->references('id')->on('user_type');
        });

        DB::table('users')->insert([
            'name' => 'Ciampi Website',
            'email' => 'sales@ciampi.com.mx',
            'phone' => '5555555555',
            'password' => bcrypt('12345678'),
            'user_type' => 1,
            'avatar' => 'default.png',
        ]);

        DB::table('users')->insert([
            'name' => 'Luis Enrique Espinosa',
            'email' => 'luis@ciampi.com.mx',
            'phone' => '5539355290',
            'password' => bcrypt('Tata1535!'),
            'user_type' => 5,
            'avatar' => 'default.png',
        ]);

        DB::table('users')->insert([
            'name' => 'Jose Miguel Espinosa',
            'email' => 'direccion@freddo.com.mx',
            'phone' => '5540706946',
            'password' => bcrypt('CiampiMickey2023'),
            'user_type' => 4,
            'avatar' => 'default.png',
        ]);

        DB::table('users')->insert([
            'name' => 'Gabriela Marin Espinosa',
            'email' => 'gabrielamarin@ciampi.com.mx',
            'phone' => '5554385780',
            'password' => bcrypt('CiampiGabriela2023'),
            'user_type' => 4,
            'avatar' => 'default.png',
        ]);

        DB::table('users')->insert([
            'name' => 'Blanca Castañeda',
            'email' => 'blanca@glasse.com.mx',
            'phone' => '5520719520',
            'password' => bcrypt('CiampiBlanca2023'),
            'user_type' => 3,
            'avatar' => 'default.png',
        ]);

        DB::table('users')->insert([
            'name' => 'Samuel Moncada Ávila',
            'email' => 'samuel@ciampi.com.mx',
            'phone' => '5511982563',
            'password' => bcrypt('CiampiSamuel2023'),
            'user_type' => 2,
            'avatar' => 'default.png',
        ]);

        DB::table('users')->insert([
            'name' => 'Marily Lopez',
            'email' => 'marily@ciampi.com.mx',
            'phone' => '5545759700',
            'password' => bcrypt('CiampiMarily2023'),
            'user_type' => 1,
            'avatar' => 'default.png',
        ]);

        DB::table('users')->insert([
            'name' => 'Antonio Mandujano',
            'email' => 'antonio@ciampi.com.mx',
            'phone' => '5549512182',
            'password' => bcrypt('CiampiAntonio2023'),
            'user_type' => 1,
            'avatar' => 'default.png',
        ]);

        DB::table('users')->insert([
            'name' => 'Maria Freire',
            'email' => 'freiremaria@ciampi.com.mx',
            'phone' => '5560922083',
            'password' => bcrypt('CiampiMafreire2023'),
            'user_type' => 1,
            'avatar' => 'default.png',
        ]);

        DB::table('users')->insert([
            'name' => 'Harry Bazán',
            'email' => 'harry@ciampi.com.mx',
            'phone' => '5624334864',
            'password' => bcrypt('CiampiHarry2023'),
            'user_type' => 1,
            'avatar' => 'default.png',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
