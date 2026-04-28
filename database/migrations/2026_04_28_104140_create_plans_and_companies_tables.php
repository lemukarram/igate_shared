<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Basic, Professional, Enterprise
            $table->enum('type', ['provider', 'client']);
            $table->integer('max_services')->default(1);
            $table->integer('max_users')->default(1);
            $table->integer('max_projects')->default(1);
            $table->integer('max_companies')->default(1); // specifically for client plans
            $table->timestamps();
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade'); // The owner
            $table->string('name');
            $table->string('industry')->nullable();
            $table->string('registration_number')->nullable();
            $table->text('about')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
        });

        // Add company_id to projects if it isn't there already. We will add it via an alteration
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
        Schema::dropIfExists('companies');
        Schema::dropIfExists('plans');
    }
};
