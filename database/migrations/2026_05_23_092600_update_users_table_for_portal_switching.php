<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'active_portal')) {
                $table->string('active_portal')->default('client')->after('role');
            }
            if (!Schema::hasColumn('users', 'client_plan_id')) {
                $table->foreignId('client_plan_id')->nullable()->after('active_portal')->constrained('plans')->onDelete('set null');
            }
            if (!Schema::hasColumn('users', 'provider_plan_id')) {
                $table->foreignId('provider_plan_id')->nullable()->after('client_plan_id')->constrained('plans')->onDelete('set null');
            }
        });

        // Migrate existing plan_id data
        DB::table('users')->where('role', 'client')->update([
            'client_plan_id' => DB::raw('plan_id'),
            'active_portal' => 'client'
        ]);

        DB::table('users')->where('role', 'provider')->update([
            'provider_plan_id' => DB::raw('plan_id'),
            'active_portal' => 'provider'
        ]);

        Schema::table('users', function (Blueprint $table) {
            // We keep plan_id for a while to avoid breaking existing code immediately, 
            // but eventually it should be dropped. 
            // For this task, I will keep it but it will be shadowed by the new logic.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['client_plan_id']);
            $table->dropForeign(['provider_plan_id']);
            $table->dropColumn(['active_portal', 'client_plan_id', 'provider_plan_id']);
        });
    }
};
