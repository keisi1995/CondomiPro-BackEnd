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
        Schema::create('concepto', function (Blueprint $table) {
            $table->id('id_concepto');
            $table->string('descripcion', 50)->unique();
            $table->unsignedBigInteger('id_tipo_concepto');
            
            // Columnas por default
            $table->unsignedBigInteger('id_usuario_registro')->nullable()->index();
            $table->unsignedBigInteger('id_usuario_modificacion')->nullable()->index();
            $table->timestamps();

            // Columnas relacionadas
            $table->foreign('id_tipo_concepto')->references('id_tipo_concepto')->on('tipo_concepto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concepto');
    }
};
