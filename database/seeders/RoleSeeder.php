<?php
// database/seeders/RoleSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'captain', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'staff', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'lupon', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'guest', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'abc', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'resident', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}