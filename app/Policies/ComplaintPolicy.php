<?php

namespace App\Policies;

use App\Models\Complaint;
use App\Models\User;

class ComplaintPolicy
{
    /**
     * Determine if user can view complaint
     */
    public function view(User $user, Complaint $complaint): bool
    {
        // Barangay staff can view complaints in their barangay
        if ($user->hasAnyRole(['barangay-captain', 'barangay-secretary', 'barangay-staff', 'lupon'])) {
            return $user->barangay_id === $complaint->barangay_id;
        }
        
        // Complainant can view their own complaint
        if ($user->id === $complaint->complainant_id) {
            return true;
        }
        
        // Respondents can view complaints they're involved in
        if ($complaint->respondents) {
            foreach ($complaint->respondents as $respondent) {
                if (isset($respondent['user_id']) && $respondent['user_id'] == $user->id) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Determine if user can update complaint
     */
    public function update(User $user, Complaint $complaint): bool
    {
        // Only barangay staff of the same barangay can update
        if ($user->hasAnyRole(['barangay-captain', 'barangay-secretary', 'barangay-staff', 'lupon'])) {
            return $user->barangay_id === $complaint->barangay_id;
        }
        
        return false;
    }

    /**
     * Determine if user can delete complaint
     */
    public function delete(User $user, Complaint $complaint): bool
    {
        // Only captain can delete
        if ($user->hasRole('barangay-captain')) {
            return $user->barangay_id === $complaint->barangay_id;
        }
        
        return false;
    }
}