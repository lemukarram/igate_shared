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
            $table->boolean('termination_requested')->default(false);
            $table->timestamp('termination_requested_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('last_action_by')->nullable(); // 'client' or 'provider'
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'termination_requested',
                'termination_requested_at',
                'rejection_reason',
                'rejected_at',
                'last_action_by'
            ]);
        });
    }
};
