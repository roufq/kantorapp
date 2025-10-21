<?php

namespace App\Policies;

use App\Models\LocationChangeRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LocationChangeRequestPolicy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LocationChangeRequest $locationChangeRequest): bool
    {
        return $user->id === $locationChangeRequest->user_id || $user->location_id === $locationChangeRequest->original_location_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Karyawan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LocationChangeRequest $locationChangeRequest): bool
    {
        return $user->hasRole('Admin Lokasi') && $user->location_id === $locationChangeRequest->original_location_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LocationChangeRequest $locationChangeRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LocationChangeRequest $locationChangeRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LocationChangeRequest $locationChangeRequest): bool
    {
        return false;
    }
}