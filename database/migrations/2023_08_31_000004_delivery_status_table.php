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
        Schema::create('delivery_status', function (Blueprint $table) {
            $table->id();
            $table->string('value');
        });

        DB::table('delivery_status')->insert([
            ['value' => 'Por Despachar'],
            ['value' => 'Salido de almacen'],
            ['value' => 'Entregado']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_status');
    }
};
