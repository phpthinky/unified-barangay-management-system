<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "Starting UBMS Database Seeding...\n\n";

        // 1. Create roles and permissions first
        echo "=== CREATING ROLES AND PERMISSIONS ===\n";
        $this->call(RolePermissionSeeder::class);
        echo "\n";

        // 2. Create site settings
        echo "=== CREATING SITE SETTINGS ===\n";
        $this->call(SiteSettingsSeeder::class);
        echo "\n";

        // 3. Create barangays
        echo "=== CREATING BARANGAYS ===\n";
        $this->call(BarangaySeeder::class);
        echo "\n";

        // 4. Create users (admins, officials, residents)
        echo "=== CREATING USERS ===\n";
        $this->call(UserSeeder::class);
        echo "\n";

        echo "=== CREATING DOCUMENT TYPES ===\n";
        $this->call(DocumentTypeSeeder::class);
        echo "\n";

        // 6. Create complaint types
        echo "=== CREATING COMPLAINT TYPES ===\n";
        $this->call(ComplaintTypeSeeder::class);
        echo "\n";
        

        // 7. Create business permit types
        echo "=== CREATING BUSINESS PERMIT TYPES ===\n";
        $this->call(BusinessPermitTypeSeeder::class);
        echo "\n";

        echo "=== UBMS DATABASE SEEDING COMPLETED ===\n";
        echo "System is now ready for use!\n\n";


        
        echo "ABC President:\n";
        echo "Email: abc.president@sablayan.gov.ph\n";
        echo "Role: Executive oversight\n\n";
     
        
    }
}