<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user'); // clave foranea del vendedor
            $table->unsignedBigInteger('id_client'); // Clave foranea del cliente
            $table->json('productos');
            $table->unsignedBigInteger('folio_status_id');
            $table->unsignedBigInteger('approval_status_id');
            $table->timestamps();

            $table->foreign('folio_status_id')->references('id')->on('folio_status');
            $table->foreign('approval_status_id')->references('id')->on('approval_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
