<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'municipality_name',
        'province',
        'region',
        'municipality_logo',
        'municipality_seal',
        'municipality_address',
        'municipality_contact',
        'municipality_email',
        'municipality_website',
        'municipality_description',
        'mayor_name',
        'mayor_photo',
        'vice_mayor_name',
        'vice_mayor_photo',
        'council_members',
        'abc_president_name',
        'abc_president_photo',
        'abc_description',
        'system_name',
        'system_version',
        'maintenance_mode',
        'maintenance_message',
        'allow_public_access',
        'allow_resident_registration',
        'require_resident_verification',
        'system_email',
        'email_notifications_enabled',
        'notification_settings',
        'default_document_processing_days',
        'default_document_fee',
        'document_validity_days',
        'max_file_size_mb',
        'allowed_file_types',
        'file_storage_path',
        'session_timeout_minutes',
        'password_reset_timeout_minutes',
        'two_factor_enabled',
        'primary_color',
        'secondary_color',
        'theme',
        'css_customizations',
        'social_media_links',
        'terms_of_service',
        'privacy_policy',
        'data_retention_policy',
    ];

    protected $casts = [
        'council_members' => 'array',
        'maintenance_mode' => 'boolean',
        'allow_public_access' => 'boolean',
        'allow_resident_registration' => 'boolean',
        'require_resident_verification' => 'boolean',
        'email_notifications_enabled' => 'boolean',
        'notification_settings' => 'array',
        'default_document_fee' => 'decimal:2',
        'allowed_file_types' => 'array',
        'two_factor_enabled' => 'boolean',
        'css_customizations' => 'array',
        'social_media_links' => 'array',
    ];

    /**
     * Get the municipality logo URL.
     */
    public function getMunicipalityLogoUrlAttribute()
    {
        return $this->municipality_logo ? asset('uploads/logos/' . $this->municipality_logo) : null;
    }

    /**
     * Get the municipality seal URL.
     */
    public function getMunicipalitySealUrlAttribute()
    {
        return $this->municipality_seal ? asset('uploads/seals/' . $this->municipality_seal) : null;
    }

    /**
     * Get the mayor photo URL.
     */
    public function getMayorPhotoUrlAttribute()
    {
        return $this->mayor_photo ? asset('uploads/photos/' . $this->mayor_photo) : null;
    }

    /**
     * Get the vice mayor photo URL.
     */
    public function getViceMayorPhotoUrlAttribute()
    {
        return $this->vice_mayor_photo ? asset('uploads/photos/' . $this->vice_mayor_photo) : null;
    }

    /**
     * Get the ABC president photo URL.
     */
    public function getAbcPresidentPhotoUrlAttribute()
    {
        return $this->abc_president_photo ? asset('uploads/photos/' . $this->abc_president_photo) : null;
    }

    /**
     * Get formatted file size limit.
     */
    public function getFormattedFileSizeAttribute()
    {
        return $this->max_file_size_mb . ' MB';
    }

    /**
     * Get allowed file types as string.
     */
    public function getAllowedFileTypesStringAttribute()
    {
        return $this->allowed_file_types ? implode(', ', $this->allowed_file_types) : '';
    }

    /**
     * Get system information.
     */
    public function getSystemInfoAttribute()
    {
        return [
            'name' => $this->system_name,
            'version' => $this->system_version,
            'municipality' => $this->municipality_name,
            'maintenance_mode' => $this->maintenance_mode,
            'public_access' => $this->allow_public_access,
        ];
    }

    /**
     * Get a specific setting value.
     */
    public static function getSetting($key, $default = null)
    {
        $settings = static::first();
        return $settings ? ($settings->$key ?? $default) : $default;
    }

    /**
     * Update a specific setting.
     */
    public static function setSetting($key, $value)
    {
        $settings = static::firstOrCreate([]);
        $settings->update([$key => $value]);
        return $settings;
    }

    /**
     * Check if system is in maintenance mode.
     */
    public static function isInMaintenance()
    {
        return static::getSetting('maintenance_mode', false);
    }

    /**
     * Check if public access is allowed.
     */
    public static function allowsPublicAccess()
    {
        return static::getSetting('allow_public_access', true);
    }

    /**
     * Check if resident registration is allowed.
     */
    public static function allowsResidentRegistration()
    {
        return static::getSetting('allow_resident_registration', true);
    }
}