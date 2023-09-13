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
        Schema::create('payment_type', function (Blueprint $table) {
            $table->id();
            $table->string('value');
        });

        DB::table('payment_type')->insert([
            ['value' => 'Efectivo: CDMX Sin referencia'],
            ['value' => 'Depósito en Efectivo'],
            ['value' => 'Transferencia Bancaria'],
            ['value' => 'Tarjeta de Crédito'],
            ['value' => 'Tarjeta de Débito'],
            ['value' => 'Paypal'],
            ['value' => 'OpenPay'],
            ['value' => 'Stripe']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_type');
    }
};
