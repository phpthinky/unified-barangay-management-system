<?php
// FILE: database/migrations/YYYY_MM_DD_000001_create_users_table_complete.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // Core Identity
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Barangay Association
            $table->foreignId('barangay_id')->nullable()->constrained()->onDelete('set null');
            
            // Name Components
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            
            // Contact Information
            $table->string('contact_number')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->text('address')->nullable();
            
            // Profile & Media
            $table->string('profile_photo')->nullable();
            $table->string('avatar')->nullable(); // For officials
            
            // Account Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            
            // Staff/Employee Fields
            $table->string('employee_id')->nullable();
            $table->string('position_title')->nullable();
            
            // Official Fields (Councilors, Captain, etc.)
            $table->date('term_start')->nullable();
            $table->date('term_end')->nullable();
            $table->string('committee')->nullable(); // For councilors
            $table->integer('councilor_number')->nullable(); // For councilors
            
            // Archive System
            $table->boolean('is_archived')->default(false);
            $table->timestamp('archived_at')->nullable();
            $table->foreignId('archived_by')->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            
            // Tokens
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['barangay_id', 'is_active']);
            $table->index(['is_archived']);
            $table->index(['archived_at']);
            $table->index(['last_name', 'first_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};