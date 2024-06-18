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
        Schema::create('servicio', function (Blueprint $table) {
            $table->id('id_servicio');
            $table->char('periodo', 2);
            $table->char('anio', 4);
            $table->string('estado', 10)->default('activo');
            $table->double('total', 10, 4);
            $table->string('observacion', 200)->nullable();
            $table->unsignedBigInteger('id_concepto');
            
            // Columnas por default
            $table->unsignedBigInteger('id_usuario_registro')->nullable()->index();
            $table->unsignedBigInteger('id_usuario_modificacion')->nullable()->index();
            $table->timestamps();
            
            // Columnas relacionadas
            $table->foreign('id_concepto')->references('id_concepto')->on('concepto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicio');
    }
};
