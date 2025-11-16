<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_number',
        'complainant_id',
        'barangay_id',
        'complaint_type_id',
        'subject',
        'description',
        'form_data',
        'incident_date',
        'incident_location',
        'uploaded_files',
        'respondents',
        'status',
        'priority',
        'received_at',
        'assigned_at',
        'resolved_at',
        'closed_at',
        'assigned_to',
        'assigned_role',
        'assignment_notes',
        'resolution_details',
        'resolution_type',
        'resolved_by',
        'resolution_files',
        'requires_follow_up',
        'follow_up_date',
        'follow_up_notes',
        
        // Workflow fields
        'workflow_status',
        'secretary_reviewed_at',
        'secretary_reviewed_by',
        'secretary_notes',
        'captain_approved_at',
        'captain_approved_by',
        'captain_notes',
        'summons_attempt',
        'summons_1_issued_date',
        'summons_1_return_date',
        'summons_1_served',
        'summons_2_issued_date',
        'summons_2_return_date',
        'summons_2_served',
        'summons_3_issued_date',
        'summons_3_return_date',
        'summons_3_served',
        'summons_all_failed',
        'respondent_appeared_at',
        'appearance_notes',
        'captain_mediation_start',
        'captain_mediation_deadline',
        'captain_mediation_extended',
        'settled_by_captain_at',
        'settlement_terms',
        'captain_mediation_notes',
        'assigned_to_lupon_at',
        'assigned_lupon_id',
        'lupon_assignment_notes',
        'current_hearing_number',
        'total_hearings_conducted',
        'lupon_resolved_at',
        'lupon_resolution',
        'lupon_resolution_notes',
        'certificate_issued_at',
        'certificate_number',
        'referred_to',
        'certificate_notes',
        'days_in_process',
    ];

    protected $casts = [
        'form_data' => 'array',
        'uploaded_files' => 'array',
        'respondents' => 'array',
        'resolution_files' => 'array',
        'incident_date' => 'datetime',
        'received_at' => 'datetime',
        'assigned_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'follow_up_date' => 'date',
        'requires_follow_up' => 'boolean',
        
        // ✅ FIX: Proper cast syntax for workflow fields
        'secretary_reviewed_at' => 'datetime',
        'captain_approved_at' => 'datetime',
        'summons_1_issued_date' => 'date',
        'summons_1_return_date' => 'date',
        'summons_1_served' => 'boolean',
        'summons_2_issued_date' => 'date',
        'summons_2_return_date' => 'date',
        'summons_2_served' => 'boolean',
        'summons_3_issued_date' => 'date',
        'summons_3_return_date' => 'date',
        'summons_3_served' => 'boolean',
        'summons_all_failed' => 'boolean',
        'respondent_appeared_at' => 'datetime',
        'captain_mediation_start' => 'datetime',
        'captain_mediation_deadline' => 'date',
        'captain_mediation_extended' => 'boolean',
        'settled_by_captain_at' => 'datetime',
        'assigned_to_lupon_at' => 'datetime',
        'lupon_resolved_at' => 'datetime',
        'certificate_issued_at' => 'datetime',
    ];

    // ... rest of your model code (relationships, scopes, etc.)


    /**
     * Get the complainant (person who filed)
     */
    public function complainant()
    {
        return $this->belongsTo(User::class, 'complainant_id');
    }

    /**
     * Get the barangay
     */
    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }
    // app/Models/Complaint.php

/**
 * Scope to filter by barangay
 */
// app/Models/Complaint.php

// Add this with your other scope methods (after line 120 or with other scopes)



/**
 * Get hearings for this complaint
 * REQUIRED for resident views
 */
public function hearings()
{
    return $this->hasMany(\App\Models\ComplaintHearing::class)
                ->orderBy('scheduled_date', 'desc');
}

