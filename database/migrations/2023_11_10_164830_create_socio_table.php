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
        Schema::create('socio', function (Blueprint $table) {
            $table->id('id_socio');
            $table->unsignedBigInteger('id_usuario')->unique();
            $table->date('fecha_baja')->nullable();
            
            // Columnas por default
            $table->unsignedBigInteger('id_usuario_registro')->nullable()->index();
            $table->unsignedBigInteger('id_usuario_modificacion')->nullable()->index();
            $table->timestamps();

            // Columnas relacionadas
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('socio');
    }
};
