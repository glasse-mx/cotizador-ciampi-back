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
            'name' => 'Jose Miguel Espinosa',
            'email' => 'direccion@freddo.com.mx',
            'phone' => '5512345678',
            'password' => bcrypt('pass123'),
            'user_type' => 4,
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
