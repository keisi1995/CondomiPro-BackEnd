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
        Schema::create('visita', function (Blueprint $table) {
            $table->id('id_visita');
            $table->datetime('fecha_hora_visita');
            $table->boolean('flag_movilidad')->default(false);
            $table->text('observacion')->nullable();
            $table->string('codigo_qr', '20')->nullable();
            $table->string('placa_vehiculo', '10')->nullable();

            $table->unsignedBigInteger('id_usuario_visita');
            $table->unsignedBigInteger('id_usuario_seguridad')->nullable();
            $table->unsignedBigInteger('id_visitante');
            $table->unsignedBigInteger('id_propiedad');
            $table->unsignedBigInteger('id_motivo');

        // Columnas por default
            $table->unsignedBigInteger('id_usuario_registro')->nullable()->index();
            $table->unsignedBigInteger('id_usuario_modificacion')->nullable()->index();
            $table->timestamps();// crea create_at y tambien update_at

        // Columnas relacionadas
            $table->foreign('id_usuario_visita')->references('id_usuario')->on('usuario');
            $table->foreign('id_usuario_seguridad')->references('id_usuario')->on('usuario');
            $table->foreign('id_visitante')->references('id_visitante')->on('visitante');
            $table->foreign('id_propiedad')->references('id_propiedad')->on('propiedad');            
            $table->foreign('id_motivo')->references('id_motivo')->on('motivo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visita');
    }
};
