<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * STEP 1: Add real workflow fields to complaints table
     * This matches actual barangay practice
     */
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            // ============================================
            // WORKFLOW STATUS
            // ============================================
            $table->enum('workflow_status', [
                'pending_review',           // Just filed, secretary needs to review
                'for_captain_review',       // Secretary prepared, waiting for captain
                'approved',                 // Captain approved
                'dismissed',                // Captain dismissed
                '1st_summons_issued',       // First summons sent
                '2nd_summons_issued',       // Second summons sent
                '3rd_summons_issued',       // Third summons sent
                'respondent_appeared',      // Respondent showed up
                'summons_failed',           // All 3 summons failed
                'captain_mediation',        // Captain mediating (15 days)
                'settled_by_captain',       // Captain resolved it
                'for_lupon',                // Assigned to Lupon
                '1st_hearing_scheduled',    // First hearing set
                '1st_hearing_ongoing',      // First hearing in progress
                '1st_hearing_completed',    // First hearing done
                '2nd_hearing_scheduled',    // Second hearing set
                '2nd_hearing_ongoing',      // Second hearing in progress
                '2nd_hearing_completed',    // Second hearing done
                '3rd_hearing_scheduled',    // Final hearing set
                '3rd_hearing_ongoing',      // Final hearing in progress
                '3rd_hearing_completed',    // Final hearing done
                'resolved_by_lupon',        // Lupon resolved it
                'for_certificate',          // Need certificate
                'certificate_issued',       // Certificate issued
                'closed'                    // Case closed
            ])->default('pending_review')->after('status');
            
            // ============================================
            // SECRETARY REVIEW
            // ============================================
            $table->timestamp('secretary_reviewed_at')->nullable()->after('workflow_status');
            $table->foreignId('secretary_reviewed_by')->nullable()->constrained('users')->nullOnDelete()->after('secretary_reviewed_at');
            $table->text('secretary_notes')->nullable()->after('secretary_reviewed_by');
            
            // ============================================
            // CAPTAIN APPROVAL
            // ============================================
            $table->timestamp('captain_approved_at')->nullable()->after('secretary_notes');
            $table->foreignId('captain_approved_by')->nullable()->constrained('users')->nullOnDelete()->after('captain_approved_at');
            $table->text('captain_notes')->nullable()->after('captain_approved_by');
            
            // ============================================
            // SUMMONS TRACKING (3 attempts max)
            // ============================================
            $table->integer('summons_attempt')->default(0)->after('captain_notes');
            
            // 1st Summons
            $table->date('summons_1_issued_date')->nullable()->after('summons_attempt');
            $table->date('summons_1_return_date')->nullable()->after('summons_1_issued_date');
            $table->boolean('summons_1_served')->default(false)->after('summons_1_return_date');
            
            // 2nd Summons
            $table->date('summons_2_issued_date')->nullable()->after('summons_1_served');
            $table->date('summons_2_return_date')->nullable()->after('summons_2_issued_date');
            $table->boolean('summons_2_served')->default(false)->after('summons_2_return_date');
            
            // 3rd Summons
            $table->date('summons_3_issued_date')->nullable()->after('summons_2_served');
            $table->date('summons_3_return_date')->nullable()->after('summons_3_issued_date');
            $table->boolean('summons_3_served')->default(false)->after('summons_3_return_date');
            
            $table->boolean('summons_all_failed')->default(false)->after('summons_3_served');
            
            // ============================================
            // RESPONDENT APPEARANCE
            // ============================================
            $table->timestamp('respondent_appeared_at')->nullable()->after('summons_all_failed');
            $table->text('appearance_notes')->nullable()->after('respondent_appeared_at');
            
            // ============================================
            // CAPTAIN MEDIATION (15 days)
            // ============================================
            $table->timestamp('captain_mediation_start')->nullable()->after('appearance_notes');
            $table->date('captain_mediation_deadline')->nullable()->after('captain_mediation_start'); // 15 days from start
            $table->boolean('captain_mediation_extended')->default(false)->after('captain_mediation_deadline');
            $table->timestamp('settled_by_captain_at')->nullable()->after('captain_mediation_extended');
            $table->longText('settlement_terms')->nullable()->after('settled_by_captain_at');
            $table->text('captain_mediation_notes')->nullable()->after('settlement_terms');
            
            // ============================================
            // LUPON ASSIGNMENT & HEARINGS
            // ============================================
            $table->timestamp('assigned_to_lupon_at')->nullable()->after('captain_mediation_notes');
            $table->foreignId('assigned_lupon_id')->nullable()->constrained('users')->nullOnDelete()->after('assigned_to_lupon_at');
            $table->text('lupon_assignment_notes')->nullable()->after('assigned_lupon_id');
            
            // Quick hearing tracking (detailed records in complaint_hearings table)
            $table->integer('current_hearing_number')->default(0)->after('lupon_assignment_notes'); // 0, 1, 2, 3
            $table->integer('total_hearings_conducted')->default(0)->after('current_hearing_number');
            
            // ============================================
            // LUPON RESOLUTION
            // ============================================
            $table->timestamp('lupon_resolved_at')->nullable()->after('total_hearings_conducted');
            $table->longText('lupon_resolution')->nullable()->after('lupon_resolved_at');
            $table->text('lupon_resolution_notes')->nullable()->after('lupon_resolution');
            
            // ============================================
            // CERTIFICATE TO FILE ACTION
            // ============================================
            $table->timestamp('certificate_issued_at')->nullable()->after('lupon_resolution_notes');
            $table->string('certificate_number', 50)->nullable()->after('certificate_issued_at');
            $table->string('referred_to', 100)->nullable()->after('certificate_number'); // 'Court', 'Police', etc.
            $table->text('certificate_notes')->nullable()->after('referred_to');
            
            // ============================================
            // HELPER FIELDS
            // ============================================
            $table->integer('days_in_process')->nullable()->after('certificate_notes'); // Total days from filing to resolution
            //$table->timestamp('closed_at')->nullable()->after('days_in_process'); // When case was finally closed
            
            // ============================================
            // INDEXES for performance
            // ============================================
            $table->index('workflow_status');
            $table->index('summons_attempt');
            $table->index('current_hearing_number');
            $table->index(['barangay_id', 'workflow_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['secretary_reviewed_by']);
            $table->dropForeign(['captain_approved_by']);
            $table->dropForeign(['assigned_lupon_id']);
            
            // Drop indexes
            $table->dropIndex(['workflow_status']);
            $table->dropIndex(['summons_attempt']);
            $table->dropIndex(['current_hearing_number']);
            $table->dropIndex(['barangay_id', 'workflow_status']);
            
            // Drop all columns
            $table->dropColumn([
                'workflow_status',
                'secretary_reviewed_at',
                'secretary_reviewed_by',
                'secretary_notes',
                'captain_approved_at',
                'captain_approved_by',
                'captain_notes',
                'summons_attempt',
                'summons_1_issued_date',
                'summons_1_return_date',
                'summons_1_served',
                'summons_2_issued_date',
                'summons_2_return_date',
                'summons_2_served',
                'summons_3_issued_date',
                'summons_3_return_date',
                'summons_3_served',
                'summons_all_failed',
                'respondent_appeared_at',
                'appearance_notes',
                'captain_mediation_start',
                'captain_mediation_deadline',
                'captain_mediation_extended',
                'settled_by_captain_at',
                'settlement_terms',
                'captain_mediation_notes',
                'assigned_to_lupon_at',
                'assigned_lupon_id',
                'lupon_assignment_notes',
                'current_hearing_number',
                'total_hearings_conducted',
                'lupon_resolved_at',
                'lupon_resolution',
                'lupon_resolution_notes',
                'certificate_issued_at',
                'certificate_number',
                'referred_to',
                'certificate_notes',
                'days_in_process',
            ]);
        });
    }
};