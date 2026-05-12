<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained('users')->nullOnDelete(); // Assuming providers are in users table, else 'provider_profiles'
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            // $table->foreignId('voucher_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tap_charge_id')->nullable()->index();
            $table->decimal('amount', 10, 3); // Tap supports 3 decimal places for KWD/BHD etc., though standard is usually 2. 10,3 is safer.
            $table->string('currency', 3)->default('SAR');
            $table->enum('status', ['pending', 'authorized', 'captured', 'failed', 'refunded'])->default('pending');
            $table->enum('type', ['subscription', 'service_escrow', 'other'])->default('service_escrow');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
