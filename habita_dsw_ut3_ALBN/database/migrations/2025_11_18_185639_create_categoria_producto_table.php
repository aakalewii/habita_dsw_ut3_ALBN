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
        Schema::create('categoria_producto', function (Blueprint $table) {
            
            $table->unsignedBigInteger('productos_id');
            $table->unsignedBigInteger('categorias_id');

            $table->primary(['productos_id', 'categorias_id']);

            $table->foreign('productos_id')->references('id')->on('productos');
            $table->foreign('categorias_id')->references('id')->on('categorias');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria_producto');
    }
};
