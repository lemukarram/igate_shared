<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('provider_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->string('priority')->default('normal'); // normal, high, urgent
            // Drop constraint safely if we're on SQLite or MySQL
            // But we might just let it be, or drop the foreign key to make it nullable.
            // Given Laravel 11/SQLite support, dropForeign might have issues, but let's try.
        });
        
        // SQLite trick for modifying column to nullable
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['provider_id']);
            $table->dropForeign(['team_id']);
            $table->dropColumn(['provider_id', 'team_id', 'priority']);
        });
        
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable(false)->change();
        });
    }
};
