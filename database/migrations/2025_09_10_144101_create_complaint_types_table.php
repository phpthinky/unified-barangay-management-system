<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('category', ['civil', 'criminal', 'administrative', 'barangay', 'others'])->default('barangay');
            $table->enum('default_handler_type', ['captain', 'secretary', 'lupon', 'any_staff'])->default('secretary');
            $table->boolean('requires_hearing')->default(false);
            $table->integer('estimated_resolution_days')->default(15);
            $table->json('required_information')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_types');
    }
};
