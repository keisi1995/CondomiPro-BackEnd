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
            $table->string('estado', 10);
            $table->string('observacion', 200);
            $table->unsignedBigInteger('id_propiedad');
            $table->unsignedBigInteger('id_socio');
            $table->unsignedBigInteger('id_persona');
            $table->unsignedBigInteger('id_parentesco');
            
            // Columnas por default
            $table->unsignedBigInteger('id_user_created')->nullable()->index();
            $table->unsignedBigInteger('id_user_updated')->nullable()->index();
            $table->unsignedBigInteger('id_user_deleted')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            // Columnas relacionadas
            $table->foreign('id_propiedad')->references('id_propiedad')->on('propiedad');
            $table->foreign('id_socio')->references('id_socio')->on('socio');
            $table->foreign('id_persona')->references('id_persona')->on('persona');
            $table->foreign('id_parentesco')->references('id_parentesco')->on('parentesco');

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
