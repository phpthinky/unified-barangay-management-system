<?php
// FILE: database/seeders/BarangayInhabitantSeeder.php - UPDATED FOR SMART VERIFICATION

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BarangayInhabitant;
use App\Models\Barangay;
use App\Models\User;
use Carbon\Carbon;

class BarangayInhabitantSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n========================================\n";
        echo "ADDING ADDITIONAL RBI RECORDS\n";
        echo "(User-linked RBI records already created in UserSeeder)\n";
        echo "========================================\n\n";

        $poblacion = Barangay::where('slug', 'poblacion')->first();
        $batongBuhay = Barangay::where('slug', 'batong-buhay')->first();
        
        if (!$poblacion) {
            echo "⚠ No barangays found. Please run BarangaySeeder first.\n";
            return;
        }

        // Get staff/officials for "registered_by" and "verified_by"
        $poblacionSecretary = User::role('barangay-secretary')->where('barangay_id', $poblacion->id)->first();
        $poblacionCaptain = User::role('barangay-captain')->where('barangay_id', $poblacion->id)->first();

        if (!$poblacionSecretary || !$poblacionCaptain) {
            echo "⚠ Secretary or Captain not found. Please run UserSeeder first.\n";
            return;
        }

        // Get existing count to continue registry numbers
        $existingCount = BarangayInhabitant::where('barangay_id', $poblacion->id)->count();
        $counter = $existingCount + 1;

        // ========================================
        // ADD FAMILY MEMBERS TO EXISTING HOUSEHOLDS
        // ========================================
        
        // Get the first household from UserSeeder (HH-POB-001)
        $household1Head = BarangayInhabitant::where('barangay_id', $poblacion->id)
            ->where('household_number', 'HH-POB-001')
            ->where('is_household_head', true)
            ->first();

        if ($household1Head) {
            // ✅ Spouse for household 1
            BarangayInhabitant::create([
                'barangay_id' => $poblacion->id,
                'registry_number' => $this->generateRegistryNumber($poblacion->id, $counter++),
                'last_name' => $household1Head->last_name,
                'first_name' => 'Maria',
                'middle_name' => 'Santos',
                'ext' => null,
                'house_number' => $household1Head->house_number,
                'zone_sitio' => $household1Head->zone_sitio,
                'place_of_birth' => 'Sablayan, Occidental Mindoro',
                'date_of_birth' => now()->subYears(28),
                'sex' => 'Female',
                'civil_status' => 'Married',
                'citizenship' => 'Filipino',
                'occupation' => 'Housewife',
                'educational_attainment' => 'High School Graduate',
                'contact_number' => '09171234580',
                'emergency_contact_name' => $household1Head->full_name,
                'emergency_contact_number' => $household1Head->contact_number,
                'emergency_contact_relationship' => 'Spouse',
                
                // ✅ RESIDENCY INFO (SAME AS HOUSEHOLD HEAD)
                'residency_since' => $household1Head->residency_since ?? now()->subYears(2),
                'residency_type' => 'permanent',
                
                'household_number' => 'HH-POB-001',
                'is_household_head' => false,
                'registered_at' => now()->subMonths(rand(3, 10)),
                'registered_by' => $poblacionSecretary->id,
                'is_verified' => true,
                'verified_at' => now()->subDays(rand(5, 30)),
                'verified_by' => $poblacionCaptain->id,
                'user_id' => null,
                'is_active' => true,
                'status' => 'active',
                'has_violations' => false,
                'has_unpaid_dues' => false,
                'attends_assembly' => true,
            ]);
            echo "✓ Added spouse to household HH-POB-001\n";

            // ✅ Children for household 1 (Child 1 - 8yo)
            BarangayInhabitant::create([
                'barangay_id' => $poblacion->id,
                'registry_number' => $this->generateRegistryNumber($poblacion->id, $counter++),
                'last_name' => $household1Head->last_name,
                'first_name' => 'John',
                'middle_name' => $household1Head->first_name,
                'ext' => 'Jr.',
                'house_number' => $household1Head->house_number,
                'zone_sitio' => $household1Head->zone_sitio,
                'place_of_birth' => 'Sablayan, Occidental Mindoro',
                'date_of_birth' => now()->subYears(8),
                'sex' => 'Male',
                'civil_status' => 'Single',
                'citizenship' => 'Filipino',
                'occupation' => 'Student',
                'educational_attainment' => 'Elementary Undergraduate',
                'emergency_contact_name' => $household1Head->full_name,
                'emergency_contact_number' => $household1Head->contact_number,
                'emergency_contact_relationship' => 'Father',
                
                // ✅ RESIDENCY INFO (BORN HERE)
                'residency_since' => now()->subYears(8), // Since birth
                'residency_type' => 'permanent',
                
                'household_number' => 'HH-POB-001',
                'is_household_head' => false,
                'registered_at' => now()->subMonths(rand(3, 10)),
                'registered_by' => $poblacionSecretary->id,
                'is_verified' => true,
                'verified_at' => now()->subDays(rand(5, 30)),
                'verified_by' => $poblacionCaptain->id,
                'user_id' => null,
                'is_active' => true,
                'status' => 'active',
                'has_violations' => false,
                'has_unpaid_dues' => false,
                'attends_assembly' => false,
            ]);
            echo "✓ Added child (8yo) to household HH-POB-001\n";

            // ✅ Child 2 (5yo)
            BarangayInhabitant::create([
                'barangay_id' => $poblacion->id,
                'registry_number' => $this->generateRegistryNumber($poblacion->id, $counter++),
                'last_name' => $household1Head->last_name,
                'first_name' => 'Anna',
                'middle_name' => 'Maria',
                'ext' => null,
                'house_number' => $household1Head->house_number,
                'zone_sitio' => $household1Head->zone_sitio,
                'place_of_birth' => 'Sablayan, Occidental Mindoro',
                'date_of_birth' => now()->subYears(5),
                'sex' => 'Female',
                'civil_status' => 'Single',
                'citizenship' => 'Filipino',
                'occupation' => 'Student',
                'educational_attainment' => 'Elementary Undergraduate',
                'emergency_contact_name' => $household1Head->full_name,
                'emergency_contact_number' => $household1Head->contact_number,
                'emergency_contact_relationship' => 'Father',
                
                // ✅ RESIDENCY INFO (BORN HERE)
                'residency_since' => now()->subYears(5),
                'residency_type' => 'permanent',
                
                'household_number' => 'HH-POB-001',
                'is_household_head' => false,
                'registered_at' => now()->subMonths(rand(3, 10)),
                'registered_by' => $poblacionSecretary->id,
                'is_verified' => true,
                'verified_at' => now()->subDays(rand(5, 30)),
                'verified_by' => $poblacionCaptain->id,
                'user_id' => null,
                'is_active' => true,
                'status' => 'active',
                'has_violations' => false,
                'has_unpaid_dues' => false,
                'attends_assembly' => false,
            ]);
            echo "✓ Added child (5yo) to household HH-POB-001\n";
        }

        // Get the second household from UserSeeder (HH-POB-002)
        $household2Head = BarangayInhabitant::where('barangay_id', $poblacion->id)
            ->where('household_number', 'HH-POB-002')
            ->where('is_household_head', true)
            ->first();

        if ($household2Head) {
            // ✅ Parent for household 2 (Senior Citizen)
            BarangayInhabitant::create([
                'barangay_id' => $poblacion->id,
                'registry_number' => $this->generateRegistryNumber($poblacion->id, $counter++),
                'last_name' => $household2Head->last_name,
                'first_name' => 'Rosa',
                'middle_name' => 'Reyes',
                'ext' => null,
                'house_number' => $household2Head->house_number,
                'zone_sitio' => $household2Head->zone_sitio,
                'place_of_birth' => 'San Jose, Occidental Mindoro',
                'date_of_birth' => now()->subYears(65),
                'sex' => 'Female',
                'civil_status' => 'Widowed',
                'citizenship' => 'Filipino',
                'occupation' => 'Retired',
                'educational_attainment' => 'Elementary Graduate',
                'contact_number' => '09171234581',
                'emergency_contact_name' => $household2Head->full_name,
                'emergency_contact_number' => $household2Head->contact_number,
                'emergency_contact_relationship' => 'Daughter',
                
                // ✅ RESIDENCY INFO (LONG-TIME RESIDENT)
                'residency_since' => now()->subYears(40),
                'residency_type' => 'permanent',
                
                'household_number' => 'HH-POB-002',
                'is_household_head' => false,
                'registered_at' => now()->subMonths(rand(3, 10)),
                'registered_by' => $poblacionSecretary->id,
                'is_verified' => true,
                'verified_at' => now()->subDays(rand(5, 30)),
                'verified_by' => $poblacionCaptain->id,
                'user_id' => null,
                'is_active' => true,
                'status' => 'active',
                'has_violations' => false,
                'has_unpaid_dues' => false,
                'attends_assembly' => true,
                'remarks' => 'Senior Citizen',
            ]);
            echo "✓ Added parent (senior citizen) to household HH-POB-002\n";

            // ✅ Sibling for household 2
            BarangayInhabitant::create([
                'barangay_id' => $poblacion->id,
                'registry_number' => $this->generateRegistryNumber($poblacion->id, $counter++),
                'last_name' => $household2Head->last_name,
                'first_name' => 'Carlos',
                'middle_name' => 'Santos',
                'ext' => null,
                'house_number' => $household2Head->house_number,
                'zone_sitio' => $household2Head->zone_sitio,
                'place_of_birth' => 'Sablayan, Occidental Mindoro',
                'date_of_birth' => now()->subYears(22),
                'sex' => 'Male',
                'civil_status' => 'Single',
                'citizenship' => 'Filipino',
                'occupation' => 'Driver',
                'educational_attainment' => 'High School Graduate',
                'contact_number' => '09171234582',
                'emergency_contact_name' => $household2Head->full_name,
                'emergency_contact_number' => $household2Head->contact_number,
                'emergency_contact_relationship' => 'Sibling',
                
                // ✅ RESIDENCY INFO (SINCE BIRTH)
                'residency_since' => now()->subYears(22),
                'residency_type' => 'permanent',
                
                'household_number' => 'HH-POB-002',
                'is_household_head' => false,
                'registered_at' => now()->subMonths(rand(3, 10)),
                'registered_by' => $poblacionSecretary->id,
                'is_verified' => true,
                'verified_at' => now()->subDays(rand(5, 30)),
                'verified_by' => $poblacionCaptain->id,
                'user_id' => null,
                'is_active' => true,
                'status' => 'active',
                'has_violations' => false,
                'has_unpaid_dues' => false,
                'attends_assembly' => true,
            ]);
            echo "✓ Added sibling to household HH-POB-002\n";
        }

        // ========================================
        // ADD SOLO INHABITANTS (Available for linking)
        // ========================================
        
        $soloInhabitants = [
            [
                'first_name' => 'Armando',
                'middle_name' => 'Santos',
                'last_name' => 'Diaz',
                'ext' => 'Sr.',
                'sex' => 'Male',
                'birth_year' => 1950,
                'civil_status' => 'Widowed',
                'occupation' => 'Retired',
                'education' => 'Elementary Graduate',
                'years_resided' => 50, // Long-time resident
            ],
            [
                'first_name' => 'Anna',
                'middle_name' => 'Mae',
                'last_name' => 'Villanueva',
                'ext' => null,
                'sex' => 'Female',
                'birth_year' => 1993,
                'civil_status' => 'Separated',
                'occupation' => 'Sales Clerk',
                'education' => 'High School Graduate',
                'years_resided' => 3, // Relatively new - NOT ELIGIBLE YET
            ],
            [
                'first_name' => 'Roberto',
                'middle_name' => 'Cruz',
                'last_name' => 'Gomez',
                'ext' => null,
                'sex' => 'Male',
                'birth_year' => 1985,
                'civil_status' => 'Single',
                'occupation' => 'Tricycle Driver',
                'education' => 'High School Graduate',
                'years_resided' => 10, // Long-time resident
            ],
            [
                'first_name' => 'Elena',
                'middle_name' => 'Reyes',
                'last_name' => 'Mercado',
                'ext' => null,
                'sex' => 'Female',
                'birth_year' => 1978,
                'civil_status' => 'Divorced',
                'occupation' => 'Sari-sari Store Owner',
                'education' => 'Vocational',
                'years_resided' => 8, // Long-time resident
            ],
        ];

        foreach ($soloInhabitants as $index => $person) {
            $purokNumber = 6 + $index;
            $birthDate = Carbon::create($person['birth_year'], rand(1, 12), rand(1, 28));
            $residencySince = now()->subYears($person['years_resided']);
            
            BarangayInhabitant::create([
                'barangay_id' => $poblacion->id,
                'registry_number' => $this->generateRegistryNumber($poblacion->id, $counter++),
                'first_name' => $person['first_name'],
                'middle_name' => $person['middle_name'],
                'last_name' => $person['last_name'],
                'ext' => $person['ext'],
                'house_number' => 'B-' . ($index + 1),
                'zone_sitio' => "Purok {$purokNumber}",
                'place_of_birth' => $index % 2 == 0 ? 'Calintaan, Occidental Mindoro' : 'Sablayan, Occidental Mindoro',
                'date_of_birth' => $birthDate,
                'sex' => $person['sex'],
                'civil_status' => $person['civil_status'],
                'citizenship' => 'Filipino',
                'occupation' => $person['occupation'],
                'educational_attainment' => $person['education'],
                'contact_number' => '09171234' . (583 + $index),
                
                // ✅ RESIDENCY INFO
                'residency_since' => $residencySince,
                'residency_type' => 'permanent',
                
                'household_number' => null, // Solo inhabitant
                'is_household_head' => false,
                'registered_at' => now()->subMonths(rand(6, 24)),
                'registered_by' => $poblacionSecretary->id,
                'is_verified' => true,
                'verified_at' => now()->subMonths(rand(1, 12)),
                'verified_by' => $poblacionCaptain->id,
                'user_id' => null, // ✅ Available for online registration
                'is_active' => true,
                'status' => 'active',
                'has_violations' => false,
                'has_unpaid_dues' => false,
                'attends_assembly' => $index % 2 == 0,
                'remarks' => $person['birth_year'] < 1960 ? 'Senior Citizen' : null,
            ]);
            
            $monthsResided = now()->diffInMonths($residencySince);
            $eligible = $monthsResided >= 6 ? '✅' : '⚠️';
            echo "{$eligible} Created solo inhabitant: {$person['first_name']} {$person['last_name']} ({$monthsResided} months)\n";
        }

        // ========================================
        // ADD UNVERIFIED INHABITANTS (Recently moved in)
        // ========================================
        
        $unverifiedInhabitants = [
            [
                'first_name' => 'Miguel',
                'middle_name' => 'Lopez',
                'last_name' => 'Torres',
                'sex' => 'Male',
                'birth_year' => 1995,
                'months_ago' => 2, // Recently moved - NOT ELIGIBLE
            ],
            [
                'first_name' => 'Sofia',
                'middle_name' => 'Cruz',
                'last_name' => 'Ramos',
                'sex' => 'Female',
                'birth_year' => 1998,
                'months_ago' => 1, // Very new - NOT ELIGIBLE
            ],
        ];

        foreach ($unverifiedInhabitants as $index => $person) {
            $purokNumber = 10 + $index;
            $birthDate = Carbon::create($person['birth_year'], rand(1, 12), rand(1, 28));
            $residencySince = now()->subMonths($person['months_ago']);
            
            BarangayInhabitant::create([
                'barangay_id' => $poblacion->id,
                'registry_number' => $this->generateRegistryNumber($poblacion->id, $counter++),
                'first_name' => $person['first_name'],
                'middle_name' => $person['middle_name'],
                'last_name' => $person['last_name'],
                'ext' => null,
                'house_number' => 'T-' . ($index + 1),
                'zone_sitio' => "Purok {$purokNumber}",
                'place_of_birth' => 'Manila',
                'date_of_birth' => $birthDate,
                'sex' => $person['sex'],
                'civil_status' => 'Single',
                'citizenship' => 'Filipino',
                'occupation' => 'New Resident',
                'educational_attainment' => 'College Undergraduate',
                'contact_number' => '09171234' . (590 + $index),
                
                // ✅ RESIDENCY INFO (NEW RESIDENT - NOT ELIGIBLE YET)
                'residency_since' => $residencySince,
                'residency_type' => 'temporary',
                
                'household_number' => null,
                'is_household_head' => false,
                'registered_at' => now()->subDays(rand(1, 30)),
                'registered_by' => $poblacionSecretary->id,
                'is_verified' => false, // ❌ NOT YET VERIFIED
                'verified_at' => null,
                'verified_by' => null,
                'user_id' => null,
                'is_active' => true,
                'status' => 'active',
                'has_violations' => false,
                'has_unpaid_dues' => false,
                'attends_assembly' => false,
                'remarks' => "Recently moved in ({$person['months_ago']} months ago) - pending verification",
            ]);
            echo "⏳ Created unverified inhabitant: {$person['first_name']} {$person['last_name']} ({$person['months_ago']} months)\n";
        }

        // ========================================
        // SUMMARY
        // ========================================
        
        $totalCount = BarangayInhabitant::where('barangay_id', $poblacion->id)->count();
        $verifiedCount = BarangayInhabitant::where('barangay_id', $poblacion->id)->where('is_verified', true)->count();
        $withAccountCount = BarangayInhabitant::where('barangay_id', $poblacion->id)->whereNotNull('user_id')->count();
        $householdCount = BarangayInhabitant::where('barangay_id', $poblacion->id)->where('is_household_head', true)->count();
        
        // ✅ Count eligible for document requests (6+ months residency)
        $eligibleCount = BarangayInhabitant::where('barangay_id', $poblacion->id)
            ->where('is_verified', true)
            ->whereRaw('TIMESTAMPDIFF(MONTH, residency_since, NOW()) >= 6')
            ->count();

        echo "\n========================================\n";
        echo "RBI REGISTRY SUMMARY (Poblacion)\n";
        echo "========================================\n";
        echo "Total Inhabitants: {$totalCount}\n";
        echo "  • Verified: {$verifiedCount}\n";
        echo "  • Unverified: " . ($totalCount - $verifiedCount) . "\n";
        echo "  • Eligible for Documents (6+ months): {$eligibleCount}\n";
        echo "  • With User Account: {$withAccountCount}\n";
        echo "  • Available for Linking: " . ($totalCount - $withAccountCount) . "\n";
        echo "Total Households: {$householdCount}\n";
        echo "========================================\n\n";
    }

    private function generateRegistryNumber($barangayId, $counter)
    {
        $barangayCode = str_pad($barangayId, 3, '0', STR_PAD_LEFT);
        $year = date('Y');
        $sequence = str_pad($counter, 5, '0', STR_PAD_LEFT);
        
        return "RBI-BGY{$barangayCode}-{$year}-{$sequence}";
    }
}