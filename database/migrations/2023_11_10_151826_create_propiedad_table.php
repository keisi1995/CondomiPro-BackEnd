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
        Schema::create('propiedad', function (Blueprint $table) {
            $table->id('id_propiedad');
            $table->string('nro_interior', 20);
            $table->string('area_propiedad', 20);
            $table->string('observacion', 200);
            $table->unsignedBigInteger('id_edificacion');

            // Columnas por default
            $table->unsignedBigInteger('id_user_created')->nullable()->index();
            $table->unsignedBigInteger('id_user_updated')->nullable()->index();
            $table->unsignedBigInteger('id_user_deleted')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            // Columnas relacionadas
            $table->foreign('id_edificacion')->references('id_edificacion')->on('edificacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propiedad');
    }
};
