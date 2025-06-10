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
        Schema::create('detalleorden', function (Blueprint $table) {
            $table->id('codigo');
            $table->unsignedBigInteger('orden_id');
            $table->unsignedBigInteger('menu_id');
            $table->integer('cantidad');
            $table->decimal('subtotal', 8, 2);
            $table->timestamps();

            $table->foreign('orden_id')->references('codigo')->on('ordenes')->onDelete('cascade');
            $table->foreign('menu_id')->references('codigo')->on('menu')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalleorden');
    }
};
