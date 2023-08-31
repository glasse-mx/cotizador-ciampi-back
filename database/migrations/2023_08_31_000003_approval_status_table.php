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
        Schema::create('approval_status', function (Blueprint $table) {
            $table->id();
            $table->string('value');
        });

        DB::table('approval_status')->insert([
            ['value' => 'creada'],
            ['value' => 'Aprobada por PDV'],
            ['value' => 'Aprobada por Direccion'],
            ['value' => 'Aprbada por tesoreria'],
            ['value' => 'Aprobada por Direccion']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_status');
    }
};
