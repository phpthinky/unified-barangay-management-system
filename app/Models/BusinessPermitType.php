<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BusinessPermitType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'requirements',
        'base_fee',
        'additional_fees',
        'processing_days',
        'validity_months',
        'requires_inspection',
        'requires_fire_safety',
        'requires_health_permit',
        'requires_environmental_clearance',
        'template_content',
        'template_fields',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'requirements' => 'array',
        'base_fee' => 'decimal:2',
        'additional_fees' => 'array',
        'template_fields' => 'array',
        'requires_inspection' => 'boolean',
        'requires_fire_safety' => 'boolean',
        'requires_health_permit' => 'boolean',
        'requires_environmental_clearance' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($permitType) {
            if (!$permitType->slug) {
                $permitType->slug = Str::slug($permitType->name);
            }
        });
    }

    /**
     * Relationships
     */
    public function businessPermits()
    {
        return $this->hasMany(BusinessPermit::class);
    }

    /**
     * Accessors
     */
    public function getFormattedBaseFeeAttribute()
    {
        return '₱' . number_format($this->base_fee, 2);
    }

    public function getValidityDescriptionAttribute()
    {
        if ($this->validity_months == 12) {
            return '1 year';
        }
        
        return $this->validity_months . ' months';
    }

    public function getTotalFeesAttribute()
    {
        $total = $this->base_fee;
        
        if (!empty($this->additional_fees) && is_array($this->additional_fees)) {
            foreach ($this->additional_fees as $fee) {
                if (is_array($fee) && isset($fee['amount'])) {
                    $total += $fee['amount'];
                }
            }
        }
        
        return $total;
    }

    public function getCategoryDisplayAttribute()
    {
        $categories = [
            'micro' => 'Micro Enterprise',
            'small' => 'Small Enterprise',
            'medium' => 'Medium Enterprise',
            'large' => 'Large Enterprise',
            'home_based' => 'Home Based',
            'street_vendor' => 'Street Vendor',
        ];

        return $categories[$this->category] ?? ucfirst(str_replace('_', ' ', $this->category));
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Route binding
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}