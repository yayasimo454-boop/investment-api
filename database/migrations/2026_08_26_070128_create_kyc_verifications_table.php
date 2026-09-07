<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kyc_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('document_type');       // ex: carte d'identité, passeport
            $table->string('document_number');
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kyc_verifications');
    }
};