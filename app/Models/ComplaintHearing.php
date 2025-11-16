<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ComplaintHearing extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'barangay_id',
        'hearing_number',
        'hearing_type',
        'scheduled_date',
        'venue',
        'agenda',
        'lupon_members',
        'presiding_officer',
        'attendees',
        'absent_parties',
        'status',
        'actual_start_time',
        'actual_end_time',
        'minutes',
        'resolution',
        'outcome',
        'agreements_reached',
        'uploaded_documents',
        'requires_next_hearing',
        'next_hearing_date',
        'next_steps',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'lupon_members' => 'array',
        'attendees' => 'array',
        'absent_parties' => 'array',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime',
        'agreements_reached' => 'array',
        'uploaded_documents' => 'array',
        'requires_next_hearing' => 'boolean',
        'next_hearing_date' => 'date',
    ];

    // Relationships
    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function presidingOfficer()
    {
        return $this->belongsTo(User::class, 'presiding_officer');
    }

    // Scopes - ADD THESE
    public function scopeByBarangay($query, $barangayId)
    {
        return $query->where('barangay_id', $barangayId);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopePresidedBy($query, $userId)
    {
        return $query->where('presiding_officer', $userId);
    }

    public function scopeWithLuponMember($query, $userId)
    {
        return $query->whereJsonContains('lupon_members', $userId);
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->where('status', 'scheduled')
                     ->whereBetween('scheduled_date', [now(), now()->addDays($days)]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_date', today());
    }

    public function scopePast($query)
    {
        return $query->where('scheduled_date', '<', now());
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'scheduled' => ['text' => 'Scheduled', 'class' => 'badge-info'],
            'ongoing' => ['text' => 'Ongoing', 'class' => 'badge-warning'],
            'postponed' => ['text' => 'Postponed', 'class' => 'badge-secondary'],
            'completed' => ['text' => 'Completed', 'class' => 'badge-success'],
            'cancelled' => ['text' => 'Cancelled', 'class' => 'badge-danger'],
        ];

        return $badges[$this->status] ?? ['text' => 'Unknown', 'class' => 'badge-secondary'];
    }

    // Helper methods
    public function isScheduled()
    {
        return $this->status === 'scheduled';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isOngoing()
    {
        return $this->status === 'ongoing';
    }

    public function canStart()
    {
        return $this->status === 'scheduled' && $this->scheduled_date->isPast();
    }

    public function canComplete()
    {
        return $this->status === 'ongoing';
    }

    public function hasLuponMember($userId)
    {
        return in_array($userId, $this->lupon_members ?? []);
    }

    public function start()
    {
        $this->update([
            'status' => 'ongoing',
            'actual_start_time' => now(),
        ]);
    }

    public function complete($minutes, $outcome, $resolution = null, $agreements = null)
    {
        $this->update([
            'status' => 'completed',
            'actual_end_time' => now(),
            'minutes' => $minutes,
            'outcome' => $outcome,
            'resolution' => $resolution,
            'agreements_reached' => $agreements,
        ]);
    }
}