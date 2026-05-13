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
        Schema::table('payments', function (Blueprint $table) {
            // Make project_id nullable to allow plan-only payments
            $table->foreignId('project_id')->nullable()->change();
            
            // Add plan_id to track plan subscription payments
            $table->foreignId('plan_id')->nullable()->after('project_id')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn('plan_id');
            $table->foreignId('project_id')->nullable(false)->change();
        });
    }
};
