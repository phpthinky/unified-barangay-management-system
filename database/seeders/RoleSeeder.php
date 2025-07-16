<?php

namespace Database\Seeders;
// database/seeders/RoleSeeder.php
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['superadmin','staff','researcher','guest'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
