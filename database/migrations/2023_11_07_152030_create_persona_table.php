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
        Schema::create('persona', function (Blueprint $table) {
            $table->id('id_persona');
            $table->string('nombres', 50);
            $table->string('apellidos', 50)->nullable();
            $table->string('direccion', 100)->nullable();
            $table->string('nro_documento', 20);
            $table->string('telefono', 20);
            $table->string('correo', 50)->nullable();
            $table->string('id_distrito', 6);
            $table->unsignedBigInteger('id_tipo_documento');
            $table->unsignedBigInteger('id_tipo_persona');

            // Columnas por default
            $table->unsignedBigInteger('id_usuario_registro')->nullable()->index();
            $table->unsignedBigInteger('id_usuario_modificacion')->nullable()->index();
            $table->timestamps();
            // $table->softDeletes();
                   
            // Columnas relacionadas
            $table->foreign('id_distrito')->references('id_distrito')->on('distrito');
            $table->foreign('id_tipo_documento')->references('id_tipo_documento')->on('tipo_documento');
            $table->foreign('id_tipo_persona')->references('id_tipo_persona')->on('tipo_persona');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persona');
    }
};
