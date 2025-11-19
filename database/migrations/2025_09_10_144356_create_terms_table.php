<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('barangay_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('position');
            $table->date('term_start');
            $table->date('term_end');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_archived')->default(false);
            $table->enum('appointment_type', ['elected', 'appointed', 'designated'])->default('elected');
            $table->date('election_date')->nullable();
            $table->string('election_type')->nullable();
            $table->text('appointment_details')->nullable();
            $table->json('achievements')->nullable();
            $table->json('projects_completed')->nullable();
            $table->json('performance_metrics')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->foreignId('archived_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('transition_notes')->nullable();
            $table->foreignId('succeeded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('archived_documents')->nullable();
            $table->json('handover_documents')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
            $table->index(['barangay_id', 'position', 'is_active']);
            $table->index('is_archived');
            $table->index(['term_start', 'term_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terms');
    }
};
