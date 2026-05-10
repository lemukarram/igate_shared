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
        // 1. Hierarchy Support in Users
        // This allows staff/managers to be linked to a 'Main' Client or Provider account.
        if (!Schema::hasColumn('users', 'parent_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('parent_id')->nullable()->constrained('users')->onDelete('cascade');
            });
        }

        // 2. Subscriptions Table
        // Tracks client subscriptions to specific iGate standardized services.
        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
                $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
                $table->foreignId('provider_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('plan_name')->nullable(); // e.g., Basic, Professional, Enterprise
                $table->string('billing_cycle')->default('monthly'); // monthly, quarterly, annually
                $table->enum('status', ['active', 'cancelled', 'expired', 'pending'])->default('active');
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->timestamp('canceled_at')->nullable();
                $table->timestamps();
            });
        }

        // 3. Escrow Ledger
        // Dedicated table for tracking escrow movements as requested for the EscrowLedgerResource.
        if (!Schema::hasTable('escrow_ledgers')) {
            Schema::create('escrow_ledgers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained()->onDelete('cascade');
                $table->decimal('amount', 10, 2);
                $table->enum('type', ['credit', 'debit']); // credit = funds into escrow, debit = funds out
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        // 4. Platform Settings (Simple table if not using spatie/laravel-settings database driver)
        if (!Schema::hasTable('platform_settings')) {
            Schema::create('platform_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
        Schema::dropIfExists('escrow_ledgers');
        Schema::dropIfExists('subscriptions');
        if (Schema::hasColumn('users', 'parent_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            });
        }
    }
};
