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
        Schema::create('barangay_officials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barangay_id')->constrained()->onDelete('cascade');

            // Official Information
            $table->string('name');
            $table->string('position'); // e.g., 'Punong Barangay', 'Kagawad 1', 'Secretary', 'Treasurer', 'SK Chairperson'
            $table->string('committee')->nullable(); // For councilors - e.g., 'Health & Sanitation', 'Peace & Order'
            $table->integer('display_order')->default(0); // For ordering in org chart

            // Term Information
            $table->date('term_start');
            $table->date('term_end');
            $table->boolean('is_active')->default(true); // Current term

            // Contact & Photo
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->string('avatar')->nullable(); // Photo for org chart

            // Additional Info
            $table->text('description')->nullable(); // Brief bio or responsibilities

            $table->timestamps();

            // Indexes
            $table->index(['barangay_id', 'is_active']);
            $table->index(['term_start', 'term_end']);
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangay_officials');
    }
};