public function latestHearing()
{
    return $this->hasOne(\App\Models\ComplaintHearing::class)
                ->latestOfMany('scheduled_date');
}
    /**
     * Get the complaint type
     */
    public function complaintType()
    {
        return $this->belongsTo(ComplaintType::class);
    }

    /**
     * Get the assigned official
     */
    public function assignedOfficial()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the resolver
     */
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Get respondent users (registered residents only)
     * Returns collection of User models
     */
    public function getRespondentUsersAttribute()
    {
        if (!$this->respondents) {
            return collect();
        }

        $userIds = collect($this->respondents)
            ->where('type', 'registered')
            ->pluck('user_id')
            ->filter()
            ->unique();

        if ($userIds->isEmpty()) {
            return collect();
        }

        return User::whereIn('id', $userIds)->get();
    }

    /**
     * Get all respondent info (registered and unregistered)
     */
    public function getRespondentInfoAttribute()
    {
        if (!$this->respondents) {
            return collect();
        }

        return collect($this->respondents)->map(function($respondent) {
            $data = [
                'type' => $respondent['type'] ?? 'unknown',
                'name' => $respondent['name'] ?? 'Unknown',
                'address' => $respondent['address'] ?? 'Not provided',
                'contact' => $respondent['contact'] ?? 'Not provided',
            ];

            // Add user object if registered
            if (isset($respondent['user_id']) && $respondent['user_id']) {
                $data['user'] = User::find($respondent['user_id']);
                $data['is_registered'] = true;
            } else {
                $data['user'] = null;
                $data['is_registered'] = false;
                $data['description'] = $respondent['description'] ?? '';
            }

            return $data;
        });
    }

    /**
     * Get respondent display names (comma-separated)
     */
    public function getRespondentNamesAttribute()
    {
        if (!$this->respondents) {
            return 'Unknown';
        }

        return collect($this->respondents)
            ->pluck('name')
            ->filter()
            ->join(', ') ?: 'Unknown';
    }

    /**
     * Check if has any registered respondents
     */
    public function hasRegisteredRespondents()
    {
        if (!$this->respondents) {
            return false;
        }

        return collect($this->respondents)
            ->where('type', 'registered')
            ->isNotEmpty();
    }

    /**
     * Check if has any unregistered respondents
     */
    public function hasUnregisteredRespondents()
    {
        if (!$this->respondents) {
            return false;
        }

        return collect($this->respondents)
            ->whereIn('type', ['unregistered', 'unknown'])
            ->isNotEmpty();
    }

    /**
     * Check if complaint is pending (not resolved/closed)
     */
    public function isPending()
    {
        return !in_array($this->status, ['resolved', 'closed', 'dismissed']);
    }

    /**
     * Check if complaint is resolved
     */
    public function isResolved()
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

/*
**
 * Scope: Filter by barangay
 */
public function scopeByBarangay($query, $barangayId)
{
    return $query->where('barangay_id', $barangayId);
}

/**
 * Scope: Active complaints (not closed/dismissed)
 */
public function scopeActive($query)
{
    return $query->whereNotIn('workflow_status', ['closed', 'dismissed', 'settled_by_captain', 'resolved_by_lupon', 'certificate_issued']);
}

    /**
     * Scope: Pending complaints
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['received', 'assigned', 'in_process', 'mediation', 'hearing_scheduled']);
    }

    /**
     * Scope: Resolved complaints
     */
    public function scopeResolved($query)
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }

    /**
     * Scope: By status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: By priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope: High priority
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    /**
     * Scope: Assigned to user
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope: Unassigned
     */
    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    /**
     * Scope: Requires follow up
     */
    public function scopeRequiresFollowUp($query)
    {
        return $query->where('requires_follow_up', true);
    }

    /**
     * Scope: Search by complaint number or subject
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('complaint_number', 'like', "%{$search}%")
              ->orWhere('subject', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'received' => 'info',
            'assigned' => 'primary',
            'in_process' => 'warning',
            'mediation' => 'warning',
            'hearing_scheduled' => 'warning',
            'resolved' => 'success',
            'closed' => 'secondary',
            'dismissed' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get priority badge color
     */
    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => 'secondary',
            'medium' => 'info',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Add respondent to complaint
     */
    public function addRespondent(array $respondentData)
    {
        $respondents = $this->respondents ?? [];
        $respondents[] = $respondentData;
        $this->respondents = $respondents;
        $this->save();
    }

    /**
     * Remove respondent by index
     */
    public function removeRespondent($index)
    {
        $respondents = $this->respondents ?? [];
        unset($respondents[$index]);
        $this->respondents = array_values($respondents); // Re-index
        $this->save();
    }

    /**
     * Update respondent by index
     */
    public function updateRespondent($index, array $respondentData)
    {
        $respondents = $this->respondents ?? [];
        if (isset($respondents[$index])) {
            $respondents[$index] = array_merge($respondents[$index], $respondentData);
            $this->respondents = $respondents;
            $this->save();
        }
    }

    /**
     * Link unregistered respondent to registered user
     */
    public function linkRespondentToUser($index, $userId)
    {
        $respondents = $this->respondents ?? [];
        if (isset($respondents[$index])) {
            $user = User::find($userId);
            if ($user) {
                $respondents[$index] = [
                    'type' => 'registered',
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'address' => $user->address,
                    'contact' => $user->contact_number,
                ];
                $this->respondents = $respondents;
                $this->save();
            }
        }
    }


     /**
     * Get status badge attributes
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'received' => ['text' => 'Received', 'class' => 'badge-info'],
            'assigned' => ['text' => 'Assigned', 'class' => 'badge-primary'],
            'in_process' => ['text' => 'In Process', 'class' => 'badge-warning'],
            'mediation' => ['text' => 'Mediation', 'class' => 'badge-warning'],
            'hearing_scheduled' => ['text' => 'Hearing Scheduled', 'class' => 'badge-warning'],
            'resolved' => ['text' => 'Resolved', 'class' => 'badge-success'],
            'closed' => ['text' => 'Closed', 'class' => 'badge-secondary'],
            'dismissed' => ['text' => 'Dismissed', 'class' => 'badge-danger'],
        ];

        return $badges[$this->status] ?? ['text' => 'Unknown', 'class' => 'badge-secondary'];
    }

    /**
     * Get priority badge attributes
     */
    public function getPriorityBadgeAttribute()
    {
        $badges = [
            'low' => ['text' => 'Low', 'class' => 'badge-secondary'],
            'medium' => ['text' => 'Medium', 'class' => 'badge-info'],
            'high' => ['text' => 'High', 'class' => 'badge-warning'],
            'urgent' => ['text' => 'Urgent', 'class' => 'badge-danger'],
        ];

        return $badges[$this->priority] ?? ['text' => 'Unknown', 'class' => 'badge-secondary'];
    }
    // app/Models/Complaint.php

