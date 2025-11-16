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
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('users', 'session_token')) {
                $table->string('session_token', 60)->nullable()->after('remember_token');
            }
            
            if (!Schema::hasColumn('users', 'is_logged_in')) {
                $table->boolean('is_logged_in')->default(false)->after('is_active');
            }
            
            if (!Schema::hasColumn('users', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable()->after('last_login_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['session_token', 'is_logged_in', 'last_activity_at']);
        });
    }
};