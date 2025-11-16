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
        Schema::create('complaint_hearings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->onDelete('cascade');
            $table->foreignId('barangay_id')->constrained()->onDelete('cascade');
            
            // Hearing Schedule
            $table->string('hearing_number');
            $table->enum('hearing_type', ['mediation', 'conciliation', 'arbitration', 'formal_hearing'])->default('mediation');
            $table->datetime('scheduled_date');
            $table->string('venue')->nullable();
            $table->text('agenda')->nullable();
            
            // Participants
            $table->json('lupon_members')->nullable(); // Array of Lupon member IDs
            $table->foreignId('presiding_officer')->nullable()->constrained('users')->onDelete('set null');
            $table->json('attendees')->nullable(); // Who attended
            $table->json('absent_parties')->nullable(); // Who was absent
            
            // Hearing Status
            $table->enum('status', ['scheduled', 'ongoing', 'postponed', 'completed', 'cancelled'])->default('scheduled');
            $table->datetime('actual_start_time')->nullable();
            $table->datetime('actual_end_time')->nullable();
            
            // Hearing Results
            $table->text('minutes')->nullable();
            $table->text('resolution')->nullable();
            $table->enum('outcome', ['settled', 'failed', 'postponed', 'referred', 'withdrawn'])->nullable();
            $table->json('agreements_reached')->nullable();
            $table->json('uploaded_documents')->nullable(); // Minutes, agreements, etc.
            
            // Next Steps
            $table->boolean('requires_next_hearing')->default(false);
            $table->date('next_hearing_date')->nullable();
            $table->text('next_steps')->nullable();
            
            $table->timestamps();
            
            $table->index(['complaint_id', 'status']);
            $table->index(['barangay_id', 'scheduled_date']);
            $table->index(['presiding_officer']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_hearings');
    }
};