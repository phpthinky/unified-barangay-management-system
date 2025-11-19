<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('barangay_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_type_id')->constrained()->cascadeOnDelete();
            $table->json('form_data')->nullable();
            $table->text('purpose')->nullable();
            $table->integer('copies_requested')->default(1);
            $table->json('uploaded_files')->nullable();
            $table->enum('status', ['pending', 'processing', 'approved', 'rejected', 'released'])->default('pending');
            $table->boolean('has_pending_complaints')->default(false);
            $table->text('notes')->nullable();
            $table->timestamp('submitted_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('processing_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->decimal('amount_paid', 8, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->string('generated_file')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['barangay_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('tracking_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};
