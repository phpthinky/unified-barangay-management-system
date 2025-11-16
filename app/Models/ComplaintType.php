<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ComplaintType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'default_handler_type',
        'requires_hearing',
        'estimated_resolution_days',
        'required_information',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'requires_hearing' => 'boolean',
        'required_information' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($complaintType) {
            if (!$complaintType->slug) {
                $complaintType->slug = Str::slug($complaintType->name);
            }
        });
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
