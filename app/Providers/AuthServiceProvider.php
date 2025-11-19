<?php

namespace App\Providers;

use App\Models\Complaint;
use App\Models\Announcement;
use App\Models\BarangayOfficial;
use App\Policies\ComplaintPolicy;
use App\Policies\AnnouncementPolicy;
use App\Policies\BarangayOfficialPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        Complaint::class => ComplaintPolicy::class,
        Announcement::class => AnnouncementPolicy::class,
        BarangayOfficial::class => BarangayOfficialPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}