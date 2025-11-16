<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DocumentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'user_id',
        'barangay_id',
        'document_type_id',
        'form_data',
        'purpose',
        'copies_requested',
        'uploaded_files',
        'status',
        'submitted_at',
        'processed_at',
        'approved_at',
        'released_at',
        'processed_by',
        'processing_notes',
        'rejection_reason',
        'amount_paid',
        'payment_method',
        'payment_reference',
        'payment_date',
        'generated_file',
        'qr_code',
        'expires_at',
    ];

    protected $casts = [
        'form_data' => 'array',
        'uploaded_files' => 'array',
        'submitted_at' => 'datetime',
        'processed_at' => 'datetime',
        'approved_at' => 'datetime',
        'released_at' => 'datetime',
        'payment_date' => 'datetime',
        'expires_at' => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            if (!$request->tracking_number) {
                $request->tracking_number = $request->generateTrackingNumber();
            }
            
            if (!$request->submitted_at) {
                $request->submitted_at = now();
            }
        });
    }

    /**
     * Get the user who made this request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the barangay for this request.
     */
    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    /**
     * Get the document type.
     */
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Get the user who processed this request.
     */
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Generate tracking number.
     */
    private function generateTrackingNumber()
    {
        $year = now()->year;
        $barangayCode = str_pad($this->barangay_id, 2, '0', STR_PAD_LEFT);
        $random = strtoupper(Str::random(6));
        
        return "DOC-{$year}-{$barangayCode}-{$random}";
    }

    /**
     * Get status badge.
     */
   /*
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => ['text' => 'Pending', 'class' => 'badge-warning'],
            'processing' => ['text' => 'Processing', 'class' => 'badge-info'],
            'approved' => ['text' => 'Approved', 'class' => 'badge-success'],
            'rejected' => ['text' => 'Rejected', 'class' => 'badge-danger'],
            'released' => ['text' => 'Released', 'class' => 'badge-primary'],
        ];

        return $badges[$this->status] ?? ['text' => 'Unknown', 'class' => 'badge-secondary'];
    }
*/
    /**
     * Get formatted amount paid.
     */
    public function getFormattedAmountAttribute()
    {
        return '₱' . number_format($this->amount_paid, 2);
    }

    /**
     * Get processing days.
     */
    public function getProcessingDaysAttribute()
    {
        if (!$this->approved_at) {
            return $this->submitted_at->diffInDays(now());
        }
        
        return $this->submitted_at->diffInDays($this->approved_at);
    }

    /**
     * Get uploaded file URLs.
     */
    public function getUploadedFileUrlsAttribute()
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
     * Get generated document URL.
     */
    public function getGeneratedFileUrlAttribute()
    {
        return $this->generated_file ? asset('uploads/documents/' . $this->generated_file) : null;
    }

    /**
     * Get QR code URL.
     */
    public function getQrCodeUrlAttribute()
    {
        return $this->qr_code ? asset('uploads/qr-codes/' . $this->qr_code) : null;
    }

    /**
     * Check if document is expired.
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if document can be downloaded.
     */
    public function canBeDownloaded()
    {
        return $this->status === 'approved' && $this->generated_file && !$this->isExpired();
    }

    /**
     * Process the request.
     */
    public function process(User $processor, $notes = null)
    {
        $this->update([
            'status' => 'processing',
            'processed_at' => now(),
            'processed_by' => $processor->id,
            'processing_notes' => $notes,
        ]);
    }

    /**
     * Approve the request.
     */
    public function approve(User $processor, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'processed_by' => $processor->id,
            'processing_notes' => $notes,
            'expires_at' => now()->addDays($this->documentType->processing_days ?? 90),
        ]);
    }

    /**
     * Reject the request.
     */
    public function reject(User $processor, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'processed_by' => $processor->id,
            'rejection_reason' => $reason,
            'processed_at' => now(),
        ]);
    }

    /**
     * Mark as released.
     */
    public function markAsReleased()
    {
        $this->update([
            'status' => 'released',
            'released_at' => now(),
        ]);
    }

    /**
     * Scopes
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByBarangay($query, $barangayId)
    {
        return $query->where('barangay_id', $barangayId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

// ADD THESE METHODS TO YOUR App\Models\DocumentRequest MODEL

/**
 * Scope for cancelled requests
 */
public function scopeCancelled($query)
{
    return $query->where('status', 'cancelled');
}

/**
 * Get status badge with cancelled status
 */
public function getStatusBadgeAttribute()
{
    $badges = [
        'pending' => ['text' => 'Pending', 'class' => 'badge-warning'],
        'processing' => ['text' => 'Processing', 'class' => 'badge-info'],
        'approved' => ['text' => 'Approved', 'class' => 'badge-success'],
        'rejected' => ['text' => 'Rejected', 'class' => 'badge-danger'],
        'released' => ['text' => 'Released', 'class' => 'badge-primary'],
        'cancelled' => ['text' => 'Cancelled', 'class' => 'badge-secondary'],
    ];

    return $badges[$this->status] ?? ['text' => 'Unknown', 'class' => 'badge-secondary'];
}

}