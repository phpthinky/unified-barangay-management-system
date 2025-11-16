<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class BusinessPermit extends Model
{
    use HasFactory;

    protected $fillable = [
        'permit_number',
        'applicant_id',
        'barangay_id',
        'business_permit_type_id',
        'business_name',
        'business_type',
        'business_address',
        'business_contact',
        'business_email',
        'business_description',
        'business_start_date',
        'number_of_employees',
        'estimated_monthly_income',
        'business_area_sqm',
        'owner_name',
        'owner_address',
        'owner_contact',
        'owner_is_applicant',
        'form_data',
        'uploaded_documents',
        'business_activities',
        'status',
        'submitted_at',
        'reviewed_at',
        'approved_at',
        'issued_at',
        'expires_at',
        'processed_by',
        'processing_notes',
        'rejection_reason',
        'inspection_required',
        'inspection_scheduled_at',
        'inspection_completed_at',
        'inspected_by',
        'inspection_result',
        'inspection_notes',
        'inspection_photos',
        'total_fees',
        'amount_paid',
        'balance',
        'payment_method',
        'payment_reference',
        'payment_date',
        'fee_breakdown',
        'generated_permit_file',
        'qr_code',
        'is_digital_copy_issued',
        'is_physical_copy_issued',
        'renewed_from',
        'is_renewal',
        'renewal_notes',
    ];

    protected $casts = [
        'form_data' => 'array',
        'uploaded_documents' => 'array',
        'business_start_date' => 'date',
        'estimated_monthly_income' => 'decimal:2',
        'business_area_sqm' => 'decimal:2',
        'owner_is_applicant' => 'boolean',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'inspection_required' => 'boolean',
        'inspection_scheduled_at' => 'datetime',
        'inspection_completed_at' => 'datetime',
        'inspection_photos' => 'array',
        'total_fees' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'payment_date' => 'datetime',
        'fee_breakdown' => 'array',
        'is_digital_copy_issued' => 'boolean',
        'is_physical_copy_issued' => 'boolean',
        'is_renewal' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($permit) {
            if (!$permit->permit_number) {
                $permit->permit_number = $permit->generatePermitNumber();
            }
            
            if (!$permit->submitted_at) {
                $permit->submitted_at = now();
            }
        });
    }

    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function businessPermitType()
    {
        return $this->belongsTo(BusinessPermitType::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    public function renewedFrom()
    {
        return $this->belongsTo(BusinessPermit::class, 'renewed_from');
    }

    public function renewals()
    {
        return $this->hasMany(BusinessPermit::class, 'renewed_from');
    }

    private function generatePermitNumber()
    {
        $year = now()->year;
        $barangayCode = str_pad($this->barangay_id, 2, '0', STR_PAD_LEFT);
        $random = strtoupper(Str::random(6));
        
        return "BP-{$year}-{$barangayCode}-{$random}";
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => ['text' => 'Pending', 'class' => 'badge-warning'],
            'under_review' => ['text' => 'Under Review', 'class' => 'badge-info'],
            'for_inspection' => ['text' => 'For Inspection', 'class' => 'badge-primary'],
            'approved' => ['text' => 'Approved', 'class' => 'badge-success'],
            'rejected' => ['text' => 'Rejected', 'class' => 'badge-danger'],
            'expired' => ['text' => 'Expired', 'class' => 'badge-secondary'],
            'renewed' => ['text' => 'Renewed', 'class' => 'badge-info'],
        ];

        return $badges[$this->status] ?? ['text' => 'Unknown', 'class' => 'badge-secondary'];
    }

    public function getInspectionStatusBadgeAttribute()
    {
        if (!$this->inspection_required) {
            return ['text' => 'Not Required', 'class' => 'badge-light'];
        }

        $badges = [
            'passed' => ['text' => 'Passed', 'class' => 'badge-success'],
            'failed' => ['text' => 'Failed', 'class' => 'badge-danger'],
            'conditional' => ['text' => 'Conditional', 'class' => 'badge-warning'],
        ];

        if (!$this->inspection_result) {
            return ['text' => 'Pending', 'class' => 'badge-info'];
        }

        return $badges[$this->inspection_result] ?? ['text' => 'Unknown', 'class' => 'badge-secondary'];
    }

    public function getFormattedTotalFeesAttribute()
    {
        return '₱' . number_format($this->total_fees, 2);
    }

    public function getFormattedAmountPaidAttribute()
    {
        return '₱' . number_format($this->amount_paid, 2);
    }

    public function getFormattedBalanceAttribute()
    {
        return '₱' . number_format($this->balance, 2);
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isActive()
    {
        return $this->status === 'approved' && !$this->isExpired();
    }

    public function canBeRenewed()
    {
        return $this->status === 'approved' && 
               $this->expires_at && 
               $this->expires_at->diffInDays(now()) <= 30;
    }

    public function approve(User $processor, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'issued_at' => now(),
            'processed_by' => $processor->id,
            'processing_notes' => $notes,
            'expires_at' => now()->addMonths($this->businessPermitType->validity_months),
        ]);
    }

    public function reject(User $processor, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'processed_by' => $processor->id,
            'rejection_reason' => $reason,
            'reviewed_at' => now(),
        ]);
    }

    public function scheduleInspection($scheduledDate, User $inspector = null)
    {
        $this->update([
            'status' => 'for_inspection',
            'inspection_required' => true,
            'inspection_scheduled_at' => $scheduledDate,
            'inspected_by' => $inspector ? $inspector->id : null,
        ]);
    }

    public function completeInspection(User $inspector, $result, $notes = null, $photos = null)
    {
        $this->update([
            'inspection_completed_at' => now(),
            'inspected_by' => $inspector->id,
            'inspection_result' => $result,
            'inspection_notes' => $notes,
            'inspection_photos' => $photos,
        ]);

        // Update status based on inspection result
        if ($result === 'passed') {
            $this->update(['status' => 'under_review']);
        } elseif ($result === 'failed') {
            $this->update(['status' => 'rejected', 'rejection_reason' => 'Failed inspection: ' . $notes]);
        }
    }

    public function renew(User $applicant, $notes = null)
    {
        $renewalData = $this->toArray();
        
        // Remove fields that shouldn't be copied
        unset($renewalData['id'], $renewalData['permit_number'], $renewalData['created_at'], $renewalData['updated_at']);
        
        // Set renewal-specific fields
        $renewalData['renewed_from'] = $this->id;
        $renewalData['is_renewal'] = true;
        $renewalData['renewal_notes'] = $notes;
        $renewalData['status'] = 'pending';
        $renewalData['submitted_at'] = now();
        $renewalData['approved_at'] = null;
        $renewalData['issued_at'] = null;
        $renewalData['expires_at'] = null;

        return static::create($renewalData);
    }

    public function scopeByBarangay($query, $barangayId)
    {
        return $query->where('barangay_id', $barangayId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'approved')->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('status', 'approved')
                    ->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    public function scopeRenewals($query)
    {
        return $query->where('is_renewal', true);
    }
}