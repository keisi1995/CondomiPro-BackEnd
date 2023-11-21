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
            $table->string('estado', 10);
            $table->double('total', 10, 4);
            $table->string('observacion', 200);
            $table->unsignedBigInteger('id_concepto');
            
            // Columnas por default
            $table->unsignedBigInteger('id_user_created')->nullable()->index();
            $table->unsignedBigInteger('id_user_updated')->nullable()->index();
            $table->unsignedBigInteger('id_user_deleted')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

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
