<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Barangay extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'contact_number',
        'email',
        'address',
        'description',
        'qr_code',
        'public_url',
        'is_active',
        'social_media',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'social_media' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($barangay) {
            if (!$barangay->slug) {
                $barangay->slug = Str::slug($barangay->name);
            }
            
            // Generate public URL
            $barangay->public_url = url('/b/' . $barangay->slug);
        });

        static::updating(function ($barangay) {
            if ($barangay->isDirty('name') && !$barangay->isDirty('slug')) {
                $barangay->slug = Str::slug($barangay->name);
            }
            
            // Update public URL if slug changed
            if ($barangay->isDirty('slug')) {
                $barangay->public_url = url('/b/' . $barangay->slug);
            }
        });
    }

    /**
     * Get users belonging to this barangay.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get barangay inhabitants (RBI records) for this barangay.
     * NEW: Primary method name for consistency
     */
    public function inhabitants()
    {
        return $this->hasMany(BarangayInhabitant::class);
    }

    /**
     * Get verified inhabitants only.
     * NEW: Primary method name for consistency
     */
    public function verifiedInhabitants()
    {
        return $this->hasMany(BarangayInhabitant::class)->where('is_verified', true);
    }

    /**
     * Get online resident profiles (registered users with profiles).
     * LEGACY ALIAS: residentProfiles now refers to online accounts
     */
    public function residentProfiles()
    {
        return $this->hasMany(ResidentProfile::class);
    }

    /**
     * Get verified online resident profiles only.
     * LEGACY ALIAS: verifiedResidents now refers to verified online accounts
     */
    public function verifiedResidents()
    {
        return $this->hasMany(ResidentProfile::class)->where('is_verified', true);
    }

    /**
     * Get pending residents (unverified from both systems).
     */
    public function pendingResidents()
    {
        // Note: This returns RBI only for backward compatibility
        // Use pendingInhabitants() or pendingProfiles() for specific systems
        return $this->hasMany(BarangayInhabitant::class)->where('is_verified', false);
    }

    /**
     * Get pending RBI inhabitants.
     */
    public function pendingInhabitants()
    {
        return $this->hasMany(BarangayInhabitant::class)->where('is_verified', false);
    }

    /**
     * Get pending online profiles.
     */
    public function pendingProfiles()
    {
        return $this->hasMany(ResidentProfile::class)->where('is_verified', false);
    }

    /**
     * Get document requests for this barangay.
     */
    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }

    /**
     * Get complaints for this barangay.
     */
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * Get business permits for this barangay.
     */
    public function businessPermits()
    {
        return $this->hasMany(BusinessPermit::class);
    }

    /**
     * Get complaint hearings for this barangay.
     */
    public function complaintHearings()
    {
        return $this->hasMany(ComplaintHearing::class);
    }

    /**
     * Get terms (archived officials) for this barangay.
     */
    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    /**
     * Get the current barangay captain.
     */
    public function captain()
    {
        return $this->hasOne(User::class)
                   ->whereHas('roles', function ($query) {
                       $query->where('name', 'barangay-captain');
                   })
                   ->where('is_active', true)
                   ->where('is_archived', false);
    }

    /**
     * Get the current barangay secretary.
     */
    public function secretary()
    {
        return $this->hasOne(User::class)
                   ->whereHas('roles', function ($query) {
                       $query->where('name', 'barangay-secretary');
                   })
                   ->where('is_active', true)
                   ->where('is_archived', false);
    }

    /**
     * Get barangay staff members.
     */
    public function staff()
    {
        return $this->hasMany(User::class)
                   ->whereHas('roles', function ($query) {
                       $query->whereIn('name', ['barangay-captain', 'barangay-secretary', 'barangay-staff']);
                   })
                   ->where('is_active', true)
                   ->where('is_archived', false);
    }

    /**
     * Get lupon members for this barangay.
     */
    public function luponMembers()
    {
        return $this->hasMany(User::class)
                   ->whereHas('roles', function ($query) {
                       $query->where('name', 'lupon-member');
                   })
                   ->where('is_active', true)
                   ->where('is_archived', false);
    }

    /**
     * Get the registration URL for this barangay.
     */
    public function getRegistrationUrlAttribute()
    {
        return url('/b/' . $this->slug . '/register');
    }

    /**
     * Get the logo URL.
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('uploads/logos/' . $this->logo) : null;
    }

    /**
     * Get the QR code URL.
     */
    public function getQrCodeUrlAttribute()
    {
        return $this->qr_code ? asset('uploads/qr-codes/' . $this->qr_code) : null;
    }

    /**
     * Get statistics for this barangay using both RBI and online data.
     */
    public function getStatsAttribute()
    {
        $rbiTotal = $this->inhabitants()->count();
        $rbiVerified = $this->verifiedInhabitants()->count();
        $onlineTotal = $this->residentProfiles()->count();
        $onlineVerified = $this->verifiedResidents()->count();
        
        return [
            // Combined totals
            'total_residents' => $rbiTotal + $onlineTotal,
            'verified_residents' => $rbiVerified + $onlineVerified,
            'pending_residents' => ($rbiTotal - $rbiVerified) + ($onlineTotal - $onlineVerified),
            
            // RBI specific
            'rbi_total' => $rbiTotal,
            'rbi_verified' => $rbiVerified,
            'rbi_pending' => $rbiTotal - $rbiVerified,
            
            // Online specific  
            'online_total' => $onlineTotal,
            'online_verified' => $onlineVerified,
            'online_pending' => $onlineTotal - $onlineVerified,
            
            // Other stats
            'household_heads' => $this->inhabitants()->where('is_household_head', true)->count() +
                               $this->residentProfiles()->where('is_household_head', true)->count(),
            'active_residents' => $this->inhabitants()->where('is_active', true)->count() +
                                $this->residentProfiles()->count(), // All online users considered active
            'document_requests' => $this->documentRequests()->count(),
            'pending_documents' => $this->documentRequests()->where('status', 'pending')->count(),
            'active_complaints' => $this->complaints()->whereIn('status', ['received', 'assigned', 'in_process'])->count(),
            'resolved_complaints' => $this->complaints()->where('status', 'resolved')->count(),
            'active_permits' => $this->businessPermits()->where('status', 'approved')->where('expires_at', '>', now())->count(),
            'expired_permits' => $this->businessPermits()->where('expires_at', '<=', now())->count(),
        ];
    }

    /**
     * Scope to get active barangays only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Generate QR Code for this barangay
     */
    public function generateQrCode(): bool
    {
        try {
            // Ensure directory exists (Windows compatible)
            $qrPath = public_path('uploads' . DIRECTORY_SEPARATOR . 'qr-codes');
            if (!file_exists($qrPath)) {
                mkdir($qrPath, 0777, true);
            }

            $qrCodeName = 'qr_' . $this->slug . '_' . time() . '.png';
            $qrCodePath = $qrPath . DIRECTORY_SEPARATOR . $qrCodeName;
            
            // Delete old QR code if exists
            if ($this->qr_code) {
                $oldQrPath = public_path('uploads' . DIRECTORY_SEPARATOR . 'qr-codes' . DIRECTORY_SEPARATOR . $this->qr_code);
                if (file_exists($oldQrPath)) {
                    unlink($oldQrPath);
                }
            }

            // Use helper method to generate QR
            $success = \App\Helpers\QrCodeHelper::saveToFile(
                $this->registration_url,
                $qrCodePath,
                300
            );

            if (!$success) {
                \Log::error('QR code file was not created for: ' . $this->name);
                return false;
            }

            // Update barangay record
            $this->update(['qr_code' => $qrCodeName]);

            return true;

        } catch (\Exception $e) {
            \Log::error('QR Code generation failed for ' . $this->name . ': ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get route key name for model binding.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}