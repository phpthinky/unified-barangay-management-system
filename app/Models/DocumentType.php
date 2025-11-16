<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'requirements',
        'fee',
        'processing_days',
        'template_content',
        'template_fields',
        'form_fields',
        'category',
        'document_format',
        'format_notes',
        'enable_printing',
        'requires_verification',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'requirements' => 'array',
        'template_fields' => 'array',
        'form_fields' => 'array',  // For dynamic form generation
        'fee' => 'decimal:2',
        'requires_verification' => 'boolean',
        'enable_printing' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($documentType) {
            if (!$documentType->slug) {
                $documentType->slug = Str::slug($documentType->name);
            }
        });

        static::updating(function ($documentType) {
            if ($documentType->isDirty('name') && !$documentType->isDirty('slug')) {
                $documentType->slug = Str::slug($documentType->name);
            }
        });
    }

    /**
     * Get document requests of this type.
     */
    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }

    /**
     * Get active document requests.
     */
    public function activeRequests()
    {
        return $this->hasMany(DocumentRequest::class)
                   ->whereIn('status', ['pending', 'processing', 'approved']);
    }

    /**
     * Get formatted fee.
     */
    public function getFormattedFeeAttribute()
    {
        return '₱' . number_format($this->fee, 2);
    }

    /**
     * Get processing time description.
     */
    public function getProcessingTimeAttribute()
    {
        if ($this->processing_days == 1) {
            return '1 day';
        }
        
        return $this->processing_days . ' days';
    }

    /**
     * Scope to get active document types only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get route key name for model binding.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Check if document type has template.
     */
    public function hasTemplate()
    {
        return !empty($this->template_content);
    }

    /**
     * Check if this is a certificate format (standard printing).
     */
    public function isCertificate()
    {
        return $this->document_format === 'certificate';
    }

    /**
     * Check if this is an ID card format.
     */
    public function isIdCard()
    {
        return $this->document_format === 'id_card';
    }

    /**
     * Check if printing is enabled for this document type.
     */
    public function isPrintingEnabled()
    {
        return $this->enable_printing === true;
    }

    /**
     * Check if can print this document.
     */
    public function canPrint()
    {
        return $this->enable_printing && $this->is_active;
    }

    /**
     * Get paper size for printing.
     */
    public function getPaperSize()
    {
        return match($this->document_format) {
            'certificate' => 'A4',           // or 'letter'
            'id_card' => '3.375x2.125in',    // Standard ID card size
            'half_sheet' => 'A5',
            'legal' => 'legal',
            default => 'A4',
        };
    }

    /**
     * Get format description.
     */
    public function getFormatDescriptionAttribute()
    {
        return match($this->document_format) {
            'certificate' => 'Standard Certificate (8.5" x 11" or A4)',
            'id_card' => 'ID Card Size (3.375" x 2.125")',
            'half_sheet' => 'Half Sheet / Short Bond',
            'legal' => 'Legal Size (8.5" x 14")',
            'custom' => 'Custom Format',
            default => 'Standard Certificate',
        };
    }

    /**
     * Get statistics for this document type.
     */
    public function getStatsAttribute()
    {
        return [
            'total_requests' => $this->documentRequests()->count(),
            'pending_requests' => $this->documentRequests()->where('status', 'pending')->count(),
            'approved_requests' => $this->documentRequests()->where('status', 'approved')->count(),
            'rejected_requests' => $this->documentRequests()->where('status', 'rejected')->count(),
            'average_processing_days' => $this->getAverageProcessingDays(),
        ];
    }

    /**
     * Get average processing days for completed requests.
     */
    private function getAverageProcessingDays()
    {
        $completed = $this->documentRequests()
                         ->whereNotNull('approved_at')
                         ->get();

        if ($completed->isEmpty()) {
            return 0;
        }

        $totalDays = 0;
        foreach ($completed as $request) {
            $totalDays += $request->submitted_at->diffInDays($request->approved_at);
        }

        return round($totalDays / $completed->count(), 1);
    }

    /**
     * Get rendered template with sample data for preview.
     */
    public function getRenderedTemplate()
    {
        if (!$this->template_content) {
            return '<p class="text-muted text-center py-5">No template configured. Click "Edit Template" to create one.</p>';
        }

        // Sample data for template preview
        $sampleData = [
            '[NAME]' => 'JUAN DELA CRUZ',
            '[ADDRESS]' => 'Purok 1, Barangay Poblacion',
            '[BIRTHDAY]' => 'January 15, 1990',
            '[BIRTHDATE]' => 'January 15, 1990',
            '[DATE_OF_BIRTH]' => 'January 15, 1990',
            '[PLACE_OF_BIRTH]' => 'Sablayan, Occidental Mindoro',
            '[AGE]' => '34',
            '[SEX]' => 'Male',
            '[GENDER]' => 'Male',
            '[CIVIL_STATUS]' => 'Married',
            '[BARANGAY]' => 'Poblacion',
            '[BARANGAY_NAME]' => 'Poblacion',
            '[DATE]' => now()->format('F d, Y'),
            '[CURRENT_DATE]' => now()->format('F d, Y'),
            '[ISSUE_DATE]' => now()->format('F d, Y'),
            '[PURPOSE]' => 'Employment purposes',
            '[BARANGAY_CAPTAIN]' => 'MARIA SANTOS',
            '[PUNONG_BARANGAY]' => 'MARIA SANTOS',
            '[CAPTAIN_NAME]' => 'MARIA SANTOS',
            '[KAGAWAD]' => 'PEDRO REYES',
            '[SECRETARY]' => 'ROSA GARCIA',
            '[TREASURER]' => 'JOSE MARTINEZ',
        ];

        // Add custom form fields from this document type
        if ($this->form_fields && is_array($this->form_fields)) {
            foreach ($this->form_fields as $field) {
                $fieldName = '[' . strtoupper($field['name'] ?? '') . ']';
                $sampleData[$fieldName] = 'Sample ' . ($field['label'] ?? 'Data');
            }
        }

        // Replace placeholders with sample data
        $rendered = $this->template_content;
        foreach ($sampleData as $placeholder => $value) {
            $rendered = str_replace($placeholder, $value, $rendered);
        }

        return $rendered;
    }
}