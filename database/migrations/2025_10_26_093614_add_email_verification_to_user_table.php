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
        Schema::table('users', function (Blueprint $table) {
            // Email verification token (6-digit code)
            $table->string('email_verification_token', 6)->nullable()->after('email_verified_at');
            $table->timestamp('email_verification_token_expires_at')->nullable()->after('email_verification_token');
            
            // Track verification attempts
            $table->integer('email_verification_attempts')->default(0)->after('email_verification_token_expires_at');
            $table->timestamp('email_verification_last_sent_at')->nullable()->after('email_verification_attempts');
            
            // Index for faster lookups
            $table->index('email_verification_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email_verification_token']);
            $table->dropColumn([
                'email_verification_token',
                'email_verification_token_expires_at',
                'email_verification_attempts',
                'email_verification_last_sent_at',
            ]);
        });
    }
};