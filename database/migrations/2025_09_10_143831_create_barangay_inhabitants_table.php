<?php
// FILE: database/migrations/YYYY_MM_DD_000002_create_barangay_inhabitants_table_complete.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangay_inhabitants', function (Blueprint $table) {
            $table->id();
            
            // Barangay Association & Registry
            $table->foreignId('barangay_id')->constrained()->onDelete('cascade');
            $table->string('registry_number')->unique(); // Auto-generated: RBI-BGY001-2024-00001
            
            // NAME (from RBI form)
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('ext')->nullable(); // Jr., Sr., III, etc.
            
            // ADDRESS
            $table->string('house_number')->nullable();
            $table->string('zone_sitio');
            
            // BIRTH INFORMATION
            $table->string('place_of_birth');
            $table->date('date_of_birth');
            $table->integer('age')->virtualAs('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE())');
            
            // DEMOGRAPHIC INFO
            $table->enum('sex', ['Male', 'Female']);
            $table->string('civil_status'); // Single, Married, Widowed, etc.
            $table->string('citizenship')->default('Filipino');
            $table->string('occupation')->nullable();
            $table->string('educational_attainment')->nullable();
            
            // CONTACT
            $table->string('contact_number')->nullable();
            
            // EMERGENCY CONTACT
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_number')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            
            // HOUSEHOLD INFORMATION
            $table->string('household_number')->nullable();
            $table->boolean('is_household_head')->default(false);
            
            // RESIDENCY
            $table->date('residency_since')->nullable();
            $table->timestamp('registered_at');
            
            // REGISTRATION & VERIFICATION
            $table->foreignId('registered_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            
            // LINK TO ONLINE ACCOUNT (Critical for our system)
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->onDelete('set null');
            
            // STATUS & ELIGIBILITY
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['active', 'moved_out', 'deceased', 'inactive'])->default('active');
            
            // ELIGIBILITY CHECKS (for document requests)
            $table->boolean('has_violations')->default(false);
            $table->text('violation_details')->nullable();
            $table->boolean('has_unpaid_dues')->default(false);
            $table->text('unpaid_dues_details')->nullable();
            $table->boolean('attends_assembly')->default(true);
            $table->date('last_assembly_attended')->nullable();
            
            // MEDIA
            $table->string('photo_path')->nullable();
            
            // NOTES
            $table->text('remarks')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['barangay_id', 'status']);
            $table->index(['last_name', 'first_name']);
            $table->index(['registry_number']);
            $table->index(['household_number']);
            $table->index(['user_id']); // Important for linking
            $table->index(['date_of_birth']); // For RBI search
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangay_inhabitants');
    }
};