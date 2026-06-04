<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('categoria', 100);
            $table->string('marca', 100)->nullable();
            $table->string('sku', 80)->nullable()->unique();
            $table->string('unidad', 40)->default('pieza');
            $table->unsignedInteger('existencia')->default(0);
            $table->unsignedInteger('stock_minimo')->default(0);
            $table->decimal('precio_compra', 10, 2)->default(0);
            $table->decimal('precio_venta', 10, 2)->default(0);
            $table->string('proveedor')->nullable();
            $table->string('ubicacion')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('nombre');
            $table->index('categoria');
            $table->index('marca');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
