<?php

namespace App\Policies;

use App\Models\BarangayOfficial;
use App\Models\User;

class BarangayOfficialPolicy
{
    /**
     * Determine if the user can view any barangay officials.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('abc-president');
    }

    /**
     * Determine if the user can view the barangay official.
     */
    public function view(User $user, BarangayOfficial $barangayOfficial)
    {
        return $user->hasRole('abc-president');
    }

    /**
     * Determine if the user can create barangay officials.
     */
    public function create(User $user)
    {
        return $user->hasRole('abc-president');
    }

    /**
     * Determine if the user can update the barangay official.
     */
    public function update(User $user, BarangayOfficial $barangayOfficial)
    {
        return $user->hasRole('abc-president');
    }

    /**
     * Determine if the user can delete the barangay official.
     */
    public function delete(User $user, BarangayOfficial $barangayOfficial)
    {
        return $user->hasRole('abc-president');
    }
}
