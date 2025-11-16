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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            
            // Municipality Information
            $table->string('municipality_name')->default('Municipality of Sablayan');
            $table->string('province')->default('Occidental Mindoro');
            $table->string('region')->default('MIMAROPA');
            $table->string('municipality_logo')->nullable();
            $table->string('municipality_seal')->nullable();
            $table->text('municipality_address')->nullable();
            $table->string('municipality_contact')->nullable();
            $table->string('municipality_email')->nullable();
            $table->string('municipality_website')->nullable();
            $table->text('municipality_description')->nullable();
            
            // Official Information
            $table->string('mayor_name')->nullable();
            $table->string('mayor_photo')->nullable();
            $table->string('vice_mayor_name')->nullable();
            $table->string('vice_mayor_photo')->nullable();
            $table->json('council_members')->nullable(); // Array of council member details
            
            // ABC Information
            $table->string('abc_president_name')->nullable();
            $table->string('abc_president_photo')->nullable();
            $table->text('abc_description')->nullable();
            
            // System Settings
            $table->string('system_name')->default('Unified Barangay Management System');
            $table->string('system_version')->default('1.0.0');
            $table->boolean('maintenance_mode')->default(false);
            $table->text('maintenance_message')->nullable();
            $table->boolean('allow_public_access')->default(true);
            $table->boolean('allow_resident_registration')->default(true);
            $table->boolean('require_resident_verification')->default(true);
            
            // Email Settings
            $table->string('system_email')->nullable();
            $table->boolean('email_notifications_enabled')->default(true);
            $table->json('notification_settings')->nullable();
            
            // Document Settings
            $table->integer('default_document_processing_days')->default(3);
            $table->decimal('default_document_fee', 8, 2)->default(0);
            $table->integer('document_validity_days')->default(90);
            
            // File Upload Settings
            $table->integer('max_file_size_mb')->default(5);
            $table->json('allowed_file_types')->nullable();
            $table->string('file_storage_path')->default('public/uploads');
            
            // Security Settings
            $table->integer('session_timeout_minutes')->default(120);
            $table->integer('password_reset_timeout_minutes')->default(60);
            $table->boolean('two_factor_enabled')->default(false);
            
            // Appearance Settings
            $table->string('primary_color')->default('#007bff');
            $table->string('secondary_color')->default('#6c757d');
            $table->string('theme')->default('light');
            $table->json('css_customizations')->nullable();
            
            // Social Media Links
            $table->json('social_media_links')->nullable();
            
            // Terms and Privacy
            $table->text('terms_of_service')->nullable();
            $table->text('privacy_policy')->nullable();
            $table->text('data_retention_policy')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};