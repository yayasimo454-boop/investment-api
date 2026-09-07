<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 18, 8);
            $table->timestamp('recorded_at')->useCurrent();

            $table->index(['asset_id', 'recorded_at']); // requêtes fréquentes : dernier prix d'un actif, historique
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_prices');
    }
};