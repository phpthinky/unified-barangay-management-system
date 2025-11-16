<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\DateHelper; // Add this import

class ResidentProfile extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'barangay_id',
        'rbi_inhabitant_id', // ✅ CRITICAL: Link to RBI record
        'purok_zone',
        'civil_status',
        'nationality',
        'religion',
        'occupation',
        'monthly_income',
        'educational_attainment',
        'emergency_contact_name',
        'emergency_contact_number',
        'emergency_contact_relationship',
        'id_documents',
        'uploaded_files',
        'is_verified',
        'verified_at',
        'verified_by',
        'verification_status', // ✅ NEW VALUES: 'rbi_linked', 'not_in_rbi', 'verified', 'pending_verification'
        'verification_notes',
        'is_household_head',
        'household_head_id',
        'residency_since',
        'residency_type',
        'is_pwd',
        'pwd_id_number',
        'is_senior_citizen',
        'is_solo_parent',
        'is_4ps_beneficiary',
    ];

    protected $casts = [
        'monthly_income' => 'decimal:2',
        'id_documents' => 'array',
        'uploaded_files' => 'array',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'is_household_head' => 'boolean',
        'residency_since' => 'date',
        'is_registered_voter' => 'boolean',
        'is_pwd' => 'boolean',
        'is_senior_citizen' => 'boolean',
        'is_solo_parent' => 'boolean',
        'is_4ps_beneficiary' => 'boolean',
    ];

    // ... (keep all your existing relationships and methods) ...

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function rbiInhabitant()
    {
        return $this->belongsTo(\App\Models\BarangayInhabitant::class, 'rbi_inhabitant_id');
    }
    /**
     * Get the household head (if not the head themselves).
     */
    public function householdHead()
    {
        return $this->belongsTo(ResidentProfile::class, 'household_head_id');
    }

    /**
     * Get household members (if this resident is the head).
     */
    public function householdMembers()
    {
        return $this->hasMany(ResidentProfile::class, 'household_head_id');
    }

    /**
     * Get the resident's age.
     */
    public function getAgeAttribute()
    {
        if (!$this->user->birth_date) {
            return null;
        }
        
        return $this->user->birth_date->age;
    }

    /**
     * Get the resident's full address.
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->user->address,
            $this->purok_zone ? "Purok/Zone {$this->purok_zone}" : null,
            $this->barangay->name,
        ]);
        
        return implode(', ', $parts);
    }

    /**
     * Check if resident has special classifications.
     */
    public function hasSpecialClassifications()
    {
        return $this->is_pwd || $this->is_senior_citizen || $this->is_solo_parent || $this->is_4ps_beneficiary;
    }

    /**
     * Get special classifications as array.
     */
    public function getSpecialClassificationsAttribute()
    {
        $classifications = [];
        
        if ($this->is_pwd) $classifications[] = 'PWD';
        if ($this->is_senior_citizen) $classifications[] = 'Senior Citizen';
        if ($this->is_solo_parent) $classifications[] = 'Solo Parent';
        if ($this->is_4ps_beneficiary) $classifications[] = '4Ps Beneficiary';
        
        return $classifications;
    }

    /**
     * Get uploaded ID document URLs.
     */
    public function getIdDocumentUrlsAttribute()
    {
        if (!$this->uploaded_files) {
            return [];
        }
        
        $urls = [];
        foreach ($this->uploaded_files as $file) {
            $urls[] = asset('uploads/documents/' . $file);
        }
        
        return $urls;
    }

    /**
     * Verify this resident profile.
     */
    public function verify(User $verifier, $notes = null)
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => $verifier->id,
            'verification_notes' => $notes,
        ]);
    }

    /**
     * Unverify this resident profile.
     */
    public function unverify($notes = null)
    {
        $this->update([
            'is_verified' => false,
            'verified_at' => null,
            'verified_by' => null,
            'verification_notes' => $notes,
        ]);
    }

    /**
     * Check if profile is complete (has all required fields filled)
     */
    public function isComplete()
    {
        $requiredFields = [
            'civil_status',
            'nationality',
            'religion',
            'occupation',
            'educational_attainment',
            'emergency_contact_name',
            'emergency_contact_number',
            'emergency_contact_relationship',
        ];

        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        // Check if user has required fields
        if (empty($this->user->birth_date) || empty($this->user->gender)) {
            return false;
        }

        return true;
    }

    /**
     * Calculate profile completion percentage
     */
    public function getCompletionPercentageAttribute()
    {
        $totalFields = 10; // Total required fields
        $completedFields = 0;

        $profileFields = [
            'civil_status',
            'nationality',
            'religion',
            'occupation',
            'educational_attainment',
            'emergency_contact_name',
            'emergency_contact_number',
            'emergency_contact_relationship',
        ];

        $userFields = [
            'birth_date',
            'gender'
        ];

        foreach ($profileFields as $field) {
            if (!empty($this->$field)) {
                $completedFields++;
            }
        }

        foreach ($userFields as $field) {
            if (!empty($this->user->$field)) {
                $completedFields++;
            }
        }

        return round(($completedFields / $totalFields) * 100);
    }

    /**
     * Scope to get verified residents only.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to get unverified residents.
     */
    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    /**
     * Scope to get residents by barangay.
     */
    public function scopeByBarangay($query, $barangayId)
    {
        return $query->where('barangay_id', $barangayId);
    }

    /**
     * Scope to get household heads only.
     */
    public function scopeHouseholdHeads($query)
    {
        return $query->where('is_household_head', true);
    }

    /**
     * Scope to get senior citizens.
     */
    public function scopeSeniorCitizens($query)
    {
        return $query->where('is_senior_citizen', true);
    }

    /**
     * Scope to get PWDs.
     */
    public function scopePwds($query)
    {
        return $query->where('is_pwd', true);
    }

    /**
     * Scope to get 4Ps beneficiaries.
     */
    public function scope4PsBeneficiaries($query)
    {
        return $query->where('is_4ps_beneficiary', true);
    }

    /**
     * Scope to get solo parents.
     */
    public function scopeSoloParents($query)
    {
        return $query->where('is_solo_parent', true);
    }


    /**
     * ✅ USE HELPER - Consistent calculation across all models
     */
    public function getResidencyMonthsAttribute()
    {
        return DateHelper::calculateMonthsBetween($this->residency_since);
    }

    public function getRemainingMonthsAttribute()
    {
        return DateHelper::getRemainingMonths($this->residency_since);
    }

    public function meetsResidencyRequirement()
    {
        return DateHelper::meetsResidencyRequirement($this->residency_since);
    }

