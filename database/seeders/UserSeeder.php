<?php
// FILE: database/seeders/UserSeeder.php - UPDATED

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Barangay;
use App\Models\ResidentProfile;
use App\Models\BarangayInhabitant;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ABC President
        $abcPresident = User::create([
            'name' => 'Maria Santos Cruz',
            'email' => 'abc.president@sablayan.gov.ph',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'first_name' => 'Maria',
            'middle_name' => 'Santos',
            'last_name' => 'Cruz',
            'contact_number' => '(043) 200-0001',
            'address' => 'Poblacion, Sablayan, Occidental Mindoro',
            'term_start' => now()->subMonths(6),
            'term_end' => now()->addYears(2)->addMonths(6),
            'is_active' => true,
        ]);
        $abcPresident->assignRole('abc-president');
        echo "✅ Created ABC President: {$abcPresident->email}\n";

    }
}