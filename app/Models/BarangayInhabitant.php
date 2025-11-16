<?php
// FILE: app/Models/BarangayInhabitant.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Helpers\DateHelper; // Add this import

class BarangayInhabitant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'barangay_id',
        'registry_number',
        'last_name',
        'first_name',
        'middle_name',
        'ext',
        'house_number',
        'zone_sitio',
        'place_of_birth',
        'date_of_birth',
        'sex',
        'civil_status',
        'citizenship',
        'occupation',
        'educational_attainment',
        'contact_number',
        'emergency_contact_name',
        'emergency_contact_number',
        'emergency_contact_relationship',
        'residency_since',
        'residency_type',
        'cedula_number',
        'certificate_of_residency_number',
        'proof_of_residency_file',
        'household_number',
        'is_household_head',
        'registered_at',
        'registered_by',
        'is_verified',
        'verified_at',
        'verified_by',
        'user_id',
        'is_active',
        'status',
        'photo_path',
        'has_violations',
        'violation_details',
        'has_unpaid_dues',
        'unpaid_dues_details',
        'attends_assembly',
        'last_assembly_attended',
        'remarks',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'residency_since' => 'date',
        'registered_at' => 'datetime',
        'verified_at' => 'datetime',
        'last_assembly_attended' => 'date',
        'is_household_head' => 'boolean',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'has_violations' => 'boolean',
        'has_unpaid_dues' => 'boolean',
        'attends_assembly' => 'boolean',
    ];

    /**
     * ✅ AUTO-VERIFY BARANGAY-ENCODED RECORDS
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Auto-verify if created by barangay staff
            if (auth()->check() && auth()->user()->isBarangayStaff()) {
                $model->is_verified = true;
                $model->verified_at = now();
                $model->verified_by = auth()->id();
            }
            
            // Generate registry number if not set
            if (empty($model->registry_number)) {
                $model->registry_number = $model->generateRegistryNumber();
            }
        });
    }

    // Relationships
    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function householdMembers()
    {
        return $this->hasMany(BarangayInhabitant::class, 'household_number', 'household_number')
                    ->where('barangay_id', $this->barangay_id);
    }

    // Scopes
    public function scopeByBarangay($query, $barangayId)
    {
        return $query->where('barangay_id', $barangayId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    public function scopeHouseholdHeads($query)
    {
        return $query->where('is_household_head', true);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        $name = trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
        return $this->ext ? "{$name} {$this->ext}" : $name;
    }

    public function getAgeAttribute()
    {
        if (!$this->date_of_birth) {
            return null;
        }
        return $this->date_of_birth->age;
    }

    public function getFullAddressAttribute()
    {
        $address = $this->house_number ? "House No. {$this->house_number}, " : "";
        return $address . $this->zone_sitio;
    }

    public function isVerified(): bool
    {
        return $this->is_verified === true;
    }

    public function getVerificationBadge(): string
    {
        if ($this->is_verified) {
            return '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Verified</span>';
        }
        
        return '<span class="badge bg-warning"><i class="fas fa-clock"></i> Pending</span>';
    }



  /**
     * ✅ USE HELPER - No more duplicate calculation logic
     */
    public function getMonthsResided(): int
    {
        return DateHelper::calculateMonthsBetween($this->residency_since);
    }

    public function getResidencyMonthsAttribute()
    {
        return $this->getMonthsResided();
    }

    public function meetsResidencyRequirement(): bool
    {
        return DateHelper::meetsResidencyRequirement($this->residency_since);
    }

    public function getEligibleDate(): ?\Carbon\Carbon
    {
        return DateHelper::getEligibleDate($this->residency_since);
    }





    public function generateRegistryNumber()
    {
        $barangayCode = str_pad($this->barangay_id, 3, '0', STR_PAD_LEFT);
        $year = date('Y');
        $lastNumber = static::where('barangay_id', $this->barangay_id)
                           ->whereYear('created_at', $year)
                           ->count() + 1;
        
        $sequence = str_pad($lastNumber, 5, '0', STR_PAD_LEFT);
        
        return "RBI-BGY{$barangayCode}-{$year}-{$sequence}";
    }

    public function linkToUser(User $user)
    {
        $this->update([
            'user_id' => $user->id,
        ]);
    }


    /**
     * ✅ ELIGIBILITY CHECK - ONLY called during SERVICE REQUESTS
     */
    public function checkClearanceEligibility()
    {
       $reasons = [];
    
    if (!$this->is_verified) {
        $reasons[] = 'Profile not yet verified by barangay officials';
    }
    
    if (!$this->meetsResidencyRequirement()) {
        $monthsResided = $this->getMonthsResided(); // This now returns whole number
        $monthsNeeded = 6 - $monthsResided;
        $eligibleDate = $this->getEligibleDate()->format('F d, Y');
        $reasons[] = "Residency requirement not met (resided for {$monthsResided} months, needs {$monthsNeeded} more months). Will be eligible on: {$eligibleDate}";
    }
        
        $pendingComplaints = $this->getPendingComplaintsCount();
        
        if ($pendingComplaints > 0) {
            $reasons[] = "Has {$pendingComplaints} pending complaint case(s) - must resolve before requesting clearance";
        }
        
        if ($this->has_violations) {
            $reasons[] = "Has barangay violations: {$this->violation_details}";
        }
        
        if ($this->has_unpaid_dues) {
            $reasons[] = "Has unpaid dues: {$this->unpaid_dues_details}";
        }
        
        if (!$this->attends_assembly) {
            $reasons[] = 'Must attend barangay assembly meetings';
        }
        
        if ($this->status !== 'active' || !$this->is_active) {
            $reasons[] = 'Registry status is not active';
        }
        
        return [
            'eligible' => empty($reasons),
            'reasons' => $reasons,
        ];
    }

    public function getPendingComplaintsCount()
    {
        if (!$this->user_id) {
            return 0;
        }
        
        if (!class_exists('\App\Models\Complaint')) {
            return 0;
        }
        
        try {
            return \App\Models\Complaint::where('barangay_id', $this->barangay_id)
                ->where(function($q) {
                    $q->whereJsonContains('respondents', ['user_id' => $this->user_id])
                      ->orWhere(function($q2) {
                          $q2->whereRaw("JSON_SEARCH(respondents, 'one', ?) IS NOT NULL", [$this->full_name]);
                      });
                })
                ->whereIn('status', ['received', 'assigned', 'in_process', 'mediation', 'hearing_scheduled'])
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function canRequestDocument($documentTypeSlug)
    {
        $requiresClearance = [
            'barangay-clearance',
            'certificate-of-good-moral',
            'business-clearance',
            'certificate-of-residency',
        ];
        
        if (in_array($documentTypeSlug, $requiresClearance)) {
            return $this->checkClearanceEligibility();
        }
        
        if (!$this->is_verified) {
            return [
                'eligible' => false,
                'reasons' => ['Profile must be verified first'],
            ];
        }
        
        if ($this->status !== 'active' || !$this->is_active) {
            return [
                'eligible' => false,
                'reasons' => ['Registry status must be active'],
            ];
        }
        
        return [
            'eligible' => true,
            'reasons' => [],
        ];
    }

// In your BarangayInhabitant model

// ADD THESE METHODS TO: app/Models/BarangayInhabitant.php

/**
 * ✅ ENHANCED: Comprehensive duplicate check with fuzzy matching
 * Handles cases where middle name or suffix might differ
 */
public static function personHasOnlineAccount($firstName, $lastName, $birthDate, $barangayId, $middleName = null, $suffix = null)
{
    $cleanFirstName = trim($firstName);
    $cleanLastName = trim($lastName);
    $cleanMiddleName = $middleName ? trim($middleName) : null;
    $cleanSuffix = $suffix ? trim($suffix) : null;
    
    // 1. ✅ EXACT MATCH: First + Middle + Last + Birth Date + Suffix
    $exactUser = User::where('barangay_id', $barangayId)
        ->where('first_name', 'LIKE', $cleanFirstName)
        ->where('last_name', 'LIKE', $cleanLastName)
        ->whereDate('birth_date', $birthDate)
        ->whereHas('roles', function($query) {
            $query->where('name', 'resident');
        })
        ->when($cleanMiddleName, function($query) use ($cleanMiddleName) {
            $query->where('middle_name', 'LIKE', $cleanMiddleName);
        })
        ->when($cleanSuffix, function($query) use ($cleanSuffix) {
            $query->where('suffix', 'LIKE', $cleanSuffix);
        })
        ->first();

    if ($exactUser) {
        return [
            'exists' => true,
            'type' => 'exact_match',
            'user' => $exactUser,
            'message' => "An account already exists for {$exactUser->full_name} (born " . $exactUser->birth_date->format('M d, Y') . ")."
        ];
    }

    // 2. ✅ FUZZY MATCH: Same First + Last + Birth Date (ignore middle/suffix differences)
    $fuzzyUser = User::where('barangay_id', $barangayId)
        ->where('first_name', 'LIKE', $cleanFirstName)
        ->where('last_name', 'LIKE', $cleanLastName)
        ->whereDate('birth_date', $birthDate)
        ->whereHas('roles', function($query) {
            $query->where('name', 'resident');
        })
        ->first();

    if ($fuzzyUser) {
        // ✅ Check if it's truly different or just missing middle name
        $inputFullName = trim($cleanFirstName . ' ' . ($cleanMiddleName ?? '') . ' ' . $cleanLastName . ' ' . ($cleanSuffix ?? ''));
        $existingFullName = $fuzzyUser->full_name;
        
        return [
            'exists' => true,
            'type' => 'fuzzy_match',
            'user' => $fuzzyUser,
            'message' => "A similar account exists for {$existingFullName} with the same birth date (" . $fuzzyUser->birth_date->format('M d, Y') . "). This might be you with a different middle name or suffix."
        ];
    }

    // 3. ✅ Check ResidentProfile through User
    $existingProfile = ResidentProfile::where('barangay_id', $barangayId)
        ->whereHas('user', function($query) use ($cleanFirstName, $cleanLastName, $birthDate) {
            $query->where('first_name', 'LIKE', $cleanFirstName)
                  ->where('last_name', 'LIKE', $cleanLastName)
                  ->whereDate('birth_date', $birthDate);
        })
        ->first();

    if ($existingProfile) {
        $user = $existingProfile->user;
        return [
            'exists' => true,
            'type' => 'resident_profile',
            'user' => $user,
            'message' => "An account already exists for {$user->full_name} (born " . $user->birth_date->format('M d, Y') . ")."
        ];
    }

    // 4. ✅ Check RBI records that are already linked
    $linkedRbiRecord = static::where('barangay_id', $barangayId)
        ->where('first_name', 'LIKE', $cleanFirstName)
        ->where('last_name', 'LIKE', $cleanLastName)
        ->whereDate('date_of_birth', $birthDate)
        ->whereNotNull('user_id')
        ->first();

    if ($linkedRbiRecord) {
        $linkedUser = User::find($linkedRbiRecord->user_id);
        if ($linkedUser) {
            return [
                'exists' => true,
                'type' => 'linked_rbi',
                'user' => $linkedUser,
                'message' => "This RBI record is already linked to an account for {$linkedUser->full_name}."
            ];
        }
    }

    // 5. ✅ Check ResidentProfile with RBI link
    $rbiLinkedProfile = ResidentProfile::where('barangay_id', $barangayId)
        ->whereNotNull('rbi_inhabitant_id')
        ->whereHas('rbiInhabitant', function($query) use ($cleanFirstName, $cleanLastName, $birthDate) {
            $query->where('first_name', 'LIKE', $cleanFirstName)
                  ->where('last_name', 'LIKE', $cleanLastName)
                  ->whereDate('date_of_birth', $birthDate);
        })
        ->first();

    if ($rbiLinkedProfile) {
        $user = $rbiLinkedProfile->user;
        return [
            'exists' => true,
            'type' => 'rbi_linked_profile',
            'user' => $user,
            'message' => "An account already exists for {$user->full_name} with an RBI record."
        ];
    }

    return ['exists' => false];
}

/**
 * Find available RBI record for registration
 */
public static function findVerifiedRecord($firstName, $lastName, $birthDate, $barangayId, $middleName = null)
{
    // First check if person already has an account
    $accountCheck = static::personHasOnlineAccount($firstName, $lastName, $birthDate, $barangayId, $middleName);
    if ($accountCheck['exists']) {
        return null;
    }

    $cleanFirstName = trim($firstName);
    $cleanLastName = trim($lastName);
    $cleanMiddleName = $middleName ? trim($middleName) : null;

    // Look for available RBI record (verified and not linked)
    $query = static::where('barangay_id', $barangayId)
        ->where('is_verified', true)
        ->where('first_name', 'LIKE', $cleanFirstName)
        ->where('last_name', 'LIKE', $cleanLastName)
        ->whereDate('date_of_birth', $birthDate)
        ->whereNull('user_id');

    // If middle name provided, prefer exact match but don't require it
    if ($cleanMiddleName) {
        // Try exact match first
        $exactMatch = (clone $query)->where('middle_name', 'LIKE', $cleanMiddleName)->first();
        if ($exactMatch) {
            return $exactMatch;
        }
    }

    // Return any match with same first + last + birth date
    return $query->first();
}

/**
 * Check if email is already used in this barangay
 */
public static function isEmailUsedInBarangay($email, $barangayId)
{
    return User::where('email', $email)
        ->where('barangay_id', $barangayId)
        ->whereHas('roles', function($query) {
            $query->where('name', 'resident');
        })
        ->exists();
}

}