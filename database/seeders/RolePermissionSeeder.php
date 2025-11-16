<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view-barangays',
            'create-barangays',
            'edit-barangays',
            'delete-barangays',
            
            'view-all-residents',
            'view-barangay-residents',
            'verify-residents',
            'manage-residents',
            
            'view-document-requests',
            'process-document-requests',
            'approve-document-requests',
            'create-document-types',
            'manage-document-types',
            
            'view-complaints',
            'file-complaints',
            'assign-complaints',
            'process-complaints',
            'resolve-complaints',
            'manage-complaint-types',
            'schedule-hearings',
            'conduct-hearings',
            
            'view-business-permits',
            'apply-business-permits',
            'process-business-permits',
            'approve-business-permits',
            'inspect-businesses',
            'manage-permit-types',
            
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'archive-users',
            
            'view-terms',
            'create-terms',
            'archive-terms',
            
            'view-municipality-reports',
            'view-barangay-reports',
            'export-reports',
            
            'manage-system-settings',
            'manage-site-settings',
            
            'view-own-profile',
            'edit-own-profile',
            'upload-profile-photo',
            
            'register-as-resident',
            'track-requests',
            'verify-documents',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // ABC President - Now the main administrator with all permissions
        $abcPresident = Role::create(['name' => 'abc-president']);
        $abcPresident->givePermissionTo(Permission::all());

        // Barangay Captain
        $barangayCaptain = Role::create(['name' => 'barangay-captain']);
        $barangayCaptain->givePermissionTo([
            'view-barangays',
            'view-barangay-residents',
            'verify-residents',
            'manage-residents',
            'view-document-requests',
            'process-document-requests',
            'approve-document-requests',
            'view-complaints',
            'assign-complaints',
            'process-complaints',
            'resolve-complaints',
            'schedule-hearings',
            'conduct-hearings',
            'view-business-permits',
            'process-business-permits',
            'approve-business-permits',
            'inspect-businesses',
            'view-users',
            'create-users',
            'edit-users',
            'view-barangay-reports',
            'export-reports',
            'view-own-profile',
            'edit-own-profile',
            'upload-profile-photo',
        ]);

        // Barangay Councilor
        $barangayCouncilor = Role::create(['name' => 'barangay-councilor']);
        $barangayCouncilor->givePermissionTo([
            'view-barangay-residents',
            'view-document-requests',
            'view-complaints',
            'view-business-permits',
            'view-barangay-reports',
            'view-own-profile',
            'edit-own-profile',
            'upload-profile-photo',
        ]);

        // Barangay Secretary
        $barangaySecretary = Role::create(['name' => 'barangay-secretary']);
        $barangaySecretary->givePermissionTo([
            'view-barangay-residents',
            'verify-residents',
            'manage-residents',
            'view-document-requests',
            'process-document-requests',
            'approve-document-requests',
            'view-complaints',
            'assign-complaints',
            'process-complaints',
            'schedule-hearings',
            'view-business-permits',
            'process-business-permits',
            'view-barangay-reports',
            'export-reports',
            'view-own-profile',
            'edit-own-profile',
            'upload-profile-photo',
        ]);

        // Barangay Treasurer
        $barangayTreasurer = Role::create(['name' => 'barangay-treasurer']);
        $barangayTreasurer->givePermissionTo([
            'view-barangay-residents',
            'view-document-requests',
            'view-business-permits',
            'view-barangay-reports',
            'export-reports',
            'view-own-profile',
            'edit-own-profile',
            'upload-profile-photo',
        ]);

        // Barangay Staff
        $barangayStaff = Role::create(['name' => 'barangay-staff']);
        $barangayStaff->givePermissionTo([
            'view-barangay-residents',
            'verify-residents',
            'view-document-requests',
            'process-document-requests',
            'view-complaints',
            'process-complaints',
            'view-business-permits',
            'process-business-permits',
            'view-own-profile',
            'edit-own-profile',
            'upload-profile-photo',
        ]);

        // Lupon
        $lupon = Role::create(['name' => 'lupon-member']);
        $lupon->givePermissionTo([
            'view-complaints',
            'process-complaints',
            'resolve-complaints',
            'schedule-hearings',
            'conduct-hearings',
            'view-own-profile',
            'edit-own-profile',
            'upload-profile-photo',
        ]);

        // Resident
        $resident = Role::create(['name' => 'resident']);
        $resident->givePermissionTo([
            'file-complaints',
            'apply-business-permits',
            'view-own-profile',
            'edit-own-profile',
            'upload-profile-photo',
            'register-as-resident',
            'track-requests',
            'verify-documents',
        ]);

        echo "Roles and permissions created successfully!\n";
        echo "ABC President is now the main administrator with full system access.\n";
        echo "Created roles: abc-president, barangay-captain, barangay-councilor, barangay-secretary, barangay-treasurer, barangay-staff, lupon, resident\n";
    }
}