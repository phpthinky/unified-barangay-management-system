<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'barangay_id',
        'position',
        'term_start',
        'term_end',
        'is_active',
        'is_archived',
        'appointment_type',
        'election_date',
        'election_type',
        'appointment_details',
        'achievements',
        'projects_completed',
        'performance_metrics',
        'archived_at',
        'archived_by',
        'transition_notes',
        'succeeded_by',
        'archived_documents',
        'handover_documents',
    ];

    protected $casts = [
        'term_start' => 'date',
        'term_end' => 'date',
        'is_active' => 'boolean',
        'is_archived' => 'boolean',
        'election_date' => 'date',
        'achievements' => 'array',
        'projects_completed' => 'array',
        'performance_metrics' => 'array',
        'archived_at' => 'datetime',
        'archived_documents' => 'array',
        'handover_documents' => 'array',
    ];

    /**
     * Get the user who served this term.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the barangay for this term (null for municipality-level positions).
     */
    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    /**
     * Get the user who archived this term.
     */
    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    /**
     * Get the successor for this term.
     */
    public function successor()
    {
        return $this->belongsTo(User::class, 'succeeded_by');
    }

    /**
     * Get term duration in years.
     */
    public function getTermDurationAttribute()
    {
        return $this->term_start->diffInYears($this->term_end);
    }

    /**
     * Get remaining days in term.
     */
    public function getRemainingDaysAttribute()
    {
        if ($this->is_archived || $this->term_end->isPast()) {
            return 0;
        }
        
        return now()->diffInDays($this->term_end);
    }

    /**
     * Check if term is expiring soon.
     */
    public function isExpiringSoon($days = 30)
    {
        return !$this->is_archived && 
               $this->is_active && 
               $this->remaining_days <= $days && 
               $this->remaining_days > 0;
    }

    /**
     * Check if term has expired.
     */
    public function hasExpired()
    {
        return $this->term_end->isPast();
    }

    /**
     * Archive this term.
     */
    public function archive(User $archivedBy, $notes = null, $successorId = null)
    {
        $this->update([
            'is_active' => false,
            'is_archived' => true,
            'archived_at' => now(),
            'archived_by' => $archivedBy->id,
            'transition_notes' => $notes,
            'succeeded_by' => $successorId,
        ]);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    public function scopeByBarangay($query, $barangayId)
    {
        return $query->where('barangay_id', $barangayId);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('is_active', true)
                    ->where('is_archived', false)
                    ->whereBetween('term_end', [now(), now()->addDays($days)]);
    }

    public function scopeExpired($query)
    {
        return $query->where('term_end', '<', now())
                    ->where('is_active', true)
                    ->where('is_archived', false);
    }
}
