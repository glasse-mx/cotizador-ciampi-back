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
        Schema::create('folio_status', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });

        DB::table('folio_status')->insert([
            ['name' => 'cotizacion'],
            ['name' => 'nota_creada'],
            ['name' => 'nota_aprobada'],
            ['name' => 'nota_cancelada']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folio_status');
    }
};
