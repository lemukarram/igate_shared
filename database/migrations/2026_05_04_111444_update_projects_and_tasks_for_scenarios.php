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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
            // Using string for status to allow more flexible states beyond the original enum
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('client_notified_at')->nullable();
            $table->timestamp('escrow_released_at')->nullable();
            $table->text('dispute_reason')->nullable();
            $table->text('termination_reason')->nullable();
            $table->boolean('provider_marked_complete')->default(false);
            $table->boolean('client_approved')->default(false);
            $table->boolean('mutual_cancellation_requested')->default(false);
            $table->string('cancellation_requested_by')->nullable(); // 'client' or 'provider'
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'completed_at',
                'client_notified_at',
                'escrow_released_at',
                'dispute_reason',
                'termination_reason',
                'provider_marked_complete',
                'client_approved',
                'mutual_cancellation_requested',
                'cancellation_requested_by'
            ]);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['is_verified', 'verified_at']);
        });
    }
};
