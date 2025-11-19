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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('barangay_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rbi_inhabitant_id')->nullable()->constrained('barangay_inhabitants')->nullOnDelete();
            $table->string('purok_zone')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('nationality')->default('Filipino');
            $table->string('religion')->nullable();
            $table->string('occupation')->nullable();
            $table->decimal('monthly_income', 10, 2)->nullable();
            $table->string('educational_attainment')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_number')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->json('id_documents')->nullable();
            $table->json('uploaded_files')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('verification_status')->default('pending_verification');
            $table->text('verification_notes')->nullable();
            $table->json('rbi_match_data')->nullable();
            $table->boolean('is_household_head')->default(false);
            $table->foreignId('household_head_id')->nullable()->constrained('resident_profiles')->nullOnDelete();
            $table->date('residency_since')->nullable();
            $table->enum('residency_type', ['permanent', 'temporary', 'transient'])->default('permanent');
            $table->boolean('is_registered_voter')->default(false);
            $table->string('precinct_number')->nullable();
            $table->boolean('is_pwd')->default(false);
            $table->string('pwd_id_number')->nullable();
            $table->boolean('is_senior_citizen')->default(false);
            $table->boolean('is_solo_parent')->default(false);
            $table->boolean('is_4ps_beneficiary')->default(false);
            $table->timestamps();
            
            $table->index(['barangay_id', 'is_verified']);
            $table->index('is_household_head');
            $table->index('rbi_inhabitant_id');
            $table->index('verification_status');
            $table->index('residency_since');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resident_profiles');
    }
};
