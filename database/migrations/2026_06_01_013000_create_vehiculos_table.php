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
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('marca', 100);
            $table->string('modelo', 100);
            $table->unsignedSmallInteger('anio');
            $table->string('placas', 20)->unique();
            $table->unsignedInteger('kilometraje_actual')->default(0);
            $table->enum('tipo_combustible', ['gasolina', 'diesel', 'hibrido', 'electrico', 'gas']);
            $table->enum('estado', ['activo', 'inactivo', 'en_servicio'])->default('activo');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('modelo');
            $table->index('anio');
            $table->index('placas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
