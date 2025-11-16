<?php
// FILE: database/migrations/YYYY_MM_DD_XXXXXX_add_residency_fields_to_barangay_inhabitants.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangay_inhabitants', function (Blueprint $table) {
            // ✅ Residency Information
            $table->enum('residency_type', ['permanent', 'temporary', 'transient'])->default('permanent')->after('residency_since');
            
            // ✅ Proof of Residency
            $table->string('cedula_number')->nullable()->after('residency_type');
            $table->string('certificate_of_residency_number')->nullable()->after('cedula_number');
            $table->string('proof_of_residency_file')->nullable()->after('certificate_of_residency_number');
            
            // Add indexes for better performance
            $table->index('residency_type');
        });
    }

    public function down(): void
    {
        Schema::table('barangay_inhabitants', function (Blueprint $table) {
            $table->dropIndex(['residency_type']);
            
            $table->dropColumn([
                'residency_type',
                'cedula_number',
                'certificate_of_residency_number',
                'proof_of_residency_file',
            ]);
        });
    }
};