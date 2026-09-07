<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('symbol')->unique();     // ex: SONARA, BTC, EURXAF
            $table->string('name');                   // ex: Société Nationale de Raffinage
            $table->enum('type', ['action', 'crypto', 'forex', 'obligation']);
            $table->string('sector')->nullable();     // ex: énergie, banque, technologie
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};