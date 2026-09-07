<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();      // ex: XAF, USD, BTC
            $table->string('name');                     // ex: Franc CFA (BEAC)
            $table->string('symbol', 10);                // ex: FCFA, $, €
            $table->decimal('exchange_rate_to_pivot', 20, 8)->default(1); // taux vers une devise pivot (ex: USD)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};