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
        Schema::create('detalle_comprobante', function (Blueprint $table) {
            $table->id('id_detalle_comprobante');
            $table->double('subtotal', 10, 4);
            $table->double('descuento', 10, 4);
            $table->double('total', 10, 4);
            $table->unsignedBigInteger('id_comprobante');
            $table->unsignedBigInteger('id_cta_por_cobrar');

            // Columnas por default
            $table->unsignedBigInteger('id_usuario_registro')->nullable()->index();
            $table->unsignedBigInteger('id_usuario_modificacion')->nullable()->index();
            $table->timestamps();

            // Columnas relacionadas
            $table->foreign('id_comprobante')->references('id_comprobante')->on('comprobante');
            $table->foreign('id_cta_por_cobrar')->references('id_cta_por_cobrar')->on('cta_por_cobrar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_comprobante');
    }
};
