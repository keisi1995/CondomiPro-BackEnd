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
        Schema::create('distrito', function (Blueprint $table) {
            $table->string('id_distrito', 6)->primary();
            $table->string('descripcion', 50);
            $table->string('id_provincia', 4);
            $table->string('id_departamento', 2);                         
            
            // Columnas relacionadas
            $table->foreign('id_provincia')->references('id_provincia')->on('provincia');
            $table->foreign('id_departamento')->references('id_departamento')->on('departamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distrito');
    }
};
