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
        Schema::create('business_permit_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('category', ['micro', 'small', 'medium', 'large', 'home_based', 'street_vendor'])->default('micro');
            $table->json('requirements')->nullable(); // Required documents/information
            $table->decimal('base_fee', 10, 2)->default(0);
            $table->json('additional_fees')->nullable(); // Other fees that might apply
            $table->integer('processing_days')->default(7);
            $table->integer('validity_months')->default(12); // How long permit is valid
            $table->boolean('requires_inspection')->default(false);
            $table->boolean('requires_fire_safety')->default(false);
            $table->boolean('requires_health_permit')->default(false);
            $table->boolean('requires_environmental_clearance')->default(false);
            $table->text('template_content')->nullable(); // For permit PDF generation
            $table->json('template_fields')->nullable(); // Dynamic fields
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_permit_types');
    }
};