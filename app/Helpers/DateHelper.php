<?php
// FILE: app/Helpers/DateHelper.php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Calculate months between two dates (with proper error handling)
     */
    public static function calculateMonthsBetween($startDate, $endDate = null): int
    {
        if (!$startDate) {
            return 0;
        }

        try {
            $start = Carbon::parse($startDate);
            $end = $endDate ? Carbon::parse($endDate) : now();

            // If start date is in the future, return 0
            if ($start->isFuture()) {
                return 0;
            }

            return (int) $start->diffInMonths($end);
        } catch (\Exception $e) {
            \Log::error('Date calculation error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Check if residency requirement is met (6 months)
     */
    public static function meetsResidencyRequirement($residencySince): bool
    {
        return self::calculateMonthsBetween($residencySince) >= 6;
    }

    /**
     * Calculate remaining months needed for residency requirement
     */
    public static function getRemainingMonths($residencySince): int
    {
        $monthsResided = self::calculateMonthsBetween($residencySince);
        return max(0, 6 - $monthsResided);
    }

    /**
     * Calculate eligible date for residency requirement
     */
    public static function getEligibleDate($residencySince): ?Carbon
    {
        if (!$residencySince) {
            return null;
        }

        try {
            $residencyDate = Carbon::parse($residencySince);
            
            // If residency date is in the future, eligible date is 6 months from now
            if ($residencyDate->isFuture()) {
                return now()->addMonths(6);
            }
            
            // Otherwise, eligible date is 6 months from residency start
            return $residencyDate->copy()->addMonths(6);
        } catch (\Exception $e) {
            \Log::error('Eligible date calculation error: ' . $e->getMessage());
            return null;
        }
    }
}