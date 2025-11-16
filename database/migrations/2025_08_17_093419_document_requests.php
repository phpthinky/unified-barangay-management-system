<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('resident_profile_id')->constrained('resident_profiles')->onDelete('cascade');
            $table->foreignId('barangay_id')->constrained()->onDelete('cascade');
            
            $table->enum('type', [
                'clearance', 
                'indigency', 
                'residency',
                'good_moral',
                'business_permit',
                'cedula'
            ]);
            
            $table->string('purpose');
            $table->text('additional_notes')->nullable();
            
            $table->enum('status', [
                'pending',
                'processing',
                'approved',
                'rejected',
                'ready',
                'claimed'
            ])->default('pending');
            
            $table->string('control_number')->unique();
            $table->string('qr_code')->unique();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_requests');
    }
};