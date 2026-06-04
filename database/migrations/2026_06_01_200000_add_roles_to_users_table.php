<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('cliente_id')->nullable()->after('id')->constrained('clientes')->nullOnDelete();
            $table->enum('rol', ['admin', 'mecanico', 'cliente'])->default('cliente')->after('cliente_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cliente_id');
            $table->dropColumn('rol');
        });
    }
};
