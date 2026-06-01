<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')
                ->constrained('clientes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('vehiculo_id')
                ->nullable()
                ->constrained('vehiculos')
                ->nullOnDelete();

            $table->string('servicio');
            $table->date('fecha');
            $table->time('hora');

            $table->enum('estado', [
                'pendiente',
                'confirmada',
                'cancelada',
                'atendida'
            ])->default('pendiente');

            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->index(['fecha', 'hora']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};