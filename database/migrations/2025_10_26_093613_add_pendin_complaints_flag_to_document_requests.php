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
        Schema::table('document_requests', function (Blueprint $table) {
            // Add flag for pending cases (for staff review)
            $table->boolean('has_pending_complaints')->default(false)->after('status');
            
            // Notes can already exist, but make sure it's there
            if (!Schema::hasColumn('document_requests', 'notes')) {
                $table->text('notes')->nullable()->after('has_pending_complaints');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn('has_pending_complaints');
            // Don't drop notes in case it was already there
        });
    }
};