<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
        });

        Schema::table('provider_services', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
        });

        Schema::table('team_members', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('provider_services', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
