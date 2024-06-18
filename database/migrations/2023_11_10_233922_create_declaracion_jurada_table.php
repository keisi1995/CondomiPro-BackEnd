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
        Schema::create('declaracion_jurada', function (Blueprint $table) {
            $table->id('id_declaracion_jurada');
            $table->string('descripcion', 100);
            $table->double('porcentaje_acciones', 6, 2);
            $table->string('estado', 10)->default('activo');
            $table->string('observacion', 200)->nullable();
            $table->unsignedBigInteger('id_propiedad');
            $table->unsignedBigInteger('id_socio');
            $table->unsignedBigInteger('id_parentesco');
            $table->unsignedBigInteger('id_persona')->nullable();
                        
            // Columnas por default
            $table->unsignedBigInteger('id_usuario_registro')->nullable()->index();
            $table->unsignedBigInteger('id_usuario_modificacion')->nullable()->index();
            $table->timestamps();

            // Columnas relacionadas
            $table->foreign('id_propiedad')->references('id_propiedad')->on('propiedad');
            $table->foreign('id_socio')->references('id_socio')->on('socio');
            $table->foreign('id_parentesco')->references('id_parentesco')->on('parentesco');
            $table->foreign('id_persona')->references('id_persona')->on('persona');            

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('declaracion_jurada');
    }
};
