<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasEmailVerification;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, HasEmailVerification;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'barangay_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'contact_number',
        'birth_date',
        'gender',
        'address',
        'profile_photo',
        'is_active',
        'employee_id',
        'term_start',
        'term_end',
        'is_archived',
        'archived_at',
        'archived_by',
        'position_title',

        'email_verified_at', // ✅ ADD THIS
        'email_verification_token',
        'email_verification_token_expires_at',
        'email_verification_attempts',
        'email_verification_last_sent_at',


        'session_token',       // ✅ Make sure this is here
        'is_logged_in',        // ✅ And this
        'last_login_at',       // ✅ And this
        'last_activity_at',    // ✅ And this

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',

        'session_token', // Hide session token from serialization

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birth_date' => 'date',
        'last_login_at' => 'datetime',
        'term_start' => 'date',
        'term_end' => 'date',
        'is_active' => 'boolean',
        'is_archived' => 'boolean',

        'email_verification_token_expires_at' => 'datetime',
        'email_verification_last_sent_at' => 'datetime',


        // ✅ ADD THESE DATE CASTS
        'last_login_at' => 'datetime',
        'last_activity_at' => 'datetime',
        
    ];

    /**
     * Get the barangay that owns the user.
     */
    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    /**
     * Get the user's resident profile.
     */
    public function residentProfile()
    {
        return $this->hasOne(ResidentProfile::class);
    }

    /**
     * Get the user's document requests.
     */
    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }

    /**
     * Get complaints filed by this user.
     */
    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'complainant_id');
    }

    /**
     * Get complaints assigned to this user.
     */
    public function assignedComplaints()
    {
        return $this->hasMany(Complaint::class, 'assigned_to');
    }

    /**
     * Get business permits applied by this user.
     */
    public function businessPermits()
    {
        return $this->hasMany(BusinessPermit::class, 'applicant_id');
    }

    /**
     * Get terms served by this user.
     */
    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    /**
     * Get the current active term for this user.
     */
    public function currentTerm()
    {
        return $this->hasOne(Term::class)->where('is_active', true);
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute()
    {
        $name = trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
        return $this->suffix ? $name . ' ' . $this->suffix : $name;
    }

    /**
     * Get the user's initials.
     */
    public function getInitialsAttribute()
    {
        $firstInitial = $this->first_name ? substr($this->first_name, 0, 1) : '';
        $lastInitial = $this->last_name ? substr($this->last_name, 0, 1) : '';
        return strtoupper($firstInitial . $lastInitial);
    }

    /**
     * Check if user is a municipality admin.
     */
    public function isMunicipalityAdmin()
    {
        return $this->hasRole(['barangay-captain']);
    }

    /**
     * Check if user is an ABC president.
     */
    public function isAbcPresident()
    {
        return $this->hasRole('abc-president');
    }

    /**
     * Check if user is a barangay captain.
     */
    public function isBarangayCaptain()
    {
        return $this->hasRole('barangay-captain');
    }

    /**
     * Check if user is barangay staff (captain, secretary, or staff).
     */
    public function isBarangayStaff()
    {
        return $this->hasAnyRole(['barangay-captain', 'barangay-secretary', 'barangay-staff']);
    }

    /**
     * Check if user is a lupon member.
     */
    public function isLupon()
    {
        return $this->hasRole('lupon');
    }

    /**
     * Check if user is a resident.
     */
    public function isResident()
    {
        return $this->hasRole('resident');
    }

    /**
     * Check if user has access to a specific barangay.
     */
    public function canAccessBarangay($barangayId)
    {
        if ($this->isMunicipalityAdmin() || $this->isAbcPresident()) {
            return true;
        }
        
        return $this->barangay_id == $barangayId;
    }

    /**
     * Scope to get active users only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get non-archived users.
     */
    public function scopeNotArchived($query)
    {
        return $query->where('is_archived', false);
    }

    /**
     * Scope to get users by barangay.
     */
    public function scopeByBarangay($query, $barangayId)
    {
        return $query->where('barangay_id', $barangayId);
    }

    /**
     * Update last login timestamp.
     */
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Archive user (for end of term).
     */
    public function archive($reason = null)
    {
        $this->update([
            'is_archived' => true,
            'is_active' => false,
            
        'archived_at' => now(),
        'archived_by' => auth()->id(),
        ]);

        // Create archived term record if applicable
        if ($this->currentTerm) {
            $this->currentTerm->update([
                'is_active' => false,
                'is_archived' => true,
                'archived_at' => now(),
                'transition_notes' => $reason,
            ]);
        }
    }

    // Add these to your User model:

/**
 * Get documents processed by this user.
 */
public function processedDocuments()
{
    return $this->hasMany(DocumentRequest::class, 'processed_by');
}

/**
 * Get business permits processed by this user.
 */
public function processedPermits()
{
    return $this->hasMany(BusinessPermit::class, 'processed_by');
}

/**
 * Get residents verified by this user.
 */
public function verifiedResidents()
{
    return $this->hasMany(ResidentProfile::class, 'verified_by');
}



    /**
     * Scope for barangay councilors
     */
    public function scopeCouncilors($query, $barangayId = null)
    {
        $query->role('barangay-councilor');
        
        if ($barangayId) {
            $query->where('barangay_id', $barangayId);
        }
        
        return $query->where('is_active', true)
                     ->where('is_archived', false)
                     ->orderBy('councilor_number');
    }

    /**
     * Get committee display name
         */
    public function getCommitteeDisplayAttribute()
    {
        $committees = [
            'peace_order' => 'Committee on Peace and Order',
            'health_sanitation' => 'Committee on Health and Sanitation',
            'education' => 'Committee on Education',
            'agriculture' => 'Committee on Agriculture',
            'infrastructure' => 'Committee on Infrastructure',
            'environment' => 'Committee on Environment',
            'budget_finance' => 'Committee on Budget and Finance',
            'women_family' => 'Committee on Women and Family',
            'youth_sports' => 'Committee on Youth and Sports',
            'senior_pwd' => 'Committee on Senior Citizens and PWD',
            'livelihood' => 'Committee on Livelihood',
            'tourism_culture' => 'Committee on Tourism and Culture',
        ];

        return $committees[$this->committee] ?? $this->committee;
    }
    /**
     * Format councilor title
     */
    public function getCouncilorTitleAttribute()
    {
        if ($this->hasRole('barangay-councilor')) {
            return 'Kagawad ' . $this->full_name;
        }
        return $this->full_name;
    }

        /**
     * Get hearings presided by this user.
     */
    public function presidingHearings()
    {
        return $this->hasMany(ComplaintHearing::class, 'presiding_officer');
    }


// Add these methods to your App\Models\User.php

/**
 * Get complaints filed by this user (as complainant).
 */
public function complainantComplaints()
{
    return $this->hasMany(Complaint::class, 'complainant_id');
}


// Add these methods to your App\Models\User.php

/**
 * Get complaints where this user is a respondent (from JSON respondents field)
 */
public function respondentComplaints()
{
    return \App\Models\Complaint::whereNotNull('respondents')
        ->where(function($query) {
            $query->whereRaw("JSON_CONTAINS(respondents, JSON_OBJECT('user_id', ?))", [$this->id])
                  ->orWhereRaw("JSON_SEARCH(respondents, 'one', ?) IS NOT NULL", [$this->id]);
        });
}

/**
 * Get pending cases against this user (affects clearance)
 */
public function pendingCases()
{
    return $this->respondentComplaints()
                ->whereIn('status', ['received', 'assigned', 'in_process', 'mediation', 'hearing_scheduled']);
}

/**
 * Check if user has any pending cases (affects clearance)
 */
public function hasPendingCases()
{
    return $this->pendingCases()->exists();
}

/**
 * Get count of pending cases
 */
public function pendingCasesCount()
{
    return $this->pendingCases()->count();
}

/**
 * Check if user can request barangay clearance
 */
public function canRequestClearance()
{
    return !$this->hasPendingCases();
}

/**
 * Get clearance eligibility message
 */
public function getClearanceEligibilityMessage()
{
    if ($this->hasPendingCases()) {
        $count = $this->pendingCasesCount();
        return "You have {$count} pending case(s) in the barangay. Please settle these first before requesting a clearance.";
    }
    
    return "You are eligible to request a barangay clearance.";
}

/**
 * Check if user is RBI verified
 */
public function isRbiVerified()
{
    return $this->rbi_verified && $this->rbi_record_id;
}

/**
 * Get linked RBI record
 */
public function rbiRecord()
{
    return $this->belongsTo(BarangayInhabitant::class, 'rbi_record_id');
}

/**
 * Check service eligibility based on RBI verification
 */
public function canAccessService($serviceName = null)
{
    // If RBI verified, check full eligibility from RBI record
    if ($this->isRbiVerified() && $this->rbiRecord) {
        return $this->rbiRecord->checkClearanceEligibility();
    }
    
    // If not RBI verified, limited access
    return [
        'eligible' => false,
        'reasons' => ['Please visit the barangay office to register in our official RBI registry for full service access.'],
    ];
}


/**
 * Send the password reset notification.
 */
public function sendPasswordResetNotification($token)
{
    $this->notify(new ResetPasswordNotification($token));
}

}