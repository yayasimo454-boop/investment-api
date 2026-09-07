<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_volatility_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('mu', 10, 6)->default(0);                    // tendance (drift)
            $table->decimal('sigma', 10, 6)->default(0.02);              // volatilité
            $table->string('sector')->nullable();
            $table->decimal('market_factor_weight', 5, 4)->default(0.5); // poids du facteur de marché commun
            $table->decimal('sector_factor_weight', 5, 4)->default(0.3); // poids du facteur sectoriel
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_volatility_profiles');
    }
};