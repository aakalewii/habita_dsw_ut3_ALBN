<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('carrito_items', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
            $table->string('nombre')->after('producto_id')->default('');
        });
    }

    public function down(): void
    {
        Schema::table('carrito_items', function (Blueprint $table) {
            $table->dropColumn('nombre');
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
        });
    }
};
