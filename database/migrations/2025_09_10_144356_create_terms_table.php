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
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('barangay_id')->nullable()->constrained()->onDelete('cascade'); // Null for municipality-level positions
            
            // Term Information
            $table->string('position'); // barangay-captain, abc-president, barangay-secretary, etc.
            $table->date('term_start');
            $table->date('term_end');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_archived')->default(false);
            
            // Election/Appointment Details
            $table->enum('appointment_type', ['elected', 'appointed', 'designated'])->default('elected');
            $table->date('election_date')->nullable();
            $table->string('election_type')->nullable(); // regular, special, recall
            $table->text('appointment_details')->nullable();
            
            // Performance Tracking
            $table->json('achievements')->nullable(); // Key achievements during term
            $table->json('projects_completed')->nullable();
            $table->json('performance_metrics')->nullable();
            
            // Transition Information
            $table->timestamp('archived_at')->nullable();
            $table->foreignId('archived_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('transition_notes')->nullable();
            $table->foreignId('succeeded_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Document Archives
            $table->json('archived_documents')->nullable(); // Important documents from the term
            $table->json('handover_documents')->nullable(); // Documents for successor
            
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
            $table->index(['barangay_id', 'position', 'is_active']);
            $table->index(['is_archived']);
            $table->index(['term_start', 'term_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms');
    }
};