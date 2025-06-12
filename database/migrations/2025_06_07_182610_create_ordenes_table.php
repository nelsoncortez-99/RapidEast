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
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id('codigo');
            $table->date('fecha');
            $table->integer('numeromesa');

            $table->bigInteger('client')->unsigned();//para la relacion de la foreing key
            $table->foreign('client')->references('codigo')->on('cliente');//llave foranea 

            $table->bigInteger('empleado')->unsigned();//para la relacion de la foreing key
            $table->foreign('empleado')->references('codigo')->on('empleados');//llave foranea 

            $table->bigInteger('state')->unsigned();//para la relacion de la foreing key
            $table->foreign('state')->references('codigo')->on('estado');//llave foranea 

            $table->bigInteger('mpago')->unsigned();//para la relacion de la foreing key
            $table->foreign('mpago')->references('codigo')->on('metodopago');//llave foranea 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes');
    }
};
