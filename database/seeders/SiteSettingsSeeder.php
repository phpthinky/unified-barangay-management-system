<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSettings;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SiteSettings::create([
            // Municipality Information
            'municipality_name' => 'Municipality of Sablayan',
            'province' => 'Occidental Mindoro',
            'region' => 'MIMAROPA',
            'municipality_address' => 'Municipal Hall, Poblacion, Sablayan, Occidental Mindoro 5104',
            'municipality_contact' => '(043) 123-4567',
            'municipality_email' => 'info@sablayan.gov.ph',
            'municipality_website' => 'https://sablayan.gov.ph',
            'municipality_description' => 'Sablayan is a first-class municipality in the province of Occidental Mindoro, Philippines. Known for its rich natural resources, beautiful beaches, and vibrant agricultural sector.',
            
            // Official Information
            'mayor_name' => 'Hon. Eduardo B. Gadiano',
            'vice_mayor_name' => 'Hon. Wilhelmina G. Santos',
            'council_members' => [
                ['name' => 'Hon. Roberto C. Cruz', 'position' => 'Councilor'],
                ['name' => 'Hon. Maria L. Rodriguez', 'position' => 'Councilor'],
                ['name' => 'Hon. Antonio M. Fernandez', 'position' => 'Councilor'],
                ['name' => 'Hon. Carmen S. Villanueva', 'position' => 'Councilor'],
                ['name' => 'Hon. Jose R. Mendoza', 'position' => 'Councilor'],
                ['name' => 'Hon. Elena T. Pascual', 'position' => 'Councilor'],
                ['name' => 'Hon. Ricardo A. Torres', 'position' => 'Councilor'],
                ['name' => 'Hon. Patricia D. Reyes', 'position' => 'Councilor'],
            ],
            
            // ABC Information
            'abc_president_name' => 'Hon. Maria Santos Cruz',
            'abc_description' => 'The Association of Barangay Captains (ABC) serves as the coordinating body for all barangay captains in the municipality.',
            
            // System Settings
            'system_name' => 'Unified Barangay Management System',
            'system_version' => '1.0.0',
            'maintenance_mode' => false,
            'maintenance_message' => 'System is currently under maintenance. Please try again later.',
            'allow_public_access' => true,
            'allow_resident_registration' => true,
            'require_resident_verification' => true,
            
            // Email Settings
            'system_email' => 'noreply@sablayan.gov.ph',
            'email_notifications_enabled' => true,
            'notification_settings' => [
                'document_approved' => true,
                'complaint_assigned' => true,
                'permit_approved' => true,
                'hearing_scheduled' => true,
            ],
            
            // Document Settings
            'default_document_processing_days' => 3,
            'default_document_fee' => 50.00,
            'document_validity_days' => 90,
            
            // File Upload Settings
            'max_file_size_mb' => 5,
            'allowed_file_types' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'],
            'file_storage_path' => 'public/uploads',
            
            // Security Settings
            'session_timeout_minutes' => 120,
            'password_reset_timeout_minutes' => 60,
            'two_factor_enabled' => false,
            
            // Appearance Settings
            'primary_color' => '#007bff',
            'secondary_color' => '#6c757d',
            'theme' => 'light',
            'css_customizations' => null,
            
            // Social Media Links
            'social_media_links' => [
                'facebook' => 'https://facebook.com/SablayanMunicipality',
                'twitter' => 'https://twitter.com/SablayanGov',
                'instagram' => 'https://instagram.com/sablayan_gov',
            ],
            
            // Terms and Privacy
            'terms_of_service' => 'By using this system, you agree to comply with all applicable laws and regulations...',
            'privacy_policy' => 'We are committed to protecting your privacy and personal information...',
            'data_retention_policy' => 'Personal data will be retained as long as necessary for the purposes outlined...',
        ]);

        echo "Site settings created successfully!\n";
        echo "Municipality: Municipality of Sablayan\n";
        echo "System: Unified Barangay Management System v1.0.0\n";
    }
}