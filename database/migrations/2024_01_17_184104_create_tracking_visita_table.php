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
        Schema::create('tracking_visita', function (Blueprint $table) {
            $table->id('tracking_visita');
            $table->unsignedBigInteger('id_visita');
            $table->string('estado', 20);
            
            // Columnas por default
            $table->unsignedBigInteger('id_usuario_registro')->nullable()->index();
            $table->unsignedBigInteger('id_usuario_modificacion')->nullable()->index();
            $table->timestamps();

            // Columnas relacionadas
            $table->foreign('id_visita')->references('id_visita')->on('visita');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_visita');
    }
};