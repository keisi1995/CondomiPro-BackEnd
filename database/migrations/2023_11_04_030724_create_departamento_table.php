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
        Schema::create('departamento', function (Blueprint $table) {
            $table->string('id_departamento', 2)->primary();
            $table->string('descripcion', 50);
            
            // Columnas por default
            $table->unsignedBigInteger('id_user_created')->nullable()->index();
            $table->unsignedBigInteger('id_user_updated')->nullable()->index();
            $table->unsignedBigInteger('id_user_deleted')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departamento');
    }
};
