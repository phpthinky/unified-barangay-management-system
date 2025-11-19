<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangay_inhabitants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barangay_id')->constrained()->cascadeOnDelete();
            $table->string('registry_number')->unique();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('ext')->nullable();
            $table->string('house_number')->nullable();
            $table->string('zone_sitio');
            $table->string('place_of_birth');
            $table->date('date_of_birth');
            $table->integer('age')->virtualAs('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE())');
            $table->enum('sex', ['Male', 'Female']);
            $table->string('civil_status');
            $table->string('citizenship')->default('Filipino');
            $table->string('occupation')->nullable();
            $table->string('educational_attainment')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_number')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('household_number')->nullable();
            $table->boolean('is_household_head')->default(false);
            $table->date('residency_since')->nullable();
            $table->enum('residency_type', ['permanent', 'temporary', 'transient'])->default('permanent');
            $table->string('cedula_number')->nullable();
            $table->string('certificate_of_residency_number')->nullable();
            $table->string('proof_of_residency_file')->nullable();
            $table->timestamp('registered_at')->useCurrent()->useCurrentOnUpdate();
            $table->foreignId('registered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['active', 'moved_out', 'deceased', 'inactive'])->default('active');
            $table->boolean('has_violations')->default(false);
            $table->text('violation_details')->nullable();
            $table->boolean('has_unpaid_dues')->default(false);
            $table->text('unpaid_dues_details')->nullable();
            $table->boolean('attends_assembly')->default(true);
            $table->date('last_assembly_attended')->nullable();
            $table->string('photo_path')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['barangay_id', 'status']);
            $table->index(['last_name', 'first_name']);
            $table->index('registry_number');
            $table->index('household_number');
            $table->index('user_id');
            $table->index('date_of_birth');
            $table->index('residency_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangay_inhabitants');
    }
};
