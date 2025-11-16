<?php
// ============================================
// 2. SEEDER: Add Barangay Councilor Role
// ============================================
// Run: php artisan make:seeder AddCouncilorRoleSeeder

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AddCouncilorRoleSeeder extends Seeder
{
    public function run()
    {
        // Create Barangay Councilor role
        $councilorRole = Role::firstOrCreate(
            ['name' => 'barangay-councilor'],
            ['guard_name' => 'web']
        );

        // Get existing permissions that councilors should have (similar to staff but with some additions)
        $permissions = [
            'view_dashboard',
            'view_residents',
            'view_document_requests',
            'view_business_permits',
            'view_blotter_records',
            'view_announcements',
            'create_announcements',
            'update_announcements',
            'review_document_requests',
            'review_business_permits',
            'generate_reports',
        ];

        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName],
                ['guard_name' => 'web']
            );
            $councilorRole->givePermissionTo($permission);
        }
    }
}