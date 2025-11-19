<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_number')->unique();
            $table->foreignId('complainant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('barangay_id')->constrained()->cascadeOnDelete();
            $table->foreignId('complaint_type_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->text('description');
            $table->json('form_data')->nullable();
            $table->dateTime('incident_date')->nullable();
            $table->text('incident_location')->nullable();
            $table->json('uploaded_files')->nullable();
            $table->json('respondents')->nullable();
            $table->enum('status', ['received', 'assigned', 'in_process', 'mediation', 'hearing_scheduled', 'resolved', 'closed', 'dismissed'])->default('received');
            $table->enum('workflow_status', [
                'pending_review', 'for_captain_review', 'approved', 'dismissed',
                '1st_summons_issued', '2nd_summons_issued', '3rd_summons_issued',
                'respondent_appeared', 'summons_failed', 'captain_mediation',
                'settled_by_captain', 'for_lupon', '1st_hearing_scheduled',
                '1st_hearing_ongoing', '1st_hearing_completed', '2nd_hearing_scheduled',
                '2nd_hearing_ongoing', '2nd_hearing_completed', '3rd_hearing_scheduled',
                '3rd_hearing_ongoing', '3rd_hearing_completed', 'resolved_by_lupon',
                'for_certificate', 'certificate_issued', 'closed'
            ])->default('pending_review');
            $table->timestamp('secretary_reviewed_at')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->timestamp('received_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('assigned_role', ['captain', 'secretary', 'lupon', 'staff'])->nullable();
            $table->text('assignment_notes')->nullable();
            $table->text('resolution_details')->nullable();
            $table->enum('resolution_type', ['settled', 'dismissed', 'referred', 'mediated'])->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('resolution_files')->nullable();
            $table->boolean('requires_follow_up')->default(false);
            $table->date('follow_up_date')->nullable();
            $table->text('follow_up_notes')->nullable();
            $table->timestamps();
            
            // Workflow-specific fields
            $table->foreignId('secretary_reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('secretary_notes')->nullable();
            $table->timestamp('captain_approved_at')->nullable();
            $table->foreignId('captain_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('captain_notes')->nullable();
            
            // Summons tracking
            $table->integer('summons_attempt')->default(0);
            $table->date('summons_1_issued_date')->nullable();
            $table->date('summons_1_return_date')->nullable();
            $table->boolean('summons_1_served')->default(false);
            $table->date('summons_2_issued_date')->nullable();
            $table->date('summons_2_return_date')->nullable();
            $table->boolean('summons_2_served')->default(false);
            $table->date('summons_3_issued_date')->nullable();
            $table->date('summons_3_return_date')->nullable();
            $table->boolean('summons_3_served')->default(false);
            $table->boolean('summons_all_failed')->default(false);
            
            // Appearance tracking
            $table->timestamp('respondent_appeared_at')->nullable();
            $table->text('appearance_notes')->nullable();
            
            // Captain mediation
            $table->timestamp('captain_mediation_start')->nullable();
            $table->date('captain_mediation_deadline')->nullable();
            $table->boolean('captain_mediation_extended')->default(false);
            $table->timestamp('settled_by_captain_at')->nullable();
            $table->longText('settlement_terms')->nullable();
            $table->text('captain_mediation_notes')->nullable();
            
            // Lupon assignment
            $table->timestamp('assigned_to_lupon_at')->nullable();
            $table->foreignId('assigned_lupon_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('lupon_assignment_notes')->nullable();
            
            // Hearing tracking
            $table->integer('current_hearing_number')->default(0);
            $table->integer('total_hearings_conducted')->default(0);
            
            // Resolution
            $table->timestamp('lupon_resolved_at')->nullable();
            $table->longText('lupon_resolution')->nullable();
            $table->text('lupon_resolution_notes')->nullable();
            
            // Certificate
            $table->timestamp('certificate_issued_at')->nullable();
            $table->string('certificate_number', 50)->nullable();
            $table->string('referred_to', 100)->nullable();
            $table->text('certificate_notes')->nullable();
            
            $table->integer('days_in_process')->nullable();
            
            $table->index(['barangay_id', 'status']);
            $table->index(['complainant_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index('complaint_number');
            $table->index('workflow_status');
            $table->index('summons_attempt');
            $table->index('current_hearing_number');
            $table->index(['barangay_id', 'workflow_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
