<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_permits', function (Blueprint $table) {
            $table->id();
            $table->string('permit_number')->unique();
            $table->foreignId('applicant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('barangay_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_permit_type_id')->constrained()->cascadeOnDelete();
            $table->string('business_name');
            $table->string('business_type')->nullable();
            $table->text('business_address');
            $table->string('business_contact')->nullable();
            $table->string('business_email')->nullable();
            $table->text('business_description')->nullable();
            $table->date('business_start_date')->nullable();
            $table->integer('number_of_employees')->default(0);
            $table->decimal('estimated_monthly_income', 15, 2)->nullable();
            $table->decimal('business_area_sqm', 8, 2)->nullable();
            $table->string('owner_name')->nullable();
            $table->text('owner_address')->nullable();
            $table->string('owner_contact')->nullable();
            $table->boolean('owner_is_applicant')->default(true);
            $table->json('form_data')->nullable();
            $table->json('uploaded_documents')->nullable();
            $table->text('business_activities')->nullable();
            $table->enum('status', ['pending', 'under_review', 'for_inspection', 'approved', 'rejected', 'expired', 'renewed'])->default('pending');
            $table->timestamp('submitted_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('processing_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('inspection_required')->default(false);
            $table->timestamp('inspection_scheduled_at')->nullable();
            $table->timestamp('inspection_completed_at')->nullable();
            $table->foreignId('inspected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('inspection_result', ['passed', 'failed', 'conditional'])->nullable();
            $table->text('inspection_notes')->nullable();
            $table->json('inspection_photos')->nullable();
            $table->decimal('total_fees', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->json('fee_breakdown')->nullable();
            $table->string('generated_permit_file')->nullable();
            $table->string('qr_code')->nullable();
            $table->boolean('is_digital_copy_issued')->default(false);
            $table->boolean('is_physical_copy_issued')->default(false);
            $table->foreignId('renewed_from')->nullable()->constrained('business_permits')->nullOnDelete();
            $table->boolean('is_renewal')->default(false);
            $table->text('renewal_notes')->nullable();
            $table->timestamps();
            
            $table->index(['barangay_id', 'status']);
            $table->index(['applicant_id', 'status']);
            $table->index('permit_number');
            $table->index('business_name');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_permits');
    }
};
