<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_number')->unique();
            $table->foreignId('complainant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('barangay_id')->constrained()->onDelete('cascade');
            $table->foreignId('complaint_type_id')->constrained()->onDelete('cascade');
            
            // Complaint Details
            $table->string('subject');
            $table->text('description');
            $table->json('form_data')->nullable(); // Additional fields specific to complaint type
            $table->datetime('incident_date')->nullable();
            $table->text('incident_location')->nullable();
            $table->json('uploaded_files')->nullable(); // Evidence files
            
            // Respondent Information
            $table->json('respondents')->nullable(); // Array of respondent details
            
            // Status Management
            $table->enum('status', ['received', 'assigned', 'in_process', 'mediation', 'hearing_scheduled', 'resolved', 'closed', 'dismissed'])->default('received');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->timestamp('received_at');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            
            // Assignment
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('assigned_role', ['captain', 'secretary', 'lupon', 'staff'])->nullable();
            $table->text('assignment_notes')->nullable();
            
            // Resolution
            $table->text('resolution_details')->nullable();
            $table->enum('resolution_type', ['settled', 'dismissed', 'referred', 'mediated'])->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('resolution_files')->nullable(); // Resolution documents
            
            // Follow-up
            $table->boolean('requires_follow_up')->default(false);
            $table->date('follow_up_date')->nullable();
            $table->text('follow_up_notes')->nullable();
            
            $table->timestamps();
            
            $table->index(['barangay_id', 'status']);
            $table->index(['complainant_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['complaint_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};