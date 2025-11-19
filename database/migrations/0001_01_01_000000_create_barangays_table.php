<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('public_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('social_media')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
            
            $table->index(['slug', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangays');
    }
};
