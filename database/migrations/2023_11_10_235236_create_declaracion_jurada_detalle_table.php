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
        Schema::create('declaracion_jurada_detalle', function (Blueprint $table) {
            $table->id('id_declaracion_jurada_detalle');
            $table->string('tipo_calculo', 20)->default('A');
            $table->unsignedBigInteger('id_concepto');
            $table->unsignedBigInteger('id_declaracion_jurada');
            
            // Columnas por default
            $table->unsignedBigInteger('id_usuario_registro')->nullable()->index();
            $table->unsignedBigInteger('id_usuario_modificacion')->nullable()->index();
            $table->timestamps();

            // Columnas relacionadas
            $table->foreign('id_concepto')->references('id_concepto')->on('concepto');
            $table->foreign('id_declaracion_jurada')->references('id_declaracion_jurada')->on('declaracion_jurada');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('declaracion_jurada_detalle');
    }
};
