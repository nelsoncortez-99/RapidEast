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
        Schema::create('menu', function (Blueprint $table) {
            $table->id('codigo');
            $table->string('nombre',100);
            $table->string('descripcion',150);
            $table->double('precio');
            $table->bigInteger('category')->unsigned();//para la relacion de la foreing key
            $table->foreign('category')->references('codigo')->on('categoria');//llave foranea 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
