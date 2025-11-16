<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resident_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Basic personal info
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('mother_maiden_name')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();

            // Address info
            $table->string('house_number')->nullable();
            $table->string('street')->nullable();
            $table->string('purok')->nullable();
            $table->foreignId('barangay_id')->nullable()->constrained()->nullOnDelete(); // New relationship
            $table->string('municipality')->default('Sablayan');
            $table->string('province')->default('Occidental Mindoro');
            $table->string('zipcode', 10)->nullable();

            // Identification
            $table->string('valid_id_type')->nullable();
            $table->string('valid_id_number')->nullable();
            $table->string('valid_id_path')->nullable();
            $table->string('proof_of_residency_path')->nullable();

            // Additional info
            $table->string('occupation')->nullable();
            $table->string('civil_status', 20)->nullable();
            $table->string('nationality')->default('Filipino');

            $table->timestamps();

            // Indexes
            $table->index(['last_name', 'first_name']);
            $table->index('valid_id_number');
        });
    }

    public function down(): void
    {
        Schema::table('resident_profiles', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
        });
        
        Schema::dropIfExists('resident_profiles');
    }
};