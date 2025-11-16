<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add session tracking for no double login feature
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('session_token', 255)->nullable()->after('remember_token');
            $table->boolean('is_logged_in')->default(false)->after('session_token');
            $table->timestamp('last_activity_at')->nullable()->after('last_login_at');
            
            // Add index for better performance
            $table->index('session_token');
            $table->index('is_logged_in');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['session_token']);
            $table->dropIndex(['is_logged_in']);
            $table->dropColumn(['session_token', 'is_logged_in', 'last_activity_at']);
        });
    }
};