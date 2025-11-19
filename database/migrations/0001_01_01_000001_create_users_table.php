<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verification_token', 6)->nullable();
            $table->timestamp('email_verification_token_expires_at')->nullable();
            $table->integer('email_verification_attempts')->default(0);
            $table->timestamp('email_verification_last_sent_at')->nullable();
            $table->string('password');
            $table->foreignId('barangay_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->string('contact_number')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->text('address')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->string('employee_id')->nullable();
            $table->string('position_title')->nullable();
            $table->date('term_start')->nullable();
            $table->date('term_end')->nullable();
            $table->string('committee')->nullable();
            $table->integer('councilor_number')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->timestamp('archived_at')->nullable();
            $table->foreignId('archived_by')->nullable()->constrained('users')->nullOnDelete();
            $table->rememberToken();
            $table->string('session_token')->nullable();
            $table->boolean('is_logged_in')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['barangay_id', 'is_active']);
            $table->index('is_archived');
            $table->index('archived_at');
            $table->index(['last_name', 'first_name']);
            $table->index('email_verification_token');
            $table->index('session_token');
            $table->index('is_logged_in');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
