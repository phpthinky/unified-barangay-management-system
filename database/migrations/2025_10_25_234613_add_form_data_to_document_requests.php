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
        // Add form_fields to document_types table (if not exists)
        Schema::table('document_types', function (Blueprint $table) {
            if (!Schema::hasColumn('document_types', 'form_fields')) {
                $table->json('form_fields')->nullable()->after('requirements');
            }
            if (!Schema::hasColumn('document_types', 'category')) {
                $table->string('category')->nullable()->after('description');
            }
        });

        // Add form_data to document_requests table (if not exists)
        Schema::table('document_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('document_requests', 'form_data')) {
                $table->json('form_data')->nullable()->after('document_type_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_types', function (Blueprint $table) {
            $table->dropColumn(['form_fields', 'category']);
        });

        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn('form_data');
        });
    }
};