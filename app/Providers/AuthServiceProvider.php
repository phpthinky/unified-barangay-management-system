<?php

namespace App\Providers;

use App\Models\Complaint;
use App\Models\Announcement;
use App\Policies\ComplaintPolicy;
use App\Policies\AnnouncementPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        Complaint::class => ComplaintPolicy::class,
        Announcement::class => AnnouncementPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}