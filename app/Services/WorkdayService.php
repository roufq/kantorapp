<?php

namespace App\Services;

use App\Models\EmployeeLeave;
use App\Models\Holiday;
use App\Models\WeeklyOff;
use App\Models\User;
use Carbon\CarbonInterface;

class WorkdayService
{
    public static function isWeeklyOff(User $user, CarbonInterface $date): bool
    {
        $dow = strtolower($date->format('l')); // e.g., monday
        // Personal weekly off
        $hasPersonal = WeeklyOff::where('user_id', $user->id)->where('day_of_week', $dow)->exists();
        if ($hasPersonal) return true;
        // Location weekly off
        if ($user->location_id) {
            return WeeklyOff::whereNull('user_id')->where('location_id', $user->location_id)->where('day_of_week', $dow)->exists();
        }
        return false;
    }

    public static function isHolidayForUser(User $user, CarbonInterface $date): bool
    {
        $q = Holiday::active()->whereDate('date', $date->toDateString());
        // national
        $national = (clone $q)->where('is_national', true)->exists();
        if ($national) return true;
        // location-specific
        if ($user->location_id) {
            return Holiday::active()->where('is_national', false)->where('location_id', $user->location_id)->whereDate('date', $date->toDateString())->exists();
        }
        return false;
    }

    public static function hasApprovedLeave(User $user, CarbonInterface $date): bool
    {
        return EmployeeLeave::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $date->toDateString())
            ->whereDate('end_date', '>=', $date->toDateString())
            ->exists();
    }

    public static function isWorkingDay(User $user, CarbonInterface $date): bool
    {
        if (self::isWeeklyOff($user, $date)) return false;
        if (self::isHolidayForUser($user, $date)) return false;
        if (self::hasApprovedLeave($user, $date)) return false;
        return true;
    }
}