/**
 * Get workflow status badge color
 */
public function getWorkflowStatusColorAttribute()
{
    return match($this->workflow_status) {
        'pending_review' => 'info',
        'for_captain_review' => 'warning',
        'approved' => 'success',
        'dismissed' => 'danger',
        '1st_summons_issued', '2nd_summons_issued', '3rd_summons_issued' => 'primary',
        'respondent_appeared' => 'info',
        'summons_failed' => 'danger',
        'captain_mediation' => 'secondary',
        'settled_by_captain', 'resolved_by_lupon' => 'success',
        'for_lupon' => 'warning',
        '1st_hearing_scheduled', '2nd_hearing_scheduled', '3rd_hearing_scheduled' => 'primary',
        '1st_hearing_ongoing', '2nd_hearing_ongoing', '3rd_hearing_ongoing' => 'warning',
        '1st_hearing_completed', '2nd_hearing_completed', '3rd_hearing_completed' => 'info',
        'for_certificate' => 'warning',
        'certificate_issued' => 'secondary',
        'closed' => 'secondary',
        default => 'secondary'
    };
}

/**
 * Get workflow status label
 */
public function getWorkflowStatusLabelAttribute()
{
    return str_replace('_', ' ', ucwords($this->workflow_status));
}

/**
 * Build timeline array for display
 */
private function buildTimeline(Complaint $complaint)
{
    $timeline = [];
    
    $timeline[] = [
        'date' => $complaint->created_at, 
        'icon' => '📄', 
        'title' => 'Complaint Filed', 
        'desc' => 'By: ' . $complaint->complainant->name
    ];
    
    if ($complaint->secretary_reviewed_at) {
        $timeline[] = [
            'date' => $complaint->secretary_reviewed_at, 
            'icon' => '👁️', 
            'title' => 'Reviewed by Secretary', 
            'desc' => User::find($complaint->secretary_reviewed_by)->name ?? 'Secretary'
        ];
    }
    
    if ($complaint->captain_approved_at) {
        $icon = $complaint->workflow_status === 'dismissed' ? '❌' : '✅';
        $title = $complaint->workflow_status === 'dismissed' ? 'Dismissed by Captain' : 'Approved by Captain';
        $timeline[] = [
            'date' => $complaint->captain_approved_at, 
            'icon' => $icon, 
            'title' => $title, 
            'desc' => User::find($complaint->captain_approved_by)->name ?? 'Captain'
        ];
    }
    
    for ($i = 1; $i <= 3; $i++) {
        $issuedDate = $complaint->{"summons_{$i}_issued_date"};
        $returnDate = $complaint->{"summons_{$i}_return_date"};
        
        if ($issuedDate) {
            $timeline[] = [
                'date' => \Carbon\Carbon::parse($issuedDate), 
                'icon' => '📨', 
                'title' => "Summons #{$i} Issued", 
                'desc' => 'Return date: ' . ($returnDate ? \Carbon\Carbon::parse($returnDate)->format('M d, Y') : 'N/A')
            ];
        }
    }
    
    if ($complaint->respondent_appeared_at) {
        $timeline[] = [
            'date' => $complaint->respondent_appeared_at, 
            'icon' => '✅', 
            'title' => 'Respondent Appeared', 
            'desc' => ''
        ];
    }
    
    if ($complaint->captain_mediation_start) {
        $timeline[] = [
            'date' => $complaint->captain_mediation_start, 
            'icon' => '🤝', 
            'title' => 'Captain Mediation Started', 
            'desc' => 'Deadline: ' . ($complaint->captain_mediation_deadline ? \Carbon\Carbon::parse($complaint->captain_mediation_deadline)->format('M d, Y') : 'N/A')
        ];
    }
    
    if ($complaint->settled_by_captain_at) {
        $timeline[] = [
            'date' => $complaint->settled_by_captain_at, 
            'icon' => '✅', 
            'title' => 'Settlement Reached', 
            'desc' => 'Case RESOLVED'
        ];
    }
    
    if ($complaint->assigned_to_lupon_at) {
        $timeline[] = [
            'date' => $complaint->assigned_to_lupon_at, 
            'icon' => '⚖️', 
            'title' => 'Assigned to Lupon', 
            'desc' => User::find($complaint->assigned_lupon_id)->name ?? ''
        ];
    }
    
    if ($complaint->certificate_issued_at) {
        $timeline[] = [
            'date' => $complaint->certificate_issued_at, 
            'icon' => '📜', 
            'title' => 'Certificate Issued', 
            'desc' => $complaint->certificate_number
        ];
    }
    
    return collect($timeline)->sortBy('date');
}


///end
}