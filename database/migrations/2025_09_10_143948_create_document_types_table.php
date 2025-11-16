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
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('requirements')->nullable(); // Array of required documents/info
            $table->decimal('fee', 8, 2)->default(0);
            $table->integer('processing_days')->default(3);
            $table->text('template_content')->nullable(); // For PDF generation
            $table->json('template_fields')->nullable(); // Dynamic fields for the template
            $table->boolean('requires_verification')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('category')->default('general'); // general, business, legal, etc.
            $table->timestamps();
            
            $table->index(['is_active', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};