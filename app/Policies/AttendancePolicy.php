<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttendancePolicy
{
    /**
     * Grant all permissions to Super Admin.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     * Employee can view their own attendance list.
     * Location Admin can view attendance list of their location.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Employee') || $user->hasRole('Location Admin') || $user->hasRole('HR');
    }

    /**
     * Determine whether the user can view the model.
     * Employee can view their own attendance.
     * Location Admin can view attendance of their location.
     */
    public function view(User $user, Attendance $attendance): bool
    {
        if ($user->hasRole('Employee')) {
            return $user->id === $attendance->user_id;
        }

        if ($user->hasRole('Location Admin')) {
            return $user->location_id === $attendance->location_id;
        }

        if ($user->hasRole('HR')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Attendance is created via clock-in/out, not direct creation.
        return false;
    }

    /**
     * Determine whether the user can update the model.
     * Only Location Admin can update attendance (e.g., for approval).
     */
    public function update(User $user, Attendance $attendance): bool
    {
        if ($user->hasRole('Location Admin')) {
            return $user->location_id === $attendance->location_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attendance $attendance): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Attendance $attendance): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Attendance $attendance): bool
    {
        return false;
    }
}
