<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['deposit', 'withdrawal', 'adjustment']);
            $table->decimal('amount', 18, 2);
            $table->decimal('balance_after', 18, 2);
            $table->foreignId('performed_by')->constrained('users')->cascadeOnDelete(); // l'admin qui a effectué l'opération
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};