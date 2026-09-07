<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['user', 'admin'])->default('user')->after('password');
            $table->boolean('is_fictional')->default(false)->after('role');
            $table->enum('kyc_status', ['pending', 'verified', 'rejected'])->default('pending')->after('is_fictional');
            $table->enum('preferred_language', ['fr', 'en', 'ar', 'pt'])->default('fr')->after('kyc_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_fictional', 'kyc_status', 'preferred_language']);
        });
    }
};