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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id('codigo');
            $table->string('nombre',100);
            $table->string('apellido',100);
            $table->string('telefono',100);
            $table->bigInteger('user')->unsigned();//para la relacion de la foreing key
            $table->foreign('user')->references('id')->on('users');//llave foranea 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
