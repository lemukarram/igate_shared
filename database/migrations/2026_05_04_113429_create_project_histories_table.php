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
        Schema::create('project_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // e.g., 'status_change', 'complete_request', 'cancellation_confirmed'
            $table->text('description');
            $table->json('metadata')->nullable(); // Store old/new values if needed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_histories');
    }
};
