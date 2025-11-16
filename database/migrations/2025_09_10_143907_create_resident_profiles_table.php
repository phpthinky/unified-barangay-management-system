<?php
// FILE: database/migrations/YYYY_MM_DD_000003_create_resident_profiles_table_complete.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resident_profiles', function (Blueprint $table) {
            $table->id();
            
            // Core Relationships
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('barangay_id')->constrained()->onDelete('cascade');
            
            // ✅ RBI LINK (Critical for smart verification)
            $table->foreignId('rbi_inhabitant_id')->nullable()
                  ->constrained('barangay_inhabitants')
                  ->onDelete('set null');
            
            // Personal Information
            $table->string('purok_zone')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('nationality')->default('Filipino');
            $table->string('religion')->nullable();
            $table->string('occupation')->nullable();
            $table->decimal('monthly_income', 10, 2)->nullable();
            $table->string('educational_attainment')->nullable();
            
            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_number')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            
            // Identification Documents
            $table->json('id_documents')->nullable();
            $table->json('uploaded_files')->nullable();
            
            // ✅ VERIFICATION STATUS (Enhanced for smart verification)
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('verification_status')->default('pending_verification');
            // Possible values:
            // - 'pending_verification': New account, not verified
            // - 'verified': Fully verified and eligible
            // - 'pending_residency_duration': RBI linked but < 6 months
            // - 'not_in_rbi': Not in RBI registry
            // - 'failed_reverification': Re-verification failed
            // - 'rbi_found_and_linked': Auto-linked during NO path
            
            $table->text('verification_notes')->nullable();
            $table->json('rbi_match_data')->nullable(); // Store RBI search results
            
            // Family Information
            $table->boolean('is_household_head')->default(false);
            $table->foreignId('household_head_id')->nullable()
                  ->constrained('resident_profiles')
                  ->onDelete('set null');
            
            // ✅ RESIDENCY STATUS (For 6-month eligibility check)
            $table->date('residency_since')->nullable();
            $table->enum('residency_type', ['permanent', 'temporary', 'transient'])->default('permanent');
            
            // Voter Registration
            $table->boolean('is_registered_voter')->default(false);
            $table->string('precinct_number')->nullable();
            
            // Special Classifications
            $table->boolean('is_pwd')->default(false);
            $table->string('pwd_id_number')->nullable();
            $table->boolean('is_senior_citizen')->default(false);
            $table->boolean('is_solo_parent')->default(false);
            $table->boolean('is_4ps_beneficiary')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['barangay_id', 'is_verified']);
            $table->index(['is_household_head']);
            $table->index(['rbi_inhabitant_id']); // For RBI linking queries
            $table->index(['verification_status']); // For filtering
            $table->index(['residency_since']); // For eligibility checks
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resident_profiles');
    }
};