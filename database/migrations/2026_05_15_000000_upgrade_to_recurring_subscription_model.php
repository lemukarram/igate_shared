<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update Plans table
        Schema::table('plans', function (Blueprint $table) {
            if (Schema::hasColumn('plans', 'price')) {
                $table->renameColumn('price', 'monthly_price');
                $table->decimal('annual_price', 10, 2)->default(0.00)->after('monthly_price');
            } else {
                $table->decimal('monthly_price', 10, 2)->default(0.00);
                $table->decimal('annual_price', 10, 2)->default(0.00)->after('monthly_price');
            }
        });

        // 2. Update Subscriptions table
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->after('provider_id')->constrained('plans')->onDelete('set null');
            $table->timestamp('next_billing_date')->nullable()->after('ends_at');
            $table->string('card_token')->nullable()->after('next_billing_date');
            $table->string('tap_customer_id')->nullable()->after('card_token');
            
            // Adjust billing_cycle if needed
            // It's already a string in the previous migration, we can leave it as string or make it enum
            // $table->string('billing_cycle')->default('monthly')->change();
        });

        // 3. Update Users table for global card storage
        Schema::table('users', function (Blueprint $table) {
            $table->string('tap_customer_id')->nullable()->after('plan_id');
            $table->string('card_token')->nullable()->after('tap_customer_id');
        });

        // 4. Update Invoices table
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('billing_period')->nullable()->after('invoice_number');
        });

        // 5. Update Transactions table
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('billing_cycle')->default('monthly')->after('type');
        });

        // 6. Update ProviderServices table
        Schema::table('provider_services', function (Blueprint $table) {
            if (Schema::hasColumn('provider_services', 'price')) {
                $table->renameColumn('price', 'monthly_price');
                $table->decimal('annual_price', 10, 2)->default(0.00)->after('monthly_price');
            } else {
                $table->decimal('monthly_price', 10, 2)->default(0.00);
                $table->decimal('annual_price', 10, 2)->default(0.00)->after('monthly_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('provider_services', function (Blueprint $table) {
            $table->renameColumn('monthly_price', 'price');
            $table->dropColumn('annual_price');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('billing_cycle');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('billing_period');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['tap_customer_id', 'card_token']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn(['plan_id', 'next_billing_date', 'card_token', 'tap_customer_id']);
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->renameColumn('monthly_price', 'price');
            $table->dropColumn('annual_price');
        });
    }
};
