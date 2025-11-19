<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_hearings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->cascadeOnDelete();
            $table->foreignId('barangay_id')->constrained()->cascadeOnDelete();
            $table->string('hearing_number');
            $table->enum('hearing_type', ['mediation', 'conciliation', 'arbitration', 'formal_hearing'])->default('mediation');
            $table->dateTime('scheduled_date');
            $table->string('venue')->nullable();
            $table->text('agenda')->nullable();
            $table->json('lupon_members')->nullable();
            $table->foreignId('presiding_officer')->nullable()->constrained('users')->nullOnDelete();
            $table->json('attendees')->nullable();
            $table->json('absent_parties')->nullable();
            $table->enum('status', ['scheduled', 'ongoing', 'postponed', 'completed', 'cancelled'])->default('scheduled');
            $table->dateTime('actual_start_time')->nullable();
            $table->dateTime('actual_end_time')->nullable();
            $table->text('minutes')->nullable();
            $table->text('resolution')->nullable();
            $table->enum('outcome', ['settled', 'failed', 'postponed', 'referred', 'withdrawn'])->nullable();
            $table->json('agreements_reached')->nullable();
            $table->json('uploaded_documents')->nullable();
            $table->boolean('requires_next_hearing')->default(false);
            $table->date('next_hearing_date')->nullable();
            $table->text('next_steps')->nullable();
            $table->timestamps();
            
            $table->index(['complaint_id', 'status']);
            $table->index(['barangay_id', 'scheduled_date']);
            $table->index('presiding_officer');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_hearings');
    }
};