/**
 * Get eligibility date (when 6 months is reached)
 */
public function getEligibilityDateAttribute()
{
    if (!$this->residency_since) {
        return null;
    }
    
    return \Carbon\Carbon::parse($this->residency_since)->addMonths(6);
}

/**
 * Get formatted residency status for display
 */
public function getResidencyStatusAttribute()
{
    if (!$this->residency_since) {
        return 'No residency date set';
    }

    $months = $this->residency_months;
    
    if ($months >= 6) {
        return "✅ Eligible ({$months} months)";
    }
    
    $remaining = $this->remaining_months;
    $eligibleDate = $this->eligibility_date->format('M d, Y');
    
    return "⏳ {$months} months ({$remaining} more needed, eligible on {$eligibleDate})";
}




    // ✅ NEW ELIGIBILITY CHECKING METHODS

    /**
     * Check eligibility for requesting services/documents
     * This is where ALL eligibility validation happens now
     */
    public function checkServiceEligibility()
    {
        $reasons = [];

        // 1. ✅ Must have RBI record linked
        if (!$this->rbi_inhabitant_id) {
            return [
                'eligible' => false,
                'reasons' => ['You must be registered in the Barangay RBI registry. Please visit the barangay office to register.'],
            ];
        }

        // 2. ✅ Get eligibility from RBI record (this includes all checks)
        $rbiRecord = $this->rbiInhabitant;
        
        if (!$rbiRecord) {
            return [
                'eligible' => false,
                'reasons' => ['RBI record not found. Please contact barangay office.'],
            ];
        }

        // 3. ✅ Use RBI's comprehensive eligibility check
        return $rbiRecord->checkClearanceEligibility();
    }

    /**
     * Check if can request specific document type
     */
    public function canRequestDocument($documentTypeSlug = null)
    {
        // All document requests now require full eligibility check
        return $this->checkServiceEligibility();
    }

    /**
     * Get eligibility status badge for display
     */
    public function getEligibilityBadge()
    {
        $eligibility = $this->checkServiceEligibility();
        
        if ($eligibility['eligible']) {
            return '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Eligible for Services</span>';
        }
        
        return '<span class="badge bg-warning"><i class="fas fa-exclamation-triangle"></i> Not Eligible</span>';
    }

    /**
     * Get formatted eligibility reasons
     */
    public function getEligibilityReasonsFormatted()
    {
        $eligibility = $this->checkServiceEligibility();
        
        if ($eligibility['eligible']) {
            return '<span class="text-success">✓ You are eligible to request barangay documents.</span>';
        }
        
        $html = '<div class="alert alert-warning"><strong>You cannot request documents yet:</strong><ul class="mb-0 mt-2">';
        foreach ($eligibility['reasons'] as $reason) {
            $html .= '<li>' . htmlspecialchars($reason) . '</li>';
        }
        $html .= '</ul></div>';
        
        return $html;
    }

    // ... (keep all your other existing methods) ...
}