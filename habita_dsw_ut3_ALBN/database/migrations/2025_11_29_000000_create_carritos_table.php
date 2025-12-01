<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('carritos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Para usuarios logueados
            $table->string('sesion_id')->index(); // Para identificar el carrito por pestaña/navegador
            $table->decimal('total', 10, 2)->default(0);
            $table->string('estado')->default('activo'); // activo, completado, abandonado
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carritos');
    }
};
