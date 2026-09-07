<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_holdings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 18, 8)->default(0);
            $table->decimal('average_buy_price', 18, 8)->default(0);
            $table->timestamps();

            $table->unique(['portfolio_id', 'asset_id']); // une seule ligne par actif dans un portefeuille
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_holdings');
    }
};