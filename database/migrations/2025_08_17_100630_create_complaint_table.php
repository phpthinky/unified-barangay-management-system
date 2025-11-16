<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('barangay_id')->constrained()->onDelete('cascade');
            
            // Complaint details
            $table->string('title');
            $table->text('description');
            $table->enum('type', [
                'noise_disturbance',
                'property_dispute',
                'sanitation',
                'public_safety',
                'violence',
                'other'
            ])->default('other');
            
            // Location details
            $table->string('location');
            $table->string('landmark')->nullable();
            
            // Status tracking
            $table->enum('status', [
                'pending',
                'under_investigation',
                'resolved',
                'dismissed'
            ])->default('pending');
            
            // Resolution details
            $table->text('resolution')->nullable();
            $table->date('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users');
            
            // Evidence/attachments
            $table->string('photo_path')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('complaints');
    }
};