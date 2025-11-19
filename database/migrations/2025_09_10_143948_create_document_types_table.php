<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('requirements')->nullable();
            $table->json('form_fields')->nullable();
            $table->decimal('fee', 8, 2)->default(0);
            $table->integer('processing_days')->default(3);
            $table->text('template_content')->nullable();
            $table->json('template_fields')->nullable();
            $table->boolean('requires_verification')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('category')->default('general');
            $table->enum('document_format', ['certificate', 'id_card', 'half_sheet', 'legal', 'custom'])->default('certificate');
            $table->text('format_notes')->nullable();
            $table->boolean('enable_printing')->default(true);
            $table->timestamps();
            
            $table->index(['is_active', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
