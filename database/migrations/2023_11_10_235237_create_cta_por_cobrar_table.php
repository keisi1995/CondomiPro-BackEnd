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
        Schema::create('cta_por_cobrar', function (Blueprint $table) {
            $table->id('id_cta_por_cobrar');
            $table->double('total', 10, 4);
            $table->double('insoluto', 10, 4);
            $table->double('intereses', 10, 4);
            $table->double('descuento', 10, 4);
            $table->string('estado', 100);
            $table->string('observacion', 200);
            $table->unsignedBigInteger('id_servicio')->nullable()->index();
            $table->unsignedBigInteger('id_declaracion_jurada')->nullable()->index();

            // Columnas por default
            $table->unsignedBigInteger('id_user_created')->nullable()->index();
            $table->unsignedBigInteger('id_user_updated')->nullable()->index();
            $table->unsignedBigInteger('id_user_deleted')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            // Columnas relacionadas
            $table->foreign('id_servicio')->references('id_servicio')->on('servicio');
            $table->foreign('id_declaracion_jurada')->references('id_declaracion_jurada')->on('declaracion_jurada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cta_por_cobrar');
    }
};
