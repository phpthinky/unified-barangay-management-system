<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangayOfficial extends Model
{
    use HasFactory;

    protected $fillable = [
        'barangay_id',
        'name',
        'position',
        'committee',
        'display_order',
        'term_start',
        'term_end',
        'is_active',
        'contact_number',
        'email',
        'avatar',
        'description',
    ];

    protected $casts = [
        'term_start' => 'date',
        'term_end' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the barangay that owns the official.
     */
    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    /**
     * Scope to get active officials only (current term).
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('term_start', '<=', now())
                     ->where('term_end', '>=', now());
    }

    /**
     * Scope to get officials by term dates.
     */
    public function scopeByTerm($query, $termStart, $termEnd)
    {
        return $query->where('term_start', $termStart)
                     ->where('term_end', $termEnd);
    }

    /**
     * Scope to order officials for org chart display.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc')
                     ->orderBy('position', 'asc');
    }

    /**
     * Check if this official is currently serving.
     */
    public function isCurrentlyServing()
    {
        $now = now();
        return $this->is_active
            && $this->term_start <= $now
            && $this->term_end >= $now;
    }

    /**
     * Get the avatar URL or default placeholder.
     */
    public function getAvatarUrl()
    {
        if ($this->avatar && file_exists(public_path('storage/' . $this->avatar))) {
            return asset('storage/' . $this->avatar);
        }

        // Return default avatar placeholder
        return asset('images/default-avatar.png');
    }

    /**
     * Get position badge class for display.
     */
    public function getPositionBadgeClass()
    {
        if (stripos($this->position, 'punong') !== false || stripos($this->position, 'captain') !== false) {
            return 'primary';
        }

        if (stripos($this->position, 'kagawad') !== false || stripos($this->position, 'councilor') !== false) {
            return 'success';
        }

        if (stripos($this->position, 'secretary') !== false) {
            return 'info';
        }

        if (stripos($this->position, 'sk') !== false) {
            return 'warning';
        }

        return 'secondary';
    }
}
