<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('commercial_registration')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('iban')->nullable();
            $table->text('bio')->nullable();
            $table->string('logo')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected', 'active', 'inactive'])->default('pending');
            $table->boolean('onboarding_completed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_profiles');
    }
};
