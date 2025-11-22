<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EmployeePolicy
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
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin Lokasi');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Employee $employee): bool
    {
        // Admin Lokasi can view any employee in their own location.
        if ($user->hasRole('Admin Lokasi')) {
            return $user->location_id === $employee->location_id;
        }

        // Karyawan can only view their own employee data.
        if ($user->hasRole('Karyawan')) {
            return $user->employee_id === $employee->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Admin Lokasi');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Employee $employee): bool
    {
        // Admin Lokasi can update any employee in their own location.
        if ($user->hasRole('Admin Lokasi')) {
            return $user->location_id === $employee->location_id;
        }

        // Karyawan can only update their own employee data.
        if ($user->hasRole('Karyawan')) {
            return $user->employee_id === $employee->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Employee $employee): bool
    {
        // Admin Lokasi can delete any employee in their own location.
        if ($user->hasRole('Admin Lokasi')) {
            return $user->location_id === $employee->location_id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Employee $employee): bool
    {
        if ($user->hasRole('Admin Lokasi')) {
            return $user->location_id === $employee->location_id;
        }
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Employee $employee): bool
    {
        if ($user->hasRole('Admin Lokasi')) {
            return $user->location_id === $employee->location_id;
        }
        return false;
    }
}