<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    /**
     * Determine if the user can view the announcement.
     */
    public function view(User $user, Announcement $announcement)
    {
        return $user->barangay_id === $announcement->barangay_id;
    }

    /**
     * Determine if the user can create announcements.
     */
    public function create(User $user)
    {
        return $user->hasAnyRole(['barangay-captain', 'barangay-secretary']);
    }

    /**
     * Determine if the user can update the announcement.
     */
    public function update(User $user, Announcement $announcement)
    {
        return $user->barangay_id === $announcement->barangay_id
               && $user->hasAnyRole(['barangay-captain', 'barangay-secretary']);
    }

    /**
     * Determine if the user can delete the announcement.
     */
    public function delete(User $user, Announcement $announcement)
    {
        return $user->barangay_id === $announcement->barangay_id
               && $user->hasAnyRole(['barangay-captain', 'barangay-secretary']);
    }
}
