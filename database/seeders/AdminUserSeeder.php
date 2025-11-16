<?php
// database/seeders/AdminUserSeeder.php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'roivanrita@gmail.com'],
            [
                'name' => 'Roi Admin',
                'password' => Hash::make('Hearts012!'),
                'role' => User::ROLE_ADMIN, // Using the constant
                'email_verified_at' => Carbon::now(), // Explicit verification
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );
    }
}