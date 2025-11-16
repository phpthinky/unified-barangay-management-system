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
        Schema::table('document_types', function (Blueprint $table) {
            // Add document_format field to identify printing type
            $table->enum('document_format', [
                'certificate',      // Standard certificate (8.5x11 or A4)
                'id_card',          // ID card size (3.375" x 2.125")
                'half_sheet',       // Half sheet/short bond
                'legal',            // Legal size paper
                'custom'            // Custom size/format
            ])->default('certificate')->after('category');
            
            // Add notes about the format
            $table->text('format_notes')->nullable()->after('document_format');
            
            // Add enable/disable printing
            $table->boolean('enable_printing')->default(true)->after('format_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_types', function (Blueprint $table) {
            $table->dropColumn(['document_format', 'format_notes', 'enable_printing']);
        });
    }
};