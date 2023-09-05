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
            $table->timestamps();
            $table->unsignedBigInteger('id_user'); // clave foranea del vendedor
            $table->string('pdv'); // Este dato viene directo del FrontEnd
            $table->unsignedBigInteger('id_client'); // Clave foranea del cliente
            $table->json('productos'); //JSON Generado en la app de cliente
            $table->json('descuentos')->nullable();
            $table->unsignedBigInteger('folio_status_id'); // tipo de Folio => cotizacion, nota de venta o cancelada
            $table->unsignedBigInteger('approval_status_id')->nullable(); // Tipo de firma 
            $table->unsignedBigInteger('folio_cotizacion_id'); // Generado antes de proceder a guardar la orden
            $table->unsignedBigInteger('folio_nota_venta_id')->nullable();
            $table->unsignedBigInteger('folio_nota_cancelada_id')->nullable();
            $table->unsignedBigInteger('delivery_status_id')->nullable();  // Estado de almacen (por despachar o entregado)
            $table->float('subtotal_productos');
            $table->float('subtotal_promos')->nullable();
            $table->json('detalle_anticipo')->nullable();
            $table->json('detalles_pago');
            $table->float('total');

            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_client')->references('id')->on('clients');
            $table->foreign('folio_cotizacion_id')->references('id')->on('folios_cotizaciones');
            $table->foreign('folio_nota_venta_id')->references('id')->on('folios_notas_venta');
            $table->foreign('folio_nota_cancelada_id')->references('id')->on('folios_notas_canceladas');
            $table->foreign('approval_status_id')->references('id')->on('approval_status');
            $table->foreign('folio_status_id')->references('id')->on('folio_status');
            $table->foreign('delivery_status_id')->references('id')->on('delivery_status');
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
