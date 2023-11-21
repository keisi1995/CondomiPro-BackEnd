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
        Schema::create('comprobante', function (Blueprint $table) {
            $table->id('id_comprobante');
            $table->string('nro_comprobante', 20);
            $table->double('total', 10, 4);
            $table->string('estado', 50);
            $table->string('observacion', 200);
            $table->unsignedBigInteger('id_tipo_comprobante');
            
            // Columnas por default
            $table->unsignedBigInteger('id_user_created')->nullable()->index();
            $table->unsignedBigInteger('id_user_updated')->nullable()->index();
            $table->unsignedBigInteger('id_user_deleted')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            // Columnas relacionadas
            $table->foreign('id_tipo_comprobante')->references('id_tipo_comprobante')->on('tipo_comprobante');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comprobante');
    }
};
