<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'barangay_id',
        'created_by',
        'title',
        'content',
        'priority',
        'status',
        'pin_to_top',
        'show_on_public',
        'published_at',
        'expires_at',
    ];

    protected $casts = [
        'pin_to_top' => 'boolean',
        'show_on_public' => 'boolean',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the barangay that owns the announcement.
     */
    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    /**
     * Get the user who created the announcement.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to get published announcements only.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->where(function($q) {
                         $q->whereNull('published_at')
                           ->orWhere('published_at', '<=', now());
                     })
                     ->where(function($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>=', now());
                     });
    }

    /**
     * Scope to get active (published and not expired) announcements.
     */
    public function scopeActive($query)
    {
        return $this->scopePublished($query);
    }

    /**
     * Scope to get pinned announcements.
     */
    public function scopePinned($query)
    {
        return $query->where('pin_to_top', true);
    }

    /**
     * Scope to order by priority and date.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('pin_to_top', 'desc')
                     ->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")
                     ->orderBy('published_at', 'desc')
                     ->orderBy('created_at', 'desc');
    }

    /**
     * Check if announcement is active.
     */
    public function isActive()
    {
        if ($this->status !== 'published') {
            return false;
        }

        if ($this->published_at && $this->published_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if announcement is expired.
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get priority badge class.
     */
    public function getPriorityBadgeClass()
    {
        return match($this->priority) {
            'urgent' => 'danger',
            'high' => 'warning',
            'normal' => 'info',
            'low' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'published' => 'success',
            'draft' => 'secondary',
            'archived' => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Get excerpt of content.
     */
    public function getExcerpt($length = 150)
    {
        return \Illuminate\Support\Str::limit(strip_tags($this->content), $length);
    }
}
